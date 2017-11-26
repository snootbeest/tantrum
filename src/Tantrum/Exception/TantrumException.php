<?php
namespace SnootBeest\Tantrum\Exception;

use Psr\Log\LogLevel;

abstract class TantrumException extends \Exception
{
    const LOG_LEVEL = LogLevel::DEBUG;

    /**
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, int $code, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the log level for this exception
     * Uses late static binding to fetch the constant from the youngest ancestor
     * @return string
     */
    public function getLogLevel(): string
    {
        return static::LOG_LEVEL;
    }
}