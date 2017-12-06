<?php
namespace SnootBeest\Tantrum\Route;

use phpDocumentor\Reflection\DocBlockFactory;

/**
 * Class MethodProxyFactory
 * @package SnootBeest\Tantrum\Route
 */
class MethodProxyFactory
{
    /** @var DocBlockFactory $docBlockFactory */
    private $docBlockFactory;

    /**
     * MethodProxyFactory constructor.
     * @param DocBlockFactory $docBlockFactory
     */
    public function __construct(DocBlockFactory $docBlockFactory) 
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * Create and return a new method proxy
     * @param \ReflectionMethod $reflectionMethod
     * @return MethodProxy
     */
    public function create(\ReflectionMethod $reflectionMethod): MethodProxy
    {
        return new MethodProxy($reflectionMethod->getName(), $this->docBlockFactory->create($reflectionMethod->getDocComment()));
    }
}