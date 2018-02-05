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

namespace SnootBeest\Tantrum\Test\ConcreteMock\Route;


use Psr\Log\LoggerInterface;
use SnootBeest\Tantrum\Controller;

class MockRoute extends RouteAncestorClass
{
    /** @var LoggerInterface $logger */
    private $logger;

    /** @var string $required */
    private $required;

    /** @var string $optional */
    private $optional;

    /** @var string $secondOptional */
    private $secondOptional;

    /**
     * Add some constructor dependencies for testing
     * @param LoggerInterface $logger
     * @param string $required
     * @param string $optional
     * @param string $secondOptional
     */
    public function __construct(LoggerInterface $logger, string $required, string $optional = 'defaultValue', string $secondOptional = 'secondDefaultValue')
    {
        $this->logger         = $logger;
        $this->required       = $required;
        $this->optional       = $optional;
        $this->secondOptional = $secondOptional;
    }

    /**
     * @httpMethod GET
     * @route /mock/route/get
     */
    public function testGetMethod()
    {

    }

    /**
     * @httpMethod GET
     * @httpMethod HEAD
     * @route /mock/route/get/head
     */
    public function testGetAndHeadMethod()
    {

    }

    /**
     * @httpMethod POST
     * @route /mock/route/post
     */
    public function testPostMethod()
    {

    }

    /**
     * @httpMethod PATCH
     * @route /mock/route/patch
     */
    public function testPatchMethod()
    {

    }

    /**
     * @httpMethod DELETE
     * @route /mock/route/delete
     */
    public function testDeleteMethod()
    {

    }

    /**
     * @httpMethod PUT
     * @route /mock/route/put
     */
    public function testPutMethod()
    {

    }

    /**
     * @httpMethod OPTIONS
     * @route /mock/route/options
     */
    public function testOptionsMethod()
    {

    }
}
