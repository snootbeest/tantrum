<?php
namespace SnootBeest\Tantrum\Test;

use \Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Return the value of an inaccessible property
     * @param mixed $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getInaccessiblePropertyValue(string $propertyName, $object)
    {
        return self::getInaccessibleProperty($object, $propertyName)->getValue($object);
    }

    /**
     * Allow access to an inaccessible property
     * @param mixed $object
     * @param string $propertyName
     * @throws \Exception
     * @return \ReflectionProperty
     */
    protected function getInaccessibleProperty($object, string $propertyName): \ReflectionProperty
    {
        $reflectionClass = new \ReflectionClass($object);
        do {
            if($reflectionClass->hasProperty($propertyName)) {
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                self::assertTrue($reflectionProperty->isPrivate() || $reflectionProperty->isProtected());
                $reflectionProperty->setAccessible(true);
                return $reflectionProperty;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());
        throw new \Exception(sprintf('Property %s was not found', $propertyName));
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        Mockery::close();
    }
}