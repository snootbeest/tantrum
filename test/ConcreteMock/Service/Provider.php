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

namespace SnootBeest\Tantrum\Test\ConcreteMock\Service;

use SnootBeest\Tantrum\Service\ServiceProvider;

class Provider extends ServiceProvider
{
    const KEY = ProvidedInterface::class;

    /** @var SubDependency $dependency */
    private $dependency;

    /**
     * Provision a dependency object
     * @param SubDependency $dependency
     */
    public function __construct(SubDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @inheritdoc
     */
    public function __invoke()
    {
        $providedService = new Provided($this->config->get('constructorValue'));
        $providedService->setValue($this->config->get('setterValue'));
        return $providedService;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function getKey():string
    {
        return self::KEY;
    }
}
