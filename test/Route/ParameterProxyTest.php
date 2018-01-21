<?php

namespace SnootBeest\Tantrum\Test\Route;


use Mockery\Mock;
use SnootBeest\Tantrum\Route\ParameterProxy;

class ParameterProxyTest extends RouteTestCase
{
    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Param "Psr\\Log\\LoggerInterface" has no default value/
     */
    public function typeHintedParameterSucceeds()
    {
        $reflectionClass = $this->mock('\ReflectionClass');
        $reflectionClass->shouldReceive('getName')
            ->once()
            ->andReturn(self::TYPEHINTED_PARAMETER_KEY);

        $reflectionParameter = $this->mock('\ReflectionParameter');
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

        $parameterProxy = new ParameterProxy($reflectionParameter);
        $this->assertTypeHintedParameter($parameterProxy, self::TYPEHINTED_PARAMETER_KEY);
        $parameterProxy->getDefaultValue();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Param "\w+" has no default value/
     */
    public function requiredParameterSucceeds()
    {
        $reflectionParameter = $this->mock('\ReflectionParameter');
        $reflectionParameter->shouldReceive('getName')
            ->once()
            ->andReturn(self::REQUIRED_PARAMETER_KEY);
        $reflectionParameter->shouldReceive('hasType')
            ->once()
            ->andReturn(false);
        $reflectionParameter->shouldReceive('isDefaultValueAvailable')
            ->once()
            ->andReturn(false);

        $parameterProxy = new ParameterProxy($reflectionParameter);
        $this->assertRequiredParameter($parameterProxy, self::REQUIRED_PARAMETER_KEY);
        $parameterProxy->getDefaultValue();
    }

    /**
     * @test
     */
    public function optionalParameterSucceeds()
    {
        $reflectionParameter = $this->mock('\ReflectionParameter');
        $reflectionParameter->shouldReceive('getName')
            ->once()
            ->andReturn(self::OPTIONAL_PARAMETER_KEY);
        $reflectionParameter->shouldReceive('hasType')
            ->once()
            ->andReturn(false);
        $reflectionParameter->shouldReceive('isDefaultValueAvailable')
            ->once()
            ->andReturn(true);
        $reflectionParameter->shouldReceive('getDefaultValue')
            ->once()
            ->andReturn(self::OPTIONAL_PARAMETER_VALUE);

        $parameterProxy = new ParameterProxy($reflectionParameter);
        $this->assertOptionalParameter($parameterProxy, self::OPTIONAL_PARAMETER_KEY, self::OPTIONAL_PARAMETER_VALUE);
    }

    /**
     * @test
     */
    public function serializationSucceeds()
    {
        $reflectionClass = new \ReflectionClass(self::MOCK_ROUTE_NAMESPACE);
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();

        $typeHinted = serialize(new ParameterProxy($parameters[0]));
        $required   = serialize(new ParameterProxy($parameters[1]));
        $optional   = serialize(new ParameterProxy($parameters[2]));

        $this->assertTypeHintedParameter(unserialize($typeHinted), self::TYPEHINTED_PARAMETER_KEY);
        $this->assertRequiredParameter(unserialize($required), self::REQUIRED_PARAMETER_KEY);
        $this->assertOptionalParameter(unserialize($optional), self::OPTIONAL_PARAMETER_KEY, self::OPTIONAL_PARAMETER_VALUE);
    }
}