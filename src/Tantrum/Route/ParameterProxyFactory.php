<?php
namespace SnootBeest\Tantrum\Route;

/**
 * Class ParameterProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class ParameterProxyFactory
{
    /**
     * Create a new parameter proxy for the provided reflection parameter
     * @param \ReflectionParameter $parameter
     * @return ParameterProxy
     */
    public function create(\ReflectionParameter $parameter): ParameterProxy
    {
        return new ParameterProxy($parameter);
    }
}