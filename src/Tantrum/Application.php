<?php
/**
 * This file is part of tantrum.
 *
 *  tantrum is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  tantrum is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with tantrum.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SnootBeest\Tantrum;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Container;
use SnootBeest\Tantrum\Exception\BootstrapException;
use SnootBeest\Tantrum\Exception\BuildException;
use SnootBeest\Tantrum\Route\ControllerProxy;
use SnootBeest\Tantrum\Route\ControllerProxyFactoryInterface;
use SnootBeest\Tantrum\Service\CacheProvider;
use SnootBeest\Tantrum\Service\ServiceProviderInterface;
use Noodlehaus\ConfigInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class Application extends App
{
    /** Slim settings. @see https://www.slimframework.com/docs/objects/application.html#slim-default-settings */
    const CONFIG_KEY_APPLICATION     = 'application';
    const CONFIG_KEY_CONFIGURATION   = 'configuration';
    const CONFIG_KEY_DEPENDENCIES    = 'dependencies';
    const CONFIG_KEY_DEPENDENCY_TYPE = 'dependencyType';
    const CONFIG_KEY_PROVIDER_CLASS  = 'providerClass';
    const CONFIG_KEY_CONTROLLERS     = 'controllers';

    /** @see https://pimple.symfony.com/#defining-factory-services */
    const DEPENDENCY_TYPE_FACTORY   = 'factory';
    /** @see https://pimple.symfony.com/#protecting-parameters */
    const DEPENDENCY_TYPE_PROTECT   = 'protect';
    const DEPENDENCY_TYPE_SINGLETON = 'singleton';

    const ROUTES_CACHE_KEY            = 'SnootBeest_Tantrum_Routes';

    /** @var ConfigInterface $config */
    private $config;

    /**
     * These are the keys in the config object which mean something special to Tantrum
     * @var array
     */
    public static $protectedConfigKeys = [
        self::CONFIG_KEY_APPLICATION,
        self::CONFIG_KEY_CONFIGURATION,
        self::CONFIG_KEY_DEPENDENCIES,
        self::CONFIG_KEY_DEPENDENCY_TYPE,
        self::CONFIG_KEY_PROVIDER_CLASS,
    ];

    /**
     * Dependency types map directly to methods on Pimple\Container
     * @var array
     */
    public static $dependencyTypes = [
        self::DEPENDENCY_TYPE_FACTORY,
        self::DEPENDENCY_TYPE_PROTECT,
        self::DEPENDENCY_TYPE_SINGLETON,
    ];

    /**
     * Initialize the container
     * @param ConfigInterface $config
     * @param CacheInterface  $cache
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $container    = $this->initContainer();
        parent::__construct($container);
    }

    /**
     * {@inheritdoc}
     * Runs the application using the routes saved during the build process
     * @param bool $silent - Determines whether a response is returned
     * @return void | ResponseInterface
     */
    public function run($silent = false)
    {
        $this->initRoutes();
        $response = parent::run($silent);

        if($silent === false) {
            return $response;
        }
    }

    /**
     * Extract the dependencies from the config and add them to the container
     * @return Container
     */
    private function initContainer(): Container
    {
        $configuration = array_merge(include('defaultConfiguration.php'), $this->config->get(self::CONFIG_KEY_CONFIGURATION, []));
        $container = new Container($configuration['configuration']);
        $this->initDependencies($this->config, $container, $configuration);

        return $container;
    }

    /**
     * Inject the dependencies into the container
     * @param ConfigInterface $config
     * @param Container $container
     * @param array $configuration
     * @throws BootstrapException
     */
    private function initDependencies(ConfigInterface $config, Container $container, array $configuration = [])
    {
        $dependencies = array_merge(include('defaultDependencies.php'), $config->get(self::CONFIG_KEY_DEPENDENCIES, []));
        foreach($dependencies as $key => $detail) {
            $dependencyConfig = array_key_exists($key, $configuration) === true ? new Config($configuration[$key]): new Config([]);
            $this->addDependency($container, $key, $detail, $dependencyConfig);
        }
    }

    /**
     * Add the given className as a dependency to the container including it's dependencies
     * @param Container $container
     * @param string $key
     * @param array $detail
     * @param Config $config
     * @throws BootstrapException
     */
    private function addDependency(Container $container, string $key, array $detail, Config $config)
    {
        if(array_key_exists(self::CONFIG_KEY_PROVIDER_CLASS, $detail) === false) {
            throw new BootstrapException(sprintf('No providerClass found for "%s": %s => %s', $key, $key, print_r($detail, 1)));
        }

        $providerClass = $detail[self::CONFIG_KEY_PROVIDER_CLASS];
        $dependencyType = array_key_exists(self::CONFIG_KEY_DEPENDENCY_TYPE, $detail) ? $detail[self::CONFIG_KEY_DEPENDENCY_TYPE] : null;
        $dependencies = array_key_exists(self::CONFIG_KEY_DEPENDENCIES, $detail) ? $detail[self::CONFIG_KEY_DEPENDENCIES] : [];

        $service = $this->createService($providerClass, $dependencies, $config);
        switch($dependencyType) {
            case self::DEPENDENCY_TYPE_FACTORY:
                $container[$key] = $container->factory($service);
                break;
            case self::DEPENDENCY_TYPE_PROTECT:
                $container[$key] = $container->protect($service);
                break;
            case self::DEPENDENCY_TYPE_SINGLETON:
            case null:
                $container[$key] = $service;
                break;
            default:
                throw new BootstrapException(sprintf('Unhandled dependency type "%s"', $dependencyType));
        }
    }

    /**
     * Create the service closure
     * @param string $providerClass
     * @param array $dependencies
     * @param Config $config
     * @return \Closure
     */
    private function createService(string $providerClass, array $dependencies, Config $config): \Closure
    {
        return function(Container $container) use ($providerClass, $dependencies, $config) {
            $args = [];
            foreach($dependencies as $dependency) {
                $args[] = $container->get($dependency);
            }

            /** @var ServiceProviderInterface $provider */
            $provider = new $providerClass(...$args);
            if($provider instanceof ServiceProviderInterface === false) {
                throw new BootstrapException(sprintf('"%s" is not an instance of %s', $providerClass, ServiceProviderInterface::class));
            }
            $provider->setConfig($config);
            return $provider();
        };
    }

    /**
     * Builds and caches the route proxies
     * @throws BuildException
     * @return bool
     */
    public function build(): bool
    {
        if ($this->config->has(self::CONFIG_KEY_CONTROLLERS) === false) {
            throw new BuildException('No controllers found in config');
        }

        $namespaces = $this->config->get(self::CONFIG_KEY_CONTROLLERS);
        if (is_array($namespaces) === false) {
            throw new BuildException('Controllers must be an array');
        }

        $container = $this->getContainer();
        /** @var ControllerProxyFactoryInterface $controllerProxyFactory */
        $controllerProxyFactory = $container->get(ControllerProxyFactoryInterface::class);

        $routes = [];
        foreach($namespaces as $namespace) {
            try {
                $controllerProxy = $controllerProxyFactory->create($namespace);
                $routes[$controllerProxy->getClassName()] = $controllerProxy;
            } catch(\Exception $ex) {
                $container->get(LoggerInterface::class)->error($ex->getMessage());
            }
        }

        if(count($routes) === 0) {
            throw new BuildException('No routes added');
        }

        /** @var CacheItem $routeCacheItem */
        $cache = $container->get(CacheItemPoolInterface::class);
        $routeCacheItem = $cache->getItem(self::ROUTES_CACHE_KEY);
        $routeCacheItem->set($routes);
        return $cache->save($routeCacheItem);
    }

    /**
     * Fetch the routes from the cache and add them to the application
     */
    private function initRoutes()
    {
        /** @var Container $container */
        $container = $this->getContainer();
        /** @var CacheItemPoolInterface $cache */
        $cache = $container->get(CacheProvider::getKey());

        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $cache->getItem(self::ROUTES_CACHE_KEY);
        if($cacheItem->isHit() === true) {
            $controllerProxies = $cacheItem->get();
        } else {
            $controllerProxies = [];
        }

        /** @var ControllerProxy $controllerProxy */
        foreach($controllerProxies as $controllerProxy) {
            $this->processControllerProxy($this, $controllerProxy);
            $container[$controllerProxy->getClassName()] = $container->factory($controllerProxy);
        }
    }

    /**
     * Add the route methods from the controller proxy to the application
     * @param Application     $app
     * @param ControllerProxy $controllerProxy
     */
    private function processControllerProxy(Application $app, ControllerProxy $controllerProxy)
    {
        $className = $controllerProxy->getClassName();
        foreach ($controllerProxy->getMethods() as $methodProxy) {
            $routeKey = sprintf('%s:%s', $className, $methodProxy->getName());
            $route = sprintf('/api%s', $methodProxy->getRoute());
            $httpMethods = $methodProxy->getMethods();
            foreach($httpMethods as $httpMethod) {
                $app->$httpMethod($route, $routeKey)
                    ->setName($routeKey);
            }
        }
    }
}
