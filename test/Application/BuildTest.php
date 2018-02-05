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

namespace SnootBeest\Tantrum\Test\Application;

use Mockery;
use SnootBeest\Tantrum\Service\CacheProvider;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Slim\Container;
use SnootBeest\Tantrum\Application;
use SnootBeest\Tantrum\Route\ControllerProxy;
use SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute;
use SnootBeest\Tantrum\Route\ControllerProxyFactoryInterface;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Application
 */
class BuildTest extends ApplicationTestCase
{
    /**
     * @test
     * @covers ::build
     */
    public function buildSucceeds()
    {
        $this->mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(true);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(
                [MockRoute::class]
            );
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);

        $mockControllerProxy = $this->mock(ControllerProxy::class);
        $mockControllerProxy->shouldReceive('getClassName')
            ->once()
            ->andReturn(MockRoute::class);
        $mockControllerProxyFactory = $this->mock(ControllerProxyFactoryInterface::class);
        $mockControllerProxyFactory->shouldReceive('create')
            ->once()
            ->with(MockRoute::class)
            ->andReturn($mockControllerProxy);

        $mockCacheItem = $this->mock(CacheItemInterface::class);
        $mockCacheItem->shouldReceive('set')
            ->with([MockRoute::class => $mockControllerProxy]);

        $mockCache = $this->mock(CacheItemPoolInterface::class);
        $mockCache->shouldReceive('getItem')
            ->once()
            ->with(Application::ROUTES_CACHE_KEY)
            ->andReturn($mockCacheItem);
        $mockCache->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $container = new Container();
        $container[CacheProvider::getKey()] = $mockCache;
        $container[ControllerProxyFactoryInterface::class] = $mockControllerProxyFactory;

        $app = $this->mock(Application::class, ['getContainer'], [$this->mockConfig]);
        $app->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);

        self::assertTrue($app->build());
    }

    /**
     * @test
     * @covers ::build
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessage No controllers found in config
     */
    public function buildThrowsExceptionWithNoControllers()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(false);

        $app = new Application($this->mockConfig);
        $app->build();
    }

    /**
     * @test
     * @covers ::build
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessage Controllers must be an array
     */
    public function buildThrowsExceptionWithInvalidControllers()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(true);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(uniqid());

        $app = new Application($this->mockConfig);
        $app->build();
    }

    /**
     * @test
     * @covers ::build
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessage No routes added
     */
    public function buildCatchesAndLogsControllerProxyException()
    {
        $this->mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(true);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONTROLLERS)
            ->andReturn(
                [MockRoute::class]
            );
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);

        $exceptionMessage = uniqid();
        $mockControllerProxy = $this->mock(ControllerProxy::class);
        $mockControllerProxy->shouldReceive('getClassName')
            ->once()
            ->andThrow(new \Exception($exceptionMessage));
        $mockControllerProxyFactory = $this->mock(ControllerProxyFactoryInterface::class);
        $mockControllerProxyFactory->shouldReceive('create')
            ->once()
            ->with(MockRoute::class)
            ->andReturn($mockControllerProxy);

        $mockLogger = $this->mock(LoggerInterface::class);
        $mockLogger->shouldReceive('error')
            ->once()
            ->with($exceptionMessage);

        $container = new Container();
        $container[ControllerProxyFactoryInterface::class] = $mockControllerProxyFactory;
        $container[LoggerInterface::class] = $mockLogger;

        $app = $this->mock(Application::class, ['getContainer'], [$this->mockConfig]);
        $app->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);
        $app->build();
    }
}
