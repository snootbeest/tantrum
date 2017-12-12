<?php

namespace SnootBeest\Tantrum;

use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Container;
use SnootBeest\Tantrum\Exception\BootstrapException;
use SnootBeest\Tantrum\Service\LoggerProvider;
use SnootBeest\Tantrum\Service\ServiceProviderInterface;
use Noodlehaus\ConfigInterface;
use SnootBeest\Tantrum\Core\Config;

class Application extends App
{
    const CONFIG_KEY_APPLICATION     = 'application';
    const CONFIG_KEY_CONFIGURATION   = 'configuration';
    const CONFIG_KEY_DEPENDENCIES    = 'dependencies';
    const CONFIG_KEY_DEPENDENCY_TYPE = 'dependencyType';
    const CONFIG_KEY_PROVIDER_CLASS  = 'providerClass';

    /** @see https://pimple.symfony.com/#defining-factory-services */
    const DEPENDENCY_TYPE_FACTORY   = 'factory';
    /** @see https://pimple.symfony.com/#protecting-parameters */
    const DEPENDENCY_TYPE_PROTECT   = 'protect';
    const DEPENDENCY_TYPE_SINGLETON = 'singleton';

    /**
     * These are the keys in the config object which mean something special to Tantrum
     * @var array
     */
    public static $protectedConfigKeys = [
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

    protected static $defaultDependencies = [
        LoggerInterface::class => [
            self::CONFIG_KEY_PROVIDER_CLASS  => LoggerProvider::class,
            self::CONFIG_KEY_DEPENDENCY_TYPE => self::DEPENDENCY_TYPE_SINGLETON,
        ],
    ];

    /**
     * Initialize the container
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $container = $this->initContainer($config);
        parent::__construct($container);
    }

    /**
     * Extract the dependencies from the config and add them to the container
     * @param ConfigInterface $config
     * @return Container
     */
    private function initContainer(ConfigInterface $config): Container
    {
        $configuration = $config->has(self::CONFIG_KEY_CONFIGURATION) ? $config->get(self::CONFIG_KEY_CONFIGURATION) : [];
        $container = new Container();
        $this->initDependencies($config, $container, $configuration);

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
        $dependencies = array_merge(self::$defaultDependencies, $config->get(self::CONFIG_KEY_DEPENDENCIES, []));
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

        $service = $this->createService($container, $providerClass, $dependencies, $config);
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
     * @param Container $container
     * @param string $providerClass
     * @param array $dependencies
     * @param Config $config
     * @return \Closure
     */
    private function createService(Container $container, string $providerClass, array $dependencies, Config $config): \Closure
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
}