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

use Noodlehaus\ConfigInterface;
use SnootBeest\Tantrum\Config;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function constructSucceeds()
    {
        $values = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];
        $config = new Config($values);
        self::assertInstanceOf(ConfigInterface::class, $config);
        foreach($values as $key => $value) {
            self::assertTrue($config->has($key));
            self::assertEquals($value, $config->get($key));
        }
    }
}
