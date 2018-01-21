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

/**
 * Class ConstructorProxy
 * @package SnootBeest\Tantrum\Route
 */
class ConstructorProxy implements ConstructorProxyInterface
{
    /** @var ParameterProxy[] $params */
    private $params = [];

    /**
     * ConstructorProxy constructor.
     * @param ParameterProxyFactoryInterface $parameterProxyFactory
     * @param \ReflectionMethod $reflectionMethod
     */
    public function __construct(ParameterProxyFactoryInterface $parameterProxyFactory, \ReflectionMethod $reflectionMethod = null)
    {
        $this->params = $this->getConstructorArguments($parameterProxyFactory, $reflectionMethod);
    }

    /**
     * @return ParameterProxy[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get the constructor arguments for the route for dependency injection
     * @param ParameterProxyFactoryInterface $parameterProxyFactory
     * @param \ReflectionMethod $constructor
     * @return array
     */
    private function getConstructorArguments(ParameterProxyFactoryInterface $parameterProxyFactory, \ReflectionMethod $constructor = null): array
    {
        $params = [];
        if(!is_null($constructor)) {
            $reflectionParameters = $constructor->getParameters();
            foreach($reflectionParameters as $reflectionParameter) {
                $params[] = $parameterProxyFactory->create($reflectionParameter);
            }
        }

        return $params;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize()
    {
        return serialize($this->params);
    }

    /**
     * @inheritdoc
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }
}
