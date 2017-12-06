<?php
namespace SnootBeest\Tantrum\Route;

use Psr\Log\LoggerInterface;

/**
 * Class ControllerProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class ControllerProxyFactory
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var ReflectionFactory $reflectionFactory */
    private $reflectionFactory;

    /** @var ConstructorProxyFactory $constructorProxyFactory */
    private $constructorProxyFactory;

    /** @var MethodProxyFactory $methodProxyFactory */
    private $methodProxyFactory;

    /**
     * ControllerProxyFactory constructor.
     * @param LoggerInterface $logger
     * @param ConstructorProxyFactory $constructorProxyFactory
     * @param MethodProxyFactory $methodProxyFactory
     */
    public function __construct(LoggerInterface $logger, ReflectionFactory $reflectionFactory, ConstructorProxyFactory $constructorProxyFactory, MethodProxyFactory $methodProxyFactory)
    {
        $this->logger                  = $logger;
        $this->reflectionFactory       = $reflectionFactory;
        $this->constructorProxyFactory = $constructorProxyFactory;
        $this->methodProxyFactory      = $methodProxyFactory;
    }

    /**
     * Create and return a controller proxy populated with a reflection of the controller
     * @param $namespace
     * @return ControllerProxy
     * @throws \Exception
     */
    public function create($namespace)
    {
        $reflectionClass   = $this->reflectionFactory->create($namespace);
        $isInstantiable    = $reflectionClass->isInstantiable();
        $isRouteController = $reflectionClass->isSubclassOf('WestwingNow\Search\Route\Controller');
        // Make sure that this is a controller class, and is instantiable
        if($isInstantiable !== true || $isRouteController !== true) {
            throw new \Exception(sprintf('Ignoring %s; isInstantiable:%s isRouteController:%s', $namespace, $isInstantiable ? 'true' : 'false', $isRouteController ? 'true' : 'false'));
        }

        return new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $reflectionClass);
    }
}