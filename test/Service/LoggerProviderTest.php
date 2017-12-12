<?php
namespace SnootBeest\Tantrum\Test\Service;

use Mockery\Mock;
use Monolog\Logger;
use Noodlehaus\ConfigInterface;
use Psr\Log\LoggerInterface;
use SnootBeest\Tantrum\Core\Config;
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
        self::assertInstanceOf(Logger::class, $logger);
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