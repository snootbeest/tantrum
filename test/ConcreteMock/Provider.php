<?php
namespace SnootBeest\Tantrum\Test\ConcreteMock;

use SnootBeest\Tantrum\Service\ServiceProvider;

class Provider extends ServiceProvider
{
    const KEY = 'SnootBeest\Tantrum\Test\ConcreteMock\ProvidedInterface';

    /** @var SubDependency $dependency */
    private $dependency;

    /**
     * Provision a dependency object
     * @param SubDependency $dependency
     */
    public function __construct(SubDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @inheritdoc
     */
    public function __invoke()
    {
        $providedService = new Provided($this->config->get('constructorValue'));
        $providedService->setValue($this->config->get('setterValue'));
        return $providedService;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function getKey():string
    {
        return self::KEY;
    }
}