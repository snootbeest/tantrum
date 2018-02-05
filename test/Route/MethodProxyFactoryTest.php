<?php

namespace SnootBeest\Tantrum\Test\Route;


use phpDocumentor\Reflection\DocBlockFactory;
use SnootBeest\Tantrum\Route\MethodProxy;
use SnootBeest\Tantrum\Route\MethodProxyFactory;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Route\MethodProxyFactory
 */
class MethodProxyFactoryTest extends RouteTestCase
{

    /**
     * @test
     * @covers ::__construct
     * @covers ::create
     */
    public function createSucceeds()
    {
        $methodName = 'testGetMethod';
        $httpMethod = 'GET';
        $route      = '/mock/route/get';
        $docComment = '
        /**
         * @param string $parameter
         * @httpMethod '.$httpMethod.'
         * @route '.$route.'
         * @return returnValue
         */';

        $docBlockFactory = DocBlockFactory::createInstance();

        $reflectionMethod = $this->mock(\ReflectionMethod::class);
        $reflectionMethod->shouldReceive('getDocComment')
            ->once()
            ->andReturn($docComment);
        $reflectionMethod->shouldReceive('getName')
            ->once()
            ->andReturn($methodName);

        $methodProxyFactory = new MethodProxyFactory($docBlockFactory);
        $methodProxy = $methodProxyFactory->create($reflectionMethod);

        self::assertInstanceOf(MethodProxy::class, $methodProxy);
        self::assertEquals($methodName, $methodProxy->getName());
        self::assertTrue(is_array($methodProxy->getMethods()));
        self::assertCount(1, $methodProxy->getMethods());
        self::assertTrue(in_array($httpMethod, $methodProxy->getMethods()));
        self::assertEquals($route, $methodProxy->getRoute());
    }
}
