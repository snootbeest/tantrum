# Tantrum

Tantrum is a RESTful API framework based on [Slim 3](https://www.slimframework.com/docs/) framework.  
It extends Slim 3 in a number of useful ways:

## #Configurable Services
Tantrum comes with a service layer based on configuration and dependency injection.  
To create services, all that is needed is a class that implements the `SnootBeest\Tantrum\Service\ServiceProvider` 
interface, and a few lines of configuration.  

```$yaml
dependencies:
  Psr\Log\LoggerInterface:
    providerClass: MyProject\Namespace\LoggerProvider
    dependencyType: default
    dependencies:
      - Doctrine\ORM\EntityManagerInterface
  ...
  Doctrine\ORM\EntityManagerInterface
    providerClass: MyProject\Namespace\EntityManagerProvider
  ...
configuration:
  Doctrine\ORM\EntityManagerInterface
     host: mysql
     port: 3306
     user: root
     password:
  ...
```
Here we see a section of the configuration detailing the dependencies(services).   
This particular service provides a `LoggerInterface` instance. `Psr\Log\LoggerInterface` is the key that will be used inside 
the dependency injection container.  
Using fully qualified interface namespaces as container keys - whilst not required -
provides several benefits:
 * Removes ambiguity - You know exactly what will be returned
 * Avoids naming collisions - Namespaces have to be unique
 * Helps with constructor injection - matching typehinted constructor arguments with container keys is super cool.

#### providerClass
The `providerClass` is the `ServiceProvider` which will be invoked from the dependency injection container, and 
which will ultimately return the `LoggerInterface` instance. It contains all the logic needed for instanciation.


#### dependencyType
The `dependencyType` key is optional, and can have three possible values:
 * factory: maps to the [factory](https://pimple.symfony.com/#defining-factory-services) method of [Pimple](https://pimple.symfony.com/). 
 A fresh instance will be returned on every invocation.
 * protect: maps -predictably- to the [protect](https://pimple.symfony.com/#protecting-parameters) method of [Pimple](https://pimple.symfony.com/). Useful if you need to return an anonymous function.
 * singleton: This is the default behaviour of Pimple, and you needn't include the dependencyType parameter if this is your 
 intended behaviour.
  
#### dependencies
The dependency injection container is not available to the `ServiceProvider` to avoid the global service locator anti-pattern.
Therefore we define a list of sub dependencies here (fully namespaced interfaces, hopefully!). These are injected into the 
`ServiceProvider` constructor in the order in which they are defined (no reflection here). There they can be made available to the service itself.  
This means that they **must** be defined elsewhere in the application dependencies to be available in the container.  

#### configuration
In the example above, the ORM will obviously need a database connection, the details of which are not provided to it here.  
In a different part of the config we define the configuration values for the dependencies, keyed once again by the interface.  
> Why not define the configuration values alongside the dependencies?  

The dependencies are generally far less dynamic than the configuration of the application. For example, your application
will always need it's `LoggerInterface` implementation, but the database will be different for every environment. In addition
configuration values may be shared and modified between many different services, and it is best that these are defined elsewhere.

## Route Inspection
One of the best things about Slim 3 is the ability to quickly add routes to the application.
This is excellent for rapid development, but I have found that adding routes using closures is that in large applications
they soon become unwieldy. In addition it is difficult to unit test closures.

Tantrum brings route inspection during the build process (I always create a build script which runs after `composer install`)
This technique reflects the routes at build time and caches the results, ready for runtime.
Having the routes pre-processed and stored in this way allows for constructor injection, making the controllers easy to
test, but also gives us the opportunity to pass named query parameters directly into the route method.

The controllers are configured under the key `controllers`, and are simply a list of namespaces:
```$yaml
...
controllers:
  - Acme\Shop\Controllers\WidgetController
  - Acme\Shop\Controllers\UserController
  ...
```

All that it asks is that your controllers extend a small class; `SnootBeest\Tantrum\Controller`, and that your controller
classes include some simple annotations to help the router. Let's look at an example:

```
<?php
namespace Acme\Shop;

use Snootbeest\Tantrum\Controller;
use Psr\Log\LoggerInterface;

class Widget extends Controller
{
    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * Widget controller constructor
    */
    public function __constructor(LoggerInterface $logger, int $requiredParameter, string $optionalParameter = 'some string')
    {
        $this->logger = $logger;
    }

    /**
     * Gets a widget from the given widget id
     *
     * @httpMethod GET
     * @httpMethod HEAD
     * @pattern /widget/{:widgetId}
    */
    public function getOneWidget(int $widgetId)
    {
        // Get and return the widget
        $this->logger->debug(sprintf('Returning widget #%d', $widgetId));
    }

    ...
}
```

Here is the widget controller for our shop.

#### Constructor injection
Note that our `__construct` method has a few dependencies.
You can see that it requires a `Psr\Log\LoggerInterface` instance as the first parameter. This is configured using the
[process above](#Configurable-Services), and automatically provided from the dependency injection
container.

The second parameter `$requiredParameter` is also required, but not type-hinted. The dispatcher will attempt to fetch this
from the container too. If it is not found there, it will be fetched from config. If it is still not found, an exception
will be thrown, resulting in a `500` response.

The third parameter `$optionalParameter` has a default value. If its name isn't found in the container or config, the
default value will be returned instead.

We can also see some custom annotations in the route declaration.

#### @httpMethod
Multiple @httpMethod annotations can be provided if necessary.

#### @route
The @route annotation is the regex that the slim router needs to resolve the request to this method. Any named placeholders
are passed in as method parameters. See [https://www.slimframework.com/docs/objects/router.html#route-placeholders](https://www.slimframework.com/docs/objects/router.html#route-placeholders)

Of course, you can still add routes in the methods described in the Slim 3 documentation if you need to take advantage
of its more advanced features. I'll try to add more of these to the route detection as time goes on.
