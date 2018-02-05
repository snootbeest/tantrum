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
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Slim\Container;
use SnootBeest\Tantrum\Application;
use SnootBeest\Tantrum\Route\ControllerProxy;
use SnootBeest\Tantrum\Route\MethodProxy;
use SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Application
 */
class RunTest extends ApplicationTestCase
{
    /**
     * Test to make sure that initRoutes does not interfere with normal Slim routing
     * @test
     * @covers ::run
     * @covers ::initRoutes
     */
    public function runSucceedsWithNoRoutes()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);

        $mockCacheItem = $this->mock(CacheItemInterface::class);
        $mockCacheItem->shouldReceive('isHit')
            ->andReturn(false);

        $mockCache = $this->mock(CacheItemPoolInterface::class);
        $mockCache->shouldReceive('getItem')
            ->once()
            ->with(Application::ROUTES_CACHE_KEY)
            ->andReturn($mockCacheItem);

        $container = new Container();
        $container[CacheItemPoolInterface::class] = $mockCache;

        $app = $this->mock(Application::class, ['getContainer'], [$this->mockConfig]);
        $app->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);

        $response = $app->run(true);
        self::assertNull($response);
    }

    /**
     * Test to make sure that initRoutes does not interfere with normal Slim routing
     * @test
     * @covers ::run
     * @covers ::initRoutes
     * @covers ::processControllerProxy
     */
    public function runSucceedsWithRoutes()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);

        $mockMethodProxy = $this->mock(MethodProxy::class);
        $mockMethodProxy->shouldReceive('getName')
            ->once()
            ->andReturn(uniqid());
        $mockMethodProxy->shouldReceive('getRoute')
            ->once()
            ->andReturn(uniqid());
        $mockMethodProxy->shouldReceive('getMethods')
            ->once()
            ->andReturn(['GET']);

        $mockControllerProxy = $this->mock(ControllerProxy::class);
        $mockControllerProxy->shouldReceive('getClassName')
            ->twice()
            ->andReturn(MockRoute::class);
        $mockControllerProxy->shouldReceive('getMethods')
            ->once()
            ->andReturn([$mockMethodProxy]);

        $mockCacheItem = $this->mock(CacheItemInterface::class);
        $mockCacheItem->shouldReceive('isHit')
            ->andReturn(true);
        $mockCacheItem->shouldReceive('get')
            ->andReturn([
                $mockControllerProxy
            ]);

        $mockCache = $this->mock(CacheItemPoolInterface::class);
        $mockCache->shouldReceive('getItem')
            ->once()
            ->with(Application::ROUTES_CACHE_KEY)
            ->andReturn($mockCacheItem);

        $container = new Container();
        $container[CacheItemPoolInterface::class] = $mockCache;

        $app = $this->mock(Application::class, ['getContainer'], [$this->mockConfig]);
        $app->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);

        $response = $app->run(true);
        self::assertNull($response);
    }

    /**
     * @test
     * @covers ::run
     */
    public function runRespondeWhenNotSilent()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);

        $mockCacheItem = $this->mock(CacheItemInterface::class);
        $mockCacheItem->shouldReceive('isHit')
            ->andReturn(false);

        $mockCache = $this->mock(CacheItemPoolInterface::class);
        $mockCache->shouldReceive('getItem')
            ->once()
            ->with(Application::ROUTES_CACHE_KEY)
            ->andReturn($mockCacheItem);

        $container = new Container();
        $container[CacheItemPoolInterface::class] = $mockCache;

        $app = $this->mock(Application::class, ['getContainer'], [$this->mockConfig]);
        $app->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);

        ob_start();
        $app->run();
        $response = ob_get_clean();
        self::assertRegexp('/Page Not Found/', $response);
    }
}
