<?php
namespace SnootBeest\Tantrum\Test\ConcreteMock;

use SnootBeest\Tantrum\Service\ServiceProvider;

class SubDependencyProvider extends ServiceProvider
{
    const KEY = SubDependency::class;

    /**
     * @inheritdoc
     * @return SubDependency
     */
    public function __invoke(): SubDependency
    {
        return new SubDependency();
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function getKey(): string
    {
        return static::KEY;
    }
}