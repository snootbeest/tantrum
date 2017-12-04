# Tantrum

Tantrum is a RESTful API framework based on [Slim 3](https://www.slimframework.com/docs/) framework.  
It extends Slim 3 in a number of useful ways:

## Configurable Services
Tantrum comes with a service layer based on configuration and dependency injection.  
To create services, all that is needed is a class that implements the `SnootBeest\Tantrum\Service\ServiceProvider` 
interface, and a few lines of configuration.  

```yaml
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
  MyProject\Namespace\EntityManagerProvider
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
