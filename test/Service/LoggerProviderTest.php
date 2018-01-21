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

namespace SnootBeest\Tantrum\Test\Service;

use Mockery\Mock;
use Monolog\Logger;
use Noodlehaus\ConfigInterface;
use Psr\Log\LoggerInterface;
use SnootBeest\Tantrum\Config;
use SnootBeest\Tantrum\Service\LoggerProvider;
use SnootBeest\Tantrum\Test\TestCase;

/**
 * @coversDefaultClass \SnootBeest\Tantrum\Service\LoggerProvider
 */
class LoggerProviderTest extends TestCase
{
    /**
     * @test
     * @covers ::__invoke
     */
    public function invokeSucceeds()
    {
        $loggerProvider = new LoggerProvider();
        $logger = $loggerProvider();
        self::assertInstanceOf(LoggerInterface::class, $logger);
    }

    /**
     * @test
     * @covers ::getKey
     */
    public function getKeySucceeds()
    {
        self::assertEquals(LoggerInterface::class, LoggerProvider::getKey());
    }

    /**
     * @test
     * @covers ::setConfig
     */
    public function setConfigSucceeds()
    {
        /** @var Mock | ConfigInterface $mockConfig */
        $mockConfig = \Mockery::mock(Config::class);
        $loggerProvider = new LoggerProvider();
        $loggerProvider->setConfig($mockConfig);
        self::assertSame($mockConfig, $this->getInaccessiblePropertyValue('config', $loggerProvider));
    }
}
