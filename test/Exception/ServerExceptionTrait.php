<?php
namespace SnootBeest\Tantrum\Test\Exception;

use SnootBeest\Tantrum\Exception\ServerException;
use SnootBeest\Tantrum\Exception\TantrumException;

trait ServerExceptionTrait
{
    /** @var  ServerException $exception */
    static $exception;

    /**
     * @test
     * @expectedException \SnootBeest\Tantrum\Exception\ServerException
     * @expectedExceptionMessage An internal server error has occurred
     * @expectedExceptionCode 500
     */
    public function throwServerExceptionHasCorrectDefaults()
    {
        self::assertInstanceOf(TantrumException::class, self::$exception);
        throw self::$exception;
    }
}