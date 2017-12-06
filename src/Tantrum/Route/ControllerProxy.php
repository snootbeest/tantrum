<?php
namespace SnootBeest\Tantrum\Route;

use Psr\Log\LoggerInterface;
use Slim\Container;

/**
 * Class ControllerProxy
 * @package SnootBeest\Tantrum\Route
 */
class ControllerProxy implements \Serializable
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var string $className */
    private $className;

    /** @var int $version */
    private $version;

    /** @var MethodProxy[]  */
    private $methods = [];

    /** @var ConstructorProxy */
    private $constructor;

    /**
     * ControllerProxy constructor.
     * @param  LoggerInterface $logger
     * @param ConstructorProxyFactory $constructorProxyFactory
     * @param  MethodProxyFactory $methodProxyFactory
     * @param \ReflectionClass $reflectionClass
     */
    public function __construct(LoggerInterface $logger, ConstructorProxyFactory $constructorProxyFactory, MethodProxyFactory $methodProxyFactory, \ReflectionClass $reflectionClass)
    {
        $this->logger      = $logger;
        $this->className   = $reflectionClass->getName();
        $this->methods     = $this->getMethodProxies($methodProxyFactory, $reflectionClass);
        $this->constructor = $constructorProxyFactory->create($reflectionClass);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return MethodProxy[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return ConstructorProxy
     */
    public function getConstructor(): ConstructorProxy
    {
        return $this->constructor;
    }

    /**
     * Get a method proxy for each public method
     * @param MethodProxyFactory $methodProxyFactory
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getMethodProxies(MethodProxyFactory $methodProxyFactory, \ReflectionClass $reflectionClass): array
    {
        $methods = [];
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach($reflectionMethods as $reflectionMethod) {
            /*
             * Make sure the method belongs to this class and not an ancestor
             * Make sure this is not the class constructor
             */
            if ($reflectionMethod->class === $reflectionClass->getName() && $reflectionMethod->isConstructor() !== true) {
                try {
                    $methodProxy = $methodProxyFactory->create($reflectionMethod);
                    $methods[$methodProxy->getName()] = $methodProxy;
                } catch (\Exception $ex) {
                    $this->logger->debug(sprintf("%s::%s\" was not added to the router (%s)\n", $this->className, $reflectionMethod->getName(), $ex->getMessage()));
                }
            }
        }

        return $methods;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            'className'        => $this->className,
            'version'          => $this->version,
            'methods'          => $this->methods,
            'constructor'      => $this->constructor,
        ]);
    }
    
    /**
     * @inheritdoc
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->className        = $data['className'];
        $this->version          = $data['version'];
        $this->methods          = $data['methods'];
        $this->constructor      = $data['constructor'];
    }

    /**
     * Called by the app when the route is initialized
     * @param Container $container
     * @return Controller
     */
    public function __invoke(Container $container): Controller
    {
        $constructorParams = $this->constructor->getParams();
        $args = [];
        foreach ($constructorParams as $constructorParam) {
            if (!$constructorParam->hasDefault() || $container->has($constructorParam->getKey())) {
                $args[] = $container->get($constructorParam->getKey());
            } else {
                $args[] = $constructorParam->getDefaultValue();
            }
        }

        /** @var Controller $controller */
        $controller = new $this->className(...$args);

        return $controller->setRequest($container->get('request'))
            ->setResponse($container->get('response'));
    }
}