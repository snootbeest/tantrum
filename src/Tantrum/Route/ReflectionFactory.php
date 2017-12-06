<?php
namespace SnootBeest\Tantrum\Route;

/**
 * Class ReflectionFactory
 * @package SnootBeest\Tantrum\Route
 */
class ReflectionFactory
{
    /**
     * Create a new reflection class from a fully qualified namespace
     * @param string $namespace
     * @return \ReflectionClass
     */
    public function create($namespace): \ReflectionClass
    {
        return new \ReflectionClass($namespace);
    }
}