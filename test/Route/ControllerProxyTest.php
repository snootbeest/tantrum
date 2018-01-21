<?php

namespace SnootBeest\Tantrum\Test\Route;

use Mockery\Mock;
use phpDocumentor\Reflection\DocBlockFactory;
use Psr\Log\LoggerInterface;
use Slim\Container;
use SnootBeest\Tantrum\Route\ConstructorProxy;
use SnootBeest\Tantrum\Route\ConstructorProxyFactory;
use SnootBeest\Tantrum\Route\ConstructorProxyFactoryInterface;
use SnootBeest\Tantrum\Route\MethodProxy;
use SnootBeest\Tantrum\Route\MethodProxyFactory;
use SnootBeest\Tantrum\Route\ControllerProxy;
use SnootBeest\Tantrum\Route\ParameterProxy;
use SnootBeest\Tantrum\Route\ParameterProxyFactory;
use SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute;
use SnootBeest\Tantrum\Test\ConcreteMock\MockRoute_2;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Route\ControllerProxy
 */
class ControllerProxyTest extends RouteTestCase
{
    /** @var LoggerInterface|Mock $logger */
    private $logger;

    /** @var ConstructorProxyFactory|Mock $constructorProxyFactory */
    private $constructorProxyFactory;

    /** @var MethodProxyFactory|Mock $constructorProxyFactory */
    private $methodProxyFactory;

    /** @var \ReflectionClass|Mock $reflectionClass */
    private $reflectionClass;

    /**
     * @test
     * @covers ::__construct
     * @covers ::getClassName
     */
    public function getClassNameSucceeds()
    {
        $this->constructorProxyFactory->shouldReceive('create')
            ->once();

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([]);

        $controllerProxy = new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        self::assertEquals(self::MOCK_ROUTE_NAMESPACE, $controllerProxy->getClassName());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodProxies
     * @covers ::getMethods
     */
    public function getMethodsSucceeds()
    {
        $expectedMethodName = 'testGetMethod';

        $this->constructorProxyFactory->shouldReceive('create')
            ->once();

        $reflectionMethod = $this->mock(\ReflectionMethod::class, ['isConstructor'], [self::MOCK_ROUTE_NAMESPACE, $expectedMethodName]);
        $reflectionMethod->shouldReceive('isConstructor')
            ->once()
            ->andReturn(false);

        $methodProxy = $this->mock(MethodProxy::class);
        $methodProxy->shouldReceive('getName')
            ->once()
            ->andReturn($expectedMethodName);

        $this->methodProxyFactory->shouldReceive('create')
            ->once()
            ->with($reflectionMethod)
            ->andReturn($methodProxy);

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([$reflectionMethod]);

        $controllerProxy = new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        self::assertArrayHasKey($expectedMethodName, $controllerProxy->getMethods());
        self::assertSame($methodProxy, $controllerProxy->getMethods()[$expectedMethodName]);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodProxies
     * @covers ::getMethods
     */
    public function getMethodsIgnoresConstructors()
    {
        $expectedMethodName = 'testGetMethod';

        $this->constructorProxyFactory->shouldReceive('create')
            ->once();

        $reflectionMethod = $this->mock(\ReflectionMethod::class, ['isConstructor'], [self::MOCK_ROUTE_NAMESPACE, $expectedMethodName]);
        $reflectionMethod->shouldReceive('isConstructor')
            ->once()
            ->andReturn(true);

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([$reflectionMethod]);

        $controllerProxy = new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        self::assertArrayNotHasKey($expectedMethodName, $controllerProxy->getMethods());
        self::assertEquals([], $controllerProxy->getMethods());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodProxies
     * @covers ::getMethods
     */
    public function getMethodsIgnoresAncestors()
    {
        $expectedMethodName = 'ancestorPublicMethod';

        $this->constructorProxyFactory->shouldReceive('create')
            ->once();

        $reflectionMethod = $this->mock(\ReflectionMethod::class, [], ['RouteAncestorClass', $expectedMethodName]);

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->with(\ReflectionMethod::IS_PUBLIC)
            ->andReturn([$reflectionMethod]);

        $controllerProxy = new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        self::assertArrayNotHasKey($expectedMethodName, $controllerProxy->getMethods());
        self::assertEquals([], $controllerProxy->getMethods());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getMethodProxies
     */
    public function methodProxyExceptionLogged()
    {
        $expectedMethodName = 'testGetMethod';

        $this->constructorProxyFactory->shouldReceive('create')
            ->once();

        $reflectionMethod = $this->mock(\ReflectionMethod::class, ['isConstructor'], [self::MOCK_ROUTE_NAMESPACE, $expectedMethodName]);
        $reflectionMethod->shouldReceive('isConstructor')
            ->once()
            ->andReturn(false);

        $expectedExceptionMessage = uniqid();
        $this->methodProxyFactory->shouldReceive('create')
            ->once()
            ->with($reflectionMethod)
            ->andThrow('\Exception', $expectedExceptionMessage);

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([$reflectionMethod]);

        $this->logger->shouldReceive('debug')
            ->with(sprintf("%s::%s\" was not added to the router (%s)\n", self::MOCK_ROUTE_NAMESPACE, $expectedMethodName, $expectedExceptionMessage));

        $controllerProxy = new ControllerProxy($this->logger, $this->constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);

        self::assertInstanceOf(ControllerProxy::class, $controllerProxy);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getConstructor
     */
    public function getConstructorSucceeds()
    {
        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([]);

        $constructorProxy = $this->mock(ConstructorProxy::class);
        $constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);
        $constructorProxyFactory->shouldReceive('create')
            ->once()
            ->with($this->reflectionClass)
            ->andReturn($constructorProxy);

        $controllerProxy = new ControllerProxy($this->logger, $constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        self::assertSame($constructorProxy, $controllerProxy->getConstructor());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::serialize
     * @covers ::unserialize
     */
    public function serializationSucceeds()
    {
        $reflectionClass = new \ReflectionClass(MockRoute::class);
        $controllerProxy = new ControllerProxy($this->logger, new ConstructorProxyFactory(new ParameterProxyFactory()), new MethodProxyFactory(DocBlockFactory::createInstance()), $reflectionClass);
        $serialized = serialize($controllerProxy);
        /** @var ControllerProxy $unserialized */
        $unserialized = unserialize($serialized);
        self::assertEquals(MockRoute::class, $unserialized->getClassName());
        self::assertMethodProxies($unserialized->getMethods());
        self::assertConstructor($unserialized->getConstructor());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function invokeSucceeds()
    {
        $expectedMethodName = 'testGetMethod';

        $reflectionMethod = $this->mock('\ReflectionMethod', ['isConstructor'], [self::MOCK_ROUTE_NAMESPACE, $expectedMethodName]);
        $reflectionMethod->shouldReceive('isConstructor')
            ->once()
            ->andReturn(false);

        $methodProxy = $this->mock(MethodProxy::class);
        $methodProxy->shouldReceive('getName')
            ->once()
            ->andReturn($expectedMethodName);

        $this->methodProxyFactory->shouldReceive('create')
            ->once()
            ->with($reflectionMethod)
            ->andReturn($methodProxy);

        $this->reflectionClass->shouldReceive('getMethods')
            ->once()
            ->andReturn([$reflectionMethod]);

        $container = $this->mock(Container::class);

        // Set the value for the logger dependency
        $loggerKey = uniqid();
        $logger = $this->mock(LoggerInterface::class);
        $loggerParamProxy = $this->mock(ParameterProxy::class);
        $loggerParamProxy->shouldReceive('hasDefault')
            ->andReturn(false);
        $container->shouldReceive('has')
            ->with($loggerKey)
            ->andReturn(true);
        $loggerParamProxy->shouldReceive('getKey')
            ->once()
            ->andReturn($loggerKey);
        $container->shouldReceive('get')
            ->with($loggerKey)
            ->andReturn($logger);

        // Set the value for the required dependency
        $requiredKey = uniqid();
        $requiredValue = uniqid();
        $requiredParamProxy = $this->mock(ParameterProxy::class);
        $requiredParamProxy->shouldReceive('hasDefault')
            ->andReturn(false);
        $container->shouldReceive('has')
            ->with($loggerKey)
            ->andReturn(true);
        $requiredParamProxy->shouldReceive('getKey')
            ->once()
            ->andReturn($requiredKey);
        $container->shouldReceive('get')
            ->with($requiredKey)
            ->andReturn($requiredValue);

        // Set the value for an optional dependency which exists in the container
        $optionalProvidedKey = uniqid();
        $optionalProvidedValue = uniqid();
        $optionalProvidedParamProxy = $this->mock(ParameterProxy::class);
        $optionalProvidedParamProxy->shouldReceive('hasDefault')
            ->andReturn(true);
        $optionalProvidedParamProxy->shouldReceive('getKey')
            ->twice()
            ->andReturn($optionalProvidedKey);
        $container->shouldReceive('has')
            ->with($optionalProvidedKey)
            ->andReturn(true);
        $container->shouldReceive('get')
            ->with($optionalProvidedKey)
            ->andReturn($optionalProvidedValue);

        // Set the value for an optional dependency which does not exist in the container
        $optionalKey   = uniqid();
        $optionalValue = uniqid();
        $optionalParamProxy = $this->mock(ParameterProxy::class);
        $optionalParamProxy->shouldReceive('hasDefault')
            ->andReturn(true);
        $optionalParamProxy->shouldReceive('getKey')
            ->once()
            ->andReturn($optionalKey);
        $optionalParamProxy->shouldReceive('getDefaultValue')
            ->once()
            ->andReturn($optionalValue);
        $container->shouldReceive('has')
            ->with($optionalKey)
            ->andReturn(false);

        // Set the parameter proxies
        $constructorProxy = $this->mock(ConstructorProxy::class);
        $constructorProxy->shouldReceive('getParams')
            ->once()
            ->andReturn([
                $loggerParamProxy,
                $requiredParamProxy,
                $optionalProvidedParamProxy,
                $optionalParamProxy,
            ]);
        $constructorProxyFactory = $this->mock(ConstructorProxyFactoryInterface::class);
        $constructorProxyFactory->shouldReceive('create')
            ->once()
            ->with($this->reflectionClass)
            ->andReturn($constructorProxy);

        // Set the expectations for request and response
        $request = $this->mock('Slim\Http\Request');
        $response = $this->mock('Slim\Http\Response');
        $container->shouldReceive('get')
            ->with('request')
            ->andReturn($request);
        $container->shouldReceive('get')
            ->with('response')
            ->andReturn($response);

        $controllerProxy = new ControllerProxy($this->logger, $constructorProxyFactory, $this->methodProxyFactory, $this->reflectionClass);
        $controller = $controllerProxy($container);
        self::assertSame($request, $this->getInaccessiblePropertyValue('request', $controller));
        self::assertSame($response, $this->getInaccessiblePropertyValue('response', $controller));
        self::assertSame($logger, $this->getInaccessiblePropertyValue('logger', $controller));
        self::assertSame($requiredValue, $this->getInaccessiblePropertyValue('required', $controller));
        self::assertSame($optionalProvidedValue, $this->getInaccessiblePropertyValue('optional', $controller));
        self::assertSame($optionalValue, $this->getInaccessiblePropertyValue('secondOptional', $controller));
    }

    // Utils

    protected function setUp()
    {
        $this->logger = $this->mockLogger();

        $this->reflectionClass = $this->mock(\ReflectionClass::class);
        $this->reflectionClass->shouldReceive('getName')
            ->andReturn(self::MOCK_ROUTE_NAMESPACE);

        $this->constructorProxyFactory = $this->mock(ConstructorProxyFactory::class);

        $this->methodProxyFactory = $this->mock(MethodProxyFactory::class);

    }
}
