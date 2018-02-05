<?php

namespace SnootBeest\Tantrum\Test\Route;


use SnootBeest\Tantrum\Controller;
use SnootBeest\Tantrum\Route\ConstructorProxyFactory;
use SnootBeest\Tantrum\Route\ControllerProxyFactory;
use SnootBeest\Tantrum\Route\MethodProxyFactory;
use SnootBeest\Tantrum\Route\ReflectionFactory;
use SnootBeest\Tantrum\Route\ControllerProxy;

class ControllerProxyFactoryTest extends RouteTestCase
{

    /**
     * @test
     */
    public function createSucceeds()
    {
        $logger = $this->mockLogger();

        $reflectionClass = $this->mock(\ReflectionClass::class);
        $reflectionClass->shouldReceive('isInstantiable')
            ->once()
            ->andReturn(true);
        $reflectionClass->shouldReceive('isSubclassOf')
            ->once()
            ->with(Controller::class)
            ->andReturn(true);
        $reflectionClass->shouldReceive('getName')
            ->once()
            ->andReturn(self::MOCK_ROUTE_NAMESPACE);
        $reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([]);

        $reflectionFactory = $this->mock(ReflectionFactory::class);
        $reflectionFactory->shouldReceive('create')
            ->with(self::MOCK_ROUTE_NAMESPACE)
            ->andReturn($reflectionClass);

        $constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);
        $constructorProxyFactory->shouldReceive('create')
            ->once();

        $methodProxyFactory = $this->mock(MethodProxyFactory::class);

        $controllerProxyFactory = new ControllerProxyFactory($logger, $reflectionFactory, $constructorProxyFactory, $methodProxyFactory);
        $controllerProxy = $controllerProxyFactory->create(self::MOCK_ROUTE_NAMESPACE);

        self::assertInstanceOf(ControllerProxy::class, $controllerProxy);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Ignoring SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute; isInstantiable:false isRouteController:true
     */
    public function createThrowsExceptionWithUninstantiableClass()
    {
        $logger = $this->mockLogger();

        $reflectionClass = $this->mock(\ReflectionClass::class);
        $reflectionClass->shouldReceive('isInstantiable')
            ->once()
            ->andReturn(false);
        $reflectionClass->shouldReceive('isSubclassOf')
            ->once()
            ->with(Controller::class)
            ->andReturn(true);

        $reflectionFactory = $this->mock(ReflectionFactory::class);
        $reflectionFactory->shouldReceive('create')
            ->with(self::MOCK_ROUTE_NAMESPACE)
            ->andReturn($reflectionClass);

        $constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);

        $methodProxyFactory = $this->mock(MethodProxyFactory::class);

        $controllerProxyFactory = new ControllerProxyFactory($logger, $reflectionFactory, $constructorProxyFactory, $methodProxyFactory);
        $controllerProxyFactory->create(self::MOCK_ROUTE_NAMESPACE);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Ignoring SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute; isInstantiable:true isRouteController:false
     */
    public function createThrowsExceptionWithNonControllerClass()
    {
        $logger = $this->mockLogger();

        $reflectionClass = $this->mock('\ReflectionClass');
        $reflectionClass->shouldReceive('isInstantiable')
            ->once()
            ->andReturn(true);
        $reflectionClass->shouldReceive('isSubclassOf')
            ->once()
            ->with(Controller::class)
            ->andReturn(false);

        $reflectionFactory = $this->mock(ReflectionFactory::class);
        $reflectionFactory->shouldReceive('create')
            ->with(self::MOCK_ROUTE_NAMESPACE)
            ->andReturn($reflectionClass);

        $constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);

        $methodProxyFactory = $this->mock(MethodProxyFactory::class);

        $controllerProxyFactory = new ControllerProxyFactory($logger, $reflectionFactory, $constructorProxyFactory, $methodProxyFactory);
        $controllerProxyFactory->create(self::MOCK_ROUTE_NAMESPACE);
    }
}
