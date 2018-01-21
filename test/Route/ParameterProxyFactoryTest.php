<?php
namespace SnootBeest\Tantrum\Test\Route;

use SnootBeest\Tantrum\Route\ParameterProxyFactory;

class ParameterProxyFactoryTest extends RouteTestCase
{
    /**
     * @test
     */
    public function createSucceeds()
    {
        $reflectionClass = $this->mock(\ReflectionClass::class);
        $reflectionClass->shouldReceive('getName')
            ->once()
            ->andReturn(self::TYPEHINTED_PARAMETER_KEY);

        $reflectionParameter = $this->mock(\ReflectionParameter::class);
        $reflectionParameter->shouldReceive('getName')
            ->once()
            ->andReturn(self::TYPEHINTED_PARAMETER_KEY);
        $reflectionParameter->shouldReceive('hasType')
            ->once()
            ->andReturn(true);

        $reflectionType = $this->mock('\ReflectionType');
        $reflectionType->shouldReceive('isBuiltin')
            ->once()
            ->andReturn(false);
        $reflectionParameter->shouldReceive('getType')
            ->once()
            ->andReturn($reflectionType);
        $reflectionParameter->shouldReceive('getClass')
            ->once()
            ->andReturn($reflectionClass);

        $parameterProxyFactory = new ParameterProxyFactory($reflectionParameter);
        $parameterProxy = $parameterProxyFactory->create($reflectionParameter);
        self::assertRequiredParameter($parameterProxy, self::TYPEHINTED_PARAMETER_KEY);
    }
}
