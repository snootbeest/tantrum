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

namespace SnootBeest\Tantrum\Test\Buid;


use phpDocumentor\Reflection\DocBlockFactory;
use SnootBeest\Tantrum\Build\DocBlockFactoryProvider;
use SnootBeest\Tantrum\Test\TestCase;

class DocBlockFactoryProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getKeySucceeds()
    {
        self::assertEquals(DocBlockFactoryProvider::KEY, DocBlockFactoryProvider::getKey());
    }

    /**
     * @test
     */
    public function __invokeSucceeds()
    {
        $docBlockFactoryProvider = new DocBlockFactoryProvider();
        $docBlockFactory = $docBlockFactoryProvider();

        self::assertInstanceOf(DocBlockFactory::class, $docBlockFactory);
    }
}
