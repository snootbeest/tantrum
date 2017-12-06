<?php
namespace SnootBeest\Tantrum\Route;

/**
 * Class ConstructorProxy
 * @package SnootBeest\Tantrum\Route
 */
class ConstructorProxy implements \Serializable
{
    /** @var ParameterProxy[] $params */
    private $params = [];

    /**
     * ConstructorProxy constructor.
     * @param ParameterProxyFactory $parameterProxyFactory
     * @param \ReflectionMethod $reflectionMethod
     */
    public function __construct(ParameterProxyFactory $parameterProxyFactory, \ReflectionMethod $reflectionMethod = null)
    {
        $this->params = $this->getConstructorArguments($parameterProxyFactory, $reflectionMethod);
    }

    /**
     * @return ParameterProxy[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get the constructor arguments for the route for dependency injection
     * @param ParameterProxyFactory $parameterProxyFactory
     * @param \ReflectionMethod $constructor
     * @return array
     */
    private function getConstructorArguments(ParameterProxyFactory $parameterProxyFactory, \ReflectionMethod $constructor = null): array
    {
        $params = [];
        if(!is_null($constructor)) {
            $reflectionParameters = $constructor->getParameters();
            foreach($reflectionParameters as $reflectionParameter) {
                $params[] = $parameterProxyFactory->create($reflectionParameter);
            }
        }

        return $params;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize()
    {
        return serialize($this->params);
    }

    /**
     * @inheritdoc
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }
}