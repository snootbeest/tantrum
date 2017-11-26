<?php
namespace SnootBeest\Tantrum\Test\Exception;


use Psr\Log\LogLevel;
use SnootBeest\Tantrum\Exception\BootstrapException;

class BootstrapTest extends ExceptionTestCase
{
    use ServerExceptionTrait;

    /**
     * @test
     */
    public function getLogLevelSucceeds()
    {
        self::assertEquals(self::$exception->getLogLevel(), LogLevel::CRITICAL);
    }

    public function setUp()
    {
        self::$exception = new BootstrapException();
    }
}