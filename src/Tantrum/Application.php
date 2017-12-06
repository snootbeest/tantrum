<?php

namespace SnootBeest\Tantrum;

use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Container;
use SnootBeest\Tantrum\Exception\BootstrapException;
use SnootBeest\Tantrum\Route\ControllerProxy;
use SnootBeest\Tantrum\Service\ServiceProviderInterface;
use Noodlehaus\ConfigInterface;

class Application extends App
{
    /** Slim settings. @see https://www.slimframework.com/docs/objects/application.html#slim-default-settings */
    const CONFIG_KEY_APPLICATION     = 'application';
    const CONFIG_KEY_CONFIGURATION   = 'configuration';
    const CONFIG_KEY_DEPENDENCIES    = 'dependencies';
    const CONFIG_KEY_DEPENDENCY_TYPE = 'dependencyType';
    const CONFIG_KEY_PROVIDER_CLASS  = 'providerClass';
    const CONFIG_KEY_ROUTE_DIRECTORY = 'routeDir';

    /** @see https://pimple.symfony.com/#defining-factory-services */
    const DEPENDENCY_TYPE_FACTORY   = 'factory';
    /** @see https://pimple.symfony.com/#protecting-parameters */
    const DEPENDENCY_TYPE_PROTECT   = 'protect';
    const DEPENDENCY_TYPE_SINGLETON = 'singleton';

    /** @var string $cachePath - The relative path to the cache directory */
    private static $cachePath       = '../../build/cache/';
    /** @var string $routesFile - The filename of the compiled routes */
    private static $routesFile      = 'routes.bin';

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
        self::CONFIG_KEY_ROUTE_DIRECTORY,
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
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $container = $this->initContainer();
        parent::__construct($container);
    }

    /**
     * {@inheritdoc}
     * Runs the application using the routes saved during the build process
     * @param bool $silent - Determines whether a response is returned
     * @return null | ResponseInterface
     */
    public function run($silent = false): ResponseInterface
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
        $configuration = $this->config->has(self::CONFIG_KEY_CONFIGURATION) ? $this->config->get(self::CONFIG_KEY_CONFIGURATION) : [];
        $container = new Container();
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
        if($config->has(self::CONFIG_KEY_DEPENDENCIES) === false) {
            // @fixme: We should log this and carry on. It's possible that the implementor might not want to use this awesome feature
            throw new BootstrapException('No dependencies mapped');
        }

        $dependencies  = $config->get(self::CONFIG_KEY_DEPENDENCIES);
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
            throw new BootstrapException(sprintf('No providerClass found for "%s"', $key));
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
     * Fetch the routes from the cache and add them to the application
     */
    private function initRoutes()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $routesPath = realpath(self::$cachePath.self::$routesFile);
        if($routesPath === false) {
            // @todo: Debug 'Falling back to slim route init & caching'
            return false;
        }

        $routes = unserialize(file_get_contents($routesPath));

        /** @var ControllerProxy $controllerProxy */
        foreach($routes as $controllerProxy) {
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
            $httpMethod = $methodProxy->getMethod();
            $app->$httpMethod($route, $routeKey)
                ->setName($routeKey);
        }
    }
}