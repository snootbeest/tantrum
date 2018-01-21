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

namespace SnootBeest\Tantrum\Test\Exception;


use PHPUnit\Framework\AssertionFailedError;
use SnootBeest\Tantrum\Exception\TantrumException;

class ExceptionTestCase extends \SnootBeest\Tantrum\Test\TestCase
{
    static $exception;

    public function setUp()
    {
        echo self::$exception === null;
        if (self::$exception === null || (self::$exception instanceof TantrumException) === false) {
            throw new AssertionFailedError('$exception has not been defined properly');
        }
    }
}
