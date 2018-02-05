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


use SnootBeest\Tantrum\Route\ConstructorProxyFactoryProvider;
use SnootBeest\Tantrum\Route\ParameterProxyFactory;
use SnootBeest\Tantrum\Route\ConstructorProxyFactory;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Route\ConstructorProxyFactoryProvider
 */
class ConstructorProxyFactoryProviderTest extends RouteTestCase
{
    /**
     * @test
     * @covers ::getKey
     */
    public function getKeySucceeds()
    {
        self::assertEquals(ConstructorProxyFactoryProvider::KEY, ConstructorProxyFactoryProvider::getKey());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function invokeSucceeds()
    {
        $parameterProxyFactory = $this->mock(ParameterProxyFactory::class);
        $constructorProxyFactoryProvider = new ConstructorProxyFactoryProvider($parameterProxyFactory);
        $constructorProxyFactory = $constructorProxyFactoryProvider();

        self::assertInstanceOf(ConstructorProxyFactory::class, $constructorProxyFactory);
    }
}
