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

use phpDocumentor\Reflection\DocBlockFactoryInterface;

/**
 * Class MethodProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class MethodProxyFactory implements MethodProxyFactoryInterface
{
    /** @var DocBlockFactoryInterface $docBlockFactory */
    private $docBlockFactory;

    /**
     * MethodProxyFactory constructor.
     * @param DocBlockFactoryInterface $docBlockFactory
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * Create and return a new method proxy
     * @param \ReflectionMethod $reflectionMethod
     * @return MethodProxy
     */
    public function create(\ReflectionMethod $reflectionMethod): MethodProxy
    {
        return new MethodProxy($reflectionMethod->getName(), $this->docBlockFactory->create($reflectionMethod->getDocComment()));
    }
}
