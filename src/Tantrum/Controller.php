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

namespace SnootBeest\Tantrum;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Controller
 * @package SnootBeest\Tantrum\Route
 */
abstract class Controller
{
    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /**
     * Set the RequestInterface object
     * @param Request $request
     * @return Controller
     */
    final public function setRequest(Request $request): Controller
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set the response object
     * @param Response $response
     * @return Controller
     */
    final public function setResponse(Response $response): Controller
    {
        $this->response  = $response;
        return $this;
    }
}
