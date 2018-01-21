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
