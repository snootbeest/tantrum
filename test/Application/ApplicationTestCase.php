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

namespace SnootBeest\Tantrum\Test\Application;


use SnootBeest\Tantrum\Test\TestCase;
use Noodlehaus\Config;
use Mockery;
use Interop\Container\ContainerInterface;

class ApplicationTestCase extends TestCase
{
    /** @var  Config | Mockery\Mock $this->mockConfig */
    protected $mockConfig;

    // Utility methods

    public function setUp()
    {
        $this->mockConfig = Mockery::mock(Config::class);
        parent::setUp();
    }

    /**
     * Checks that the default dependencies have been loaded correctly
     * @param ContainerInterface $container
     */
    protected function assertDefaultDependencies(ContainerInterface $container)
    {
        $defaultDependencies = include(realpath(__DIR__ . '/../../src/Tantrum/defaultDependencies.php'));
        foreach($defaultDependencies as $key => $dependency) {
            self::assertTrue($container->has($key));
            self::assertInstanceOf($key, $container->get($key));
        }
    }
}
