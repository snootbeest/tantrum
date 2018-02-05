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

namespace Snootbeest\Tantrum;

use SnootBeest\Tantrum\Service\CacheProvider;
use SnootBeest\Tantrum\Service\LoggerProvider;
use SnootBeest\Tantrum\Build\DocBlockFactoryProvider;
use SnootBeest\Tantrum\Build\ReflectionFactoryProvider;
use SnootBeest\Tantrum\Route\ControllerLocatorProvider;
use SnootBeest\Tantrum\Route\ControllerProxyFactoryProvider;
use SnootBeest\Tantrum\Route\ConstructorProxyFactoryProvider;
use SnootBeest\Tantrum\Route\MethodProxyFactoryProvider;
use SnootBeest\Tantrum\Route\ParameterProxyFactoryProvider;

return [
    // For logging
    LoggerProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS  => LoggerProvider::class,
    ],
    // For the build process
    DocBlockFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => DocBlockFactoryProvider::class,
    ],
    ReflectionFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => ReflectionFactoryProvider::class,
    ],
    ControllerProxyFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => ControllerProxyFactoryProvider::class,
        Application::CONFIG_KEY_DEPENDENCIES => [
            LoggerProvider::getKey(),
            ReflectionFactoryProvider::getKey(),
            ConstructorProxyFactoryProvider::getKey(),
            MethodProxyFactoryProvider::getKey(),
        ],
    ],
    ConstructorProxyFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => ConstructorProxyFactoryProvider::class,
        Application::CONFIG_KEY_DEPENDENCIES => [
            ParameterProxyFactoryProvider::getKey(),
        ],
    ],
    MethodProxyFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => MethodProxyFactoryProvider::class,
        Application::CONFIG_KEY_DEPENDENCIES => [
            DocBlockFactoryProvider::getKey(),
        ],
    ],
    ParameterProxyFactoryProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => ParameterProxyFactoryProvider::class
    ],
    CacheProvider::getKey() => [
        Application::CONFIG_KEY_PROVIDER_CLASS => CacheProvider::class,
    ],
];
