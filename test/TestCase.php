<?php
/**
 * This file is part of tantrum.
 *
 *  tantrum is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  tantrum is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with tantrum.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SnootBeest\Tantrum\Test;

use Mockery;
use Psr\Log\LoggerInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var LoggerInterface|Mockery\Mock */
    private $mockLogger;

    /**
     * Return the value of an inaccessible property
     * @param string $propertyName
     * @param mixed $object
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
     * Provides a mocked object
     * @param string $namespace      - The full namespace of the class to mock
     * @param array $methods         - [Optional] Creates a partial mock with these methods mocked
     * @param array $constructorArgs - [Optional] Constructor arguments for partial mocks
     * @return mixed|\Mockery\Mock
     */
    protected function mock(string $namespace, array $methods = [], array $constructorArgs = [])
    {
        if (count($methods) > 0) {
            $methodString = sprintf('[%s]', implode(',', $methods));
            $object = Mockery::mock($namespace.$methodString, $constructorArgs);
        } else {
            $object = Mockery::mock($namespace);
        }

        return $object;
    }

    /**
     * Provides a mocked logger object
     * @return LoggerInterface|Mockery\Mock
     */
    protected function mockLogger()
    {
        $this->mockLogger = $this->mock(LoggerInterface::class);
        return $this->mockLogger;
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        Mockery::close();
    }
}
