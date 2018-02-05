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

interface ControllerProxyFactoryInterface
{
    /**
     * Provision the dependencies.
     * @param LoggerInterface $logger
     * @param ReflectionFactoryInterface $reflectionFactory
     * @param ConstructorProxyFactoryInterface $constructorProxyFactory
     * @param MethodProxyFactoryInterface $methodProxyFactory
     */
    public function __construct(LoggerInterface $logger, ReflectionFactoryInterface $reflectionFactory,
                                ConstructorProxyFactoryInterface $constructorProxyFactory, MethodProxyFactoryInterface $methodProxyFactory);

    /**
     * Create and return a controller proxy populated with a reflection of the controller
     * @param $namespace
     * @throws \Exception
     * @return ControllerProxyInterface
     */
    public function create($namespace): ControllerProxyInterface;
}
