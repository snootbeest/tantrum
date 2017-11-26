<?php
namespace SnootBeest\Tantrum\Exception;


use Psr\Log\LogLevel;

class BootstrapException extends ServerException
{
    const LOG_LEVEL = LogLevel::CRITICAL;
}