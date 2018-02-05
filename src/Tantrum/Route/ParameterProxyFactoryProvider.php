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


use SnootBeest\Tantrum\Service\ServiceProvider;

class ParameterProxyFactoryProvider extends ServiceProvider
{
    CONST KEY = ParameterProxyFactoryInterface::class;

    /**
     * {@inheritdoc}
     * Returns a ParameterProxyFactoryInterface instance
     * @return ParameterProxyFactory
     */
    public function __invoke(): ParameterProxyFactoryInterface
    {
        return new ParameterProxyFactory();
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
