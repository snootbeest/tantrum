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
 * Class ReflectionFactory
 * @package SnootBeest\Tantrum\Route
 */
class ReflectionFactory implements ReflectionFactoryInterface
{
    /**
     * {@inheritdoc}
     * @param string $namespace
     * @return \ReflectionClass
     */
    public function create($namespace): \ReflectionClass
    {
        return new \ReflectionClass($namespace);
    }
}
