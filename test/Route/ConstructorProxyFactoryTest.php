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

namespace SnootBeest\Tantrum\Test\Route;

use SnootBeest\Tantrum\Route\ConstructorProxyFactory;
use SnootBeest\Tantrum\Route\ParameterProxy;
use SnootBeest\Tantrum\Route\ParameterProxyFactory;
use SnootBeest\Tantrum\Route\ConstructorProxy;

class ConstructorProxyFactoryTest extends RouteTestCase
{
    /**
     * @test
     */
    public function createSucceeds()
    {
        $mockReflectionParameter = $this->mock(\ReflectionParameter::class);

        $reflectionParameters = [
            $mockReflectionParameter,
        ];

        $mockParameterProxy = $this->mock(ParameterProxy::class);

        $mockParameterProxyFactory = $this->mock(ParameterProxyFactory::class);
        $mockParameterProxyFactory->shouldReceive('create')
            ->once()
            ->with($mockReflectionParameter)
            ->andReturn($mockParameterProxy);

        $mockReflectionMethod = $this->mock(\ReflectionMethod::class);
        $mockReflectionMethod->shouldReceive('getParameters')
            ->once()
            ->andReturn($reflectionParameters);

        $mockReflectionClass = $this->mock(\ReflectionClass::class);
        $mockReflectionClass->shouldReceive('getConstructor')
            ->once()
            ->andReturn($mockReflectionMethod);

        $constructorProxyFactory = new ConstructorProxyFactory($mockParameterProxyFactory);
        $constructorProxy = $constructorProxyFactory->create($mockReflectionClass);

        self::assertInstanceOf(ConstructorProxy::class, $constructorProxy);
    }
}
