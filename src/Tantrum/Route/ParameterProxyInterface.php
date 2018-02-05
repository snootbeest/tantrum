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


interface ParameterProxyInterface extends \Serializable
{
    /**
     * Provision the ReflectionParameter object
     * @param \ReflectionParameter $reflectionParameter
     */
    public function __construct(\ReflectionParameter $reflectionParameter);

    /**
     * Return the name of the parameter
     * @return string
     */
    public function getKey(): string;

    /**
     * Has this parameter got a default value?
     * @return bool
     */
    public function hasDefault(): bool;

    /**
     * Get the default value for the parameter
     * @throws \RuntimeException
     * @return mixed | null
     */
    public function getDefaultValue();

    /**
     * {@inheritdoc}
     * @return string
     */
    public function serialize(): string;

    /**
     * {@inheritdoc}
     * @param string $serialized
     */
    public function unserialize($serialized);
}
