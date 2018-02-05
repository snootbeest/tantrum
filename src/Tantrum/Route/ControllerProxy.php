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

namespace SnootBeest\Tantrum\Route;

use Psr\Log\LoggerInterface;
use Slim\Container;
use SnootBeest\Tantrum\Controller;

/**
 * Class ControllerProxy
 * @package SnootBeest\Tantrum\Route
 */
class ControllerProxy implements ControllerProxyInterface
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var string $className */
    private $className;

    /** @var MethodProxy[]  */
    private $methods = [];

    /** @var ConstructorProxy */
    private $constructor;

    /**
     * ControllerProxy constructor.
     * @param LoggerInterface $logger
     * @param ConstructorProxyFactoryInterface $constructorProxyFactory
     * @param MethodProxyFactoryInterface $methodProxyFactory
     * @param \ReflectionClass $reflectionClass
     */
    public function __construct(LoggerInterface $logger, ConstructorProxyFactoryInterface $constructorProxyFactory,
                                MethodProxyFactoryInterface $methodProxyFactory, \ReflectionClass $reflectionClass)
    {
        $this->logger      = $logger;
        $this->className   = $reflectionClass->getName();
        $this->methods     = $this->getMethodProxies($methodProxyFactory, $reflectionClass);
        $this->constructor = $constructorProxyFactory->create($reflectionClass);
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     * @return MethodProxy[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     * @return ConstructorProxyInterface
     */
    public function getConstructor(): ConstructorProxyInterface
    {
        return $this->constructor;
    }

    /**
     * Get a method proxy for each public method
     * @param MethodProxyFactoryInterface $methodProxyFactory
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    private function getMethodProxies(MethodProxyFactoryInterface $methodProxyFactory, \ReflectionClass $reflectionClass): array
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
     * {@inheritdoc}
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            'className'        => $this->className,
            'methods'          => $this->methods,
            'constructor'      => $this->constructor,
        ]);
    }
    
    /**
     * {@inheritdoc}
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->className        = $data['className'];
        $this->methods          = $data['methods'];
        $this->constructor      = $data['constructor'];
    }

    /**
     * {@inheritdoc}
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
