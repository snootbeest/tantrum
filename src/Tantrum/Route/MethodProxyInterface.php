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


use phpDocumentor\Reflection\DocBlock;

interface MethodProxyInterface extends \Serializable
{
    /**
     * Provision the name and DocBlock
     * The name is provided by the factory, as it has access to the ReflectionMethod
     * @param string $name
     * @param DocBlock $docBlock
     */
    public function __construct(string $name, DocBlock $docBlock);

    /**
     * Get the method name
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the HTTP methods
     * @return array
     */
    public function getMethods(): array;

    /**
     * Get the route pattern to which this method corresponds.
     * @return string
     */
    public function getRoute(): string;

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize(): string;

    /**
     * @inheritdoc
     * @param string $serialized
     */
    public function unserialize($serialized);
}
