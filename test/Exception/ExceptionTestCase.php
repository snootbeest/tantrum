<?php

namespace SnootBeest\Tantrum\Test\Exception;


use PHPUnit\Framework\AssertionFailedError;
use SnootBeest\Tantrum\Exception\TantrumException;

class ExceptionTestCase extends \SnootBeest\Tantrum\Test\TestCase
{
    static $exception;

    public function setUp()
    {
        if (self::$exception === null || self::$exception instanceof TantrumException) {
            throw new AssertionFailedError('$exception has not been defined properly');
        }
    }
}