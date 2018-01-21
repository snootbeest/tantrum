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
use SnootBeest\Tantrum\Controller;

/**
 * Class ControllerProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class ControllerProxyFactory implements ControllerProxyFactoryInterface
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
     * {@inheritdoc}
     * @param LoggerInterface $logger
     * @param ReflectionFactoryInterface $reflectionFactory
     * @param ConstructorProxyFactoryInterface $constructorProxyFactory
     * @param MethodProxyFactoryInterface $methodProxyFactory
     */
    public function __construct(LoggerInterface $logger, ReflectionFactoryInterface $reflectionFactory,
                                ConstructorProxyFactoryInterface $constructorProxyFactory, MethodProxyFactoryInterface $methodProxyFactory)
    {
        $this->logger                  = $logger;
        $this->reflectionFactory       = $reflectionFactory;
        $this->constructorProxyFactory = $constructorProxyFactory;
        $this->methodProxyFactory      = $methodProxyFactory;
    }

    /**
     * {@inheritdoc}
     * @param $namespace
     * @return ControllerProxyInterface
     * @throws \Exception
     */
    public function create($namespace): ControllerProxyInterface
    {
        $reflectionClass   = $this->reflectionFactory->create($namespace);
        $isInstantiable    = $reflectionClass->isInstantiable();
        $isRouteController = $reflectionClass->isSubclassOf(Controller::class);
        // Make sure that this is a controller class, and is instantiable
        if($isInstantiable !== true || $isRouteController !== true) {
            throw new \Exception(sprintf('Ignoring %s; isInstantiable:%s isRouteController:%s', $namespace, $isInstantiable ? 'true' : 'false', $isRouteController ? 'true' : 'false'));
        }

        return new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $reflectionClass);
    }
}
