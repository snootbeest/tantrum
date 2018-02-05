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

namespace SnootBeest\Tantrum\Build;


use SnootBeest\Tantrum\Route\ReflectionFactory;
use SnootBeest\Tantrum\Route\ReflectionFactoryInterface;
use SnootBeest\Tantrum\Service\ServiceProvider;

class ReflectionFactoryProvider extends ServiceProvider
{
    const KEY = ReflectionFactoryInterface::class;

    /**
     * {@inheritdoc}
     * Returns a ReflectionFactoryInterface instance
     * @return ReflectionFactoryInterface
     */
    public function __invoke(): ReflectionFactoryInterface
    {
        return new ReflectionFactory();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public static function getKey(): string
    {
        return self::KEY;
    }
}
