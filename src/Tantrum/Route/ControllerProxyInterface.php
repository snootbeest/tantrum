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

interface ControllerProxyInterface extends \Serializable
{
    /**
     * ControllerProxy constructor.
     * @param LoggerInterface $logger
     * @param ConstructorProxyFactoryInterface $constructorProxyFactory
     * @param MethodProxyFactoryInterface $methodProxyFactory
     * @param \ReflectionClass $reflectionClass
     */
    public function __construct(LoggerInterface $logger, ConstructorProxyFactoryInterface $constructorProxyFactory,
                                MethodProxyFactoryInterface $methodProxyFactory, \ReflectionClass $reflectionClass);

    /**
     * Returns the class name of the controller
     * @return string
     */
    public function getClassName(): string;

    /**
     * Returns an array of MethodProxy objects corresponding to the routes in the controller
     * @return MethodProxy[]
     */
    public function getMethods(): array;

    /**
     * Returns a ConstructorProxy object representing the controller constructor
     * @return ConstructorProxyInterface
     */
    public function getConstructor(): ConstructorProxyInterface;

    /**
     * {@inheritdoc}
     * @return string
     */
    public function serialize(): string;

    /**
     * {@inheritdoc}
     * @param string $serialized
     */
    public function unserialize($serialized);

    /**
     * Called by the app when the route is initialized
     * Returns the Controller object
     * @param Container $container
     * @return Controller
     */
    public function __invoke(Container $container): Controller;
}
