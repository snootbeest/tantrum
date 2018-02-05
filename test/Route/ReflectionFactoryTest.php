<?php

namespace SnootBeest\Tantrum\Test\Route;

use SnootBeest\Tantrum\Route\ReflectionFactory;

class ReflectionFactoryTest extends RouteTestCase
{
    /**
     * @test
     */
    public function createSucceeds()
    {
        $reflectionFactory = new ReflectionFactory();
        $reflectionClass = $reflectionFactory->create(self::MOCK_ROUTE_NAMESPACE);
        self::assertInstanceOf(\ReflectionClass::class, $reflectionClass);
        self::assertEquals(self::MOCK_ROUTE_NAMESPACE, $reflectionClass->getName());
    }
}