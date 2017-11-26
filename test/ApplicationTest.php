<?php

namespace SnootBeest\Tantrum\Test;

use Mockery;
use Noodlehaus\Config;
use Slim\Container;
use SnootBeest\Tantrum\Application;;
use SnootBeest\Tantrum\Test\ConcreteMock\ProvidedInterface;
use SnootBeest\Tantrum\Test\ConcreteMock\SubDependency;
use SnootBeest\Tantrum\Test\ConcreteMock\SubDependencyProvider;
use SnootBeest\Tantrum\Test\ConcreteMock\Provided;
use SnootBeest\Tantrum\Test\ConcreteMock\Provider;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Application
 */
class ApplicationTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessage No dependencies mapped
     */
    public function initDependenciesThrowsExceptionWithNoDependencies()
    {
        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnFalse();
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnFalse();

        $application = new Application($mockConfig);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessage No providerClass found for "SnootBeest\Tantrum\Test\ConcreteMock\ProvidedInterface"
     */
    public function addDependencyThrowsExceptionWithNoProviderClass()
    {
        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnFalse();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturn([
                Provider::KEY => []
            ]);

        $application = new Application($mockConfig);
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

        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
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
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
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

        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
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
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($mockConfig);
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

        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
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
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertInstanceOf(Provided::class, $provided);
        $constructorValue = $this->getInaccessiblePropertyValue('constructValue', $provided);
        self::assertEquals($expectedConstructorValue, $constructorValue);
        $setterValue = $this->getInaccessiblePropertyValue('setterValue', $provided);
        self::assertEquals($expectedSetterValue, $setterValue);
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

        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
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
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($mockConfig);
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

        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
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
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturn([
                ProvidedInterface::class => [
                    'constructorValue' => $expectedConstructorValue,
                    'setterValue'      => $expectedSetterValue,
                ]
            ]);

        $application = new Application($mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
        self::assertEquals($provided, $container->get(Provider::KEY));
        self::assertInstanceOf(\Closure::class, $provided);

    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initContainer
     * @covers ::initDependencies
     * @covers ::addDependency
     * @covers ::createService
     * @expectedException \SnootBeest\Tantrum\Exception\BootstrapException
     * @expectedExceptionMessage "SnootBeest\Tantrum\Test\ConcreteMock\SubDependency" is not an instance of SnootBeest\Tantrum\Service\ServiceProviderInterface
     */
    public function initContainerThrowsExceptionWithNoProviderInterface()
    {
        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_PROVIDER_CLASS  => SubDependency::class,
                ],
            ]);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnFalse();

        $application = new Application($mockConfig);
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
        /** @var Mockery\Mock | Config $mockConfig */
        $mockConfig = Mockery::mock(Config::class);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturnTrue();
        $mockConfig->shouldReceive('get')
            ->once()
            ->with(Application::CONFIG_KEY_DEPENDENCIES)
            ->andReturn([
                Provider::KEY => [
                    Application::CONFIG_KEY_DEPENDENCY_TYPE => uniqid(),
                    Application::CONFIG_KEY_PROVIDER_CLASS  => Provider::class,
                ],
            ]);
        $mockConfig->shouldReceive('has')
            ->once()
            ->with(Application::CONFIG_KEY_CONFIGURATION)
            ->andReturnFalse();

        $application = new Application($mockConfig);
        $container = $application->getContainer();
        self::assertInstanceOf(Container::class, $container);
        self::assertTrue($container->has(Provider::KEY));
        $provided = $container->get(Provider::KEY);
    }
}