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

namespace SnootBeest\Tantrum\Route;

/**
 * Class ParameterProxy
 * @package SnootBeest\Tantrum\Route
 */
class ParameterProxy implements ParameterProxyInterface
{
    /** @var \ReflectionParameter $reflectionParameter */
    private $reflectionParameter;

    /** @var bool $isInitialised; */
    private $isInitialised = false;

    /** @var string  */
    private $key;

    /** @var bool */
    private $hasDefault = false;

    /** @var mixed */
    private $defaultValue;

    /**
     * @param \ReflectionParameter $reflectionParameter
     */
    public function __construct(\ReflectionParameter $reflectionParameter)
    {
        $parameterName = $reflectionParameter->getName();
        if ($reflectionParameter->hasType() && !$reflectionParameter->getType()->isBuiltin()) {
            // If the parameter is typehinted attempt to fetch from the container using the namespace as a key
            $this->key           = $reflectionParameter->getClass()->getName();
        } else {
            // Try to get from the container using the parameter name as a key
            $this->key           = $parameterName;
            $this->hasDefault    = $reflectionParameter->isDefaultValueAvailable();
            $this->defaultValue  = $this->hasDefault ? $reflectionParameter->getDefaultValue() : null;
        }
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * @throws \RuntimeException
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        if($this->hasDefault !== true) {
            throw new \RuntimeException(sprintf('Param "%s" has no default value', $this->key));
        }
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            'key'           => $this->key,
            'hasDefault'    => $this->hasDefault,
            'defaultValue'  => $this->defaultValue,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->key           = $data['key'];
        $this->hasDefault    = $data['hasDefault'];
        $this->defaultValue  = $data['defaultValue'];
    }
}
