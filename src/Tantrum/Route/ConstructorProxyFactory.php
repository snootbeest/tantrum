<?php
namespace SnootBeest\Tantrum\Route;

/**
 * Class ConstructorProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class ConstructorProxyFactory
{
    /** @var ParameterProxyFactory $parameterProxyFactory */
    private $parameterProxyFactory;

    /**
     * ConstructorProxyFactory constructor.
     * @param ParameterProxyFactory $parameterProxyFactory
     */
    public function __construct(ParameterProxyFactory $parameterProxyFactory)
    {
        $this->parameterProxyFactory = $parameterProxyFactory;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return ConstructorProxy
     */
    public function create(\ReflectionClass $reflectionClass):ConstructorProxy
    {
        return new ConstructorProxy($this->parameterProxyFactory, $reflectionClass->getConstructor());
    }
}