<?php

namespace SnootBeest\Tantrum\Test\Route;


use phpDocumentor\Reflection\DocBlockFactory;
use SnootBeest\Tantrum\Route\MethodProxy;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Route\MethodProxy
 */
class MethodProxyTest extends RouteTestCase
{
    private $methodName = 'getControllerMethod';

    private $docComment = '
        /**
         * @param string $parameter
         * @httpMethod GET
         * @route /mock/route/get
         * @return returnValue
         */';


    /**
     * @test
     * @covers ::__construct
     * @covers ::getName
     */
    public function getNameSucceeds()
    {
        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($this->docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        self::assertEquals($this->methodName, $methodProxy->getName());
    }

    /**
     * @test
     * @dataProvider supportedMethodsDataProvider
     * @covers ::__construct
     * @covers ::getMethods
     * @covers ::getHttpRequestMethods
     */
    public function getMethodsSucceeds($method)
    {
        $docComment = '
        /**
         * @httpMethod {METHOD}
         * @route /mock/route/get
         */';

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create(str_replace('{METHOD}', $method, $docComment));

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        self::assertEquals([$method], $methodProxy->getMethods());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getHttpRequestMethods
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessage No httpMethod annotation found
     */
    public function noHttpMethodThrowsException()
    {
        $docComment = '
            /**
             * @route /mock/route/get
             */';
        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getHttpRequestMethods
     * @dataProvider unsupportedMethodsDataProvider
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessageRegExp /HTTP methods \["\w+"\] are not allowed. Allowed methods are \["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS", "HEAD"\]/
     */
    public function unsupportedHttpMethodThrowsException($method)
    {
        $docComment = '
        /**
         * @httpMethod {METHOD}
         * @route /mock/route/get
         */';

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create(str_replace('{METHOD}', $method, $docComment));

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPattern
     * @covers ::getRoute
     */
    public function getRouteSucceeds()
    {
        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($this->docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        self::assertEquals('/mock/route/get', $methodProxy->getRoute());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPattern
     * @covers ::getRoute
     */
    public function noRouteReturnsEmpty()
    {
        $docComment = '
        /**
         * @httpMethod GET
         */';

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        self::assertEquals('', $methodProxy->getRoute());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPattern
     * @covers ::getRoute
     */
    public function emptyRouteReturnsEmpty()
    {
        $docComment = '
        /**
         * @httpMethod GET
         * @route
         */';

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        self::assertEquals('', $methodProxy->getRoute());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::getPattern
     * @covers ::getRoute
     * @expectedException \SnootBeest\Tantrum\Exception\BuildException
     * @expectedExceptionMessage Only one route annotation is allowed
     */
    public function multipleRoutesThrowsException()
    {
        $docComment = '
        /**
         * @route /mock/route/post
         * @httpMethod GET
         * @route /mock/route/get
         */';

        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
    }

    /**
     * @test
     * @covers ::serialize
     * @covers ::unserialize
     */
    public function serializationSucceeds()
    {
        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock = $docBlockFactory->create($this->docComment);

        $methodProxy = new MethodProxy($this->methodName, $docBlock);
        $serialized = serialize($methodProxy);
        $unserialized = unserialize($serialized);

        self::assertEquals($this->methodName, $methodProxy->getName());
        self::assertEquals(['GET'], $methodProxy->getMethods());
        self::assertEquals('/mock/route/get', $methodProxy->getRoute());
    }

    // Data Providers

    /**
     * @return array
     */
    public function supportedMethodsDataProvider(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            ['OPTIONS'],
        ];
    }

    /**
     * @return array
     */
    public function unsupportedMethodsDataProvider(): array
    {
        return [
            ['TRACE'],
            ['CONNECT'],
        ];
    }
}
