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
use Psr\Log\LoggerInterface;
use Slim\Container;
use SnootBeest\Tantrum\Application;
use SnootBeest\Tantrum\Test\ConcreteMock\Service\ProvidedInterface;
use SnootBeest\Tantrum\Test\ConcreteMock\Service\SubDependency;
use SnootBeest\Tantrum\Test\ConcreteMock\Service\SubDependencyProvider;
use SnootBeest\Tantrum\Test\ConcreteMock\Service\Provided;
use SnootBeest\Tantrum\Test\ConcreteMock\Service\Provider;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Application
 */
class ApplicationTest extends ApplicationTestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     */
    public function initDependenciesSucceedsWithNoDependencies()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertTrue($container->has(LoggerInterface::class));
        self::assertInstanceOf(LoggerInterface::class, $container->get(LoggerInterface::class));
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessage No providerClass found for "SnootBeest\Tantrum\Test\ConcreteMock\Service\ProvidedInterface"
     */
    public function addDependencyThrowsExceptionWithNoProviderClass()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => []
            ]);

        $application = new Application($this->mockConfig);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     */
    public function addDependencyCreatesSingletonWithNull()
    {
        $expectedConstructorValue = uniqid();
        $expectedSetterValue      = uniqid();

        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => Provider::class,
                    Application::CONFIG_KEY_DEPENDENCIES   => [
                        SubDependencyProvider::KEY,
                    ]
                ],
                SubDependencyProvider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => SubDependencyProvider::class
                ]
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     */
    public function addDependencyCreatesSingleton()
    {
        $expectedConstructorValue = uniqid();
        $expectedSetterValue      = uniqid();

        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS  => Provider::class,
                    Application::CONFIG_KEY_DEPENDENCY_TYPE => Application::DEPENDENCY_TYPE_SINGLETON,
                    Application::CONFIG_KEY_DEPENDENCIES    => [
                        SubDependencyProvider::KEY,
                    ]
                ],
                SubDependencyProvider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => SubDependencyProvider::class
                ]
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertSame($provided, $container->get(Provider::KEY));
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     */
    public function addDependencyCreatesDefault()
    {
        $expectedConstructorValue = uniqid();
        $expectedSetterValue      = uniqid();

        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => Provider::class,
                    Application::CONFIG_KEY_DEPENDENCIES   => [
                        SubDependencyProvider::KEY,
                    ]
                ],
                SubDependencyProvider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => SubDependencyProvider::class
                ]
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     */
    public function initContainerCreatesFactory()
    {
        $expectedConstructorValue = uniqid();
        $expectedSetterValue      = uniqid();

        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS  => Provider::class,
                    Application::CONFIG_KEY_DEPENDENCY_TYPE => Application::DEPENDENCY_TYPE_FACTORY,
                    Application::CONFIG_KEY_DEPENDENCIES    => [
                        SubDependencyProvider::KEY,
                    ]
                ],
                SubDependencyProvider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => SubDependencyProvider::class
                ]
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertEquals($provided, $container->get(Provider::KEY));
        self::assertNotSame($provided, $container->get(Provider::KEY));
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     */
    public function initContainerCreatesClosure()
    {
        $expectedConstructorValue = uniqid();
        $expectedSetterValue      = uniqid();

        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS  => Provider::class,
                    Application::CONFIG_KEY_DEPENDENCY_TYPE => Application::DEPENDENCY_TYPE_PROTECT,
                    Application::CONFIG_KEY_DEPENDENCIES    => [
                        SubDependencyProvider::KEY,
                    ]
                ],
                SubDependencyProvider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS => SubDependencyProvider::class
                ]
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertEquals($provided, $container->get(Provider::KEY));
        self::assertInstanceOf(\Closure::class, $provided);
        self::assertDefaultDependencies($container);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessage "SnootBeest\Tantrum\Test\ConcreteMock\Service\SubDependency" is not an instance of SnootBeest\Tantrum\Service\ServiceProviderInterface
     */
    public function initContainerThrowsExceptionWithNoProviderInterface()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS  => SubDependency::class,
                ],
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessageRegExp /Unhandled dependency type "\w+"/
     */
    public function initContainerThrowsExceptionWithWrongDependencyType()
    {
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES, [])
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_DEPENDENCY_TYPE => uniqid(),
                    Application::CONFIG_KEY_PROVIDER_CLASS  => Provider::class,
                ],
            ]);
        $this->mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION, [])
            ->andReturn([]);

        $application = new Application($this->mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
    }
}
