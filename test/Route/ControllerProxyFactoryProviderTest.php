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

use Monolog\Logger;
use SnootBeest\Tantrum\Controller;
use SnootBeest\Tantrum\Route\ConstructorProxy;
use SnootBeest\Tantrum\Route\ControllerProxyFactory;
use SnootBeest\Tantrum\Route\ControllerProxyFactoryProvider;
use SnootBeest\Tantrum\Route\ReflectionFactory;
use SnootBeest\Tantrum\Route\ConstructorProxyFactory;
use SnootBeest\Tantrum\Route\MethodProxyFactory;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Route\ControllerProxyFactoryProvider
 */
class ControllerProxyFactoryProviderTest extends RouteTestCase
{
    /**
     * @test
     * @covers ::getKey
     */
    public function getKeySucceeds()
    {
        self::assertEquals(ControllerProxyFactoryProvider::KEY, ControllerProxyFactoryProvider::getKey());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function invokeSucceeds()
    {
        $logger                  = $this->mock(Logger::class);
        $reflectionFactory       = $this->mock(ReflectionFactory::class);
        $constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);
        $methodProxyFactory      = $this->mock(MethodProxyFactory::class);

        $controllerProxyFactoryProvider = new ControllerProxyFactoryProvider($logger, $reflectionFactory, $constructorProxyFactory, $methodProxyFactory);
        $controllerProxyFactory = $controllerProxyFactoryProvider();

        self::assertInstanceOf(ControllerProxyFactory::class, $controllerProxyFactory);
    }
}
