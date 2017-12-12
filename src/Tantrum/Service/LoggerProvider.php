<?php

namespace SnootBeest\Tantrum\Service;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerProvider extends ServiceProvider
{
    const KEY = LoggerInterface::class;

    /**
     * {@inheritdoc}
     * Returns a Loggerinteface instance
     */
    public function __invoke()
    {
        return new Logger(new NullHandler());
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public static function getKey(): string
    {
        return self::KEY;
    }
}