<?php
namespace SnootBeest\Tantrum\Exception;

use Psr\Log\LogLevel;

abstract class ServerException extends TantrumException
{
    const LOG_LEVEL                  = LogLevel::ERROR;
    const CODE_INTERNAL_SERVER_ERROR = 500;

    /**
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message = 'An internal server error has occurred', int $code = self::CODE_INTERNAL_SERVER_ERROR, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}