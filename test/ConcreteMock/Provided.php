<?php
namespace SnootBeest\Tantrum\Test\ConcreteMock;

class Provided implements ProvidedInterface
{
    /** @var string */
    private $constructValue;

    /** @var string */
    private $setterValue;

    /**
     * Example of how a provider can set values via the constructor
     * @param string $constructValue
     */
    public function __construct(string $constructValue)
    {
        $this->constructValue = $constructValue;
    }

    /**
     * Example of how a provider can set values via a setter
     * @param string $setterValue
     */
    public function setValue(string $setterValue)
    {
        $this->setterValue = $setterValue;
    }

}