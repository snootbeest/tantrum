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

namespace SnootBeest\Tantrum\Test;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute;

/**
 * @coversDefaultClass SnootBeest\Tantrum\Controller
 */
class ControllerTest extends TestCase
{
    /**
     * @test
     * @covers ::setRequest
     */
    public function setRequestSucceeds()
    {
        $request = $this->mock(Request::class);
        $logger = $this->mock(LoggerInterface::class);
        $requiredParameter = uniqid();
        $controller = new MockRoute($logger, $requiredParameter);
        $controller->setRequest($request);

        self::assertSame($request, $this->getInaccessiblePropertyValue('request', $controller));
    }

    /**
     * @test
     * @covers ::setResponse
     */
    public function setResponseSucceeds()
    {
        $response = $this->mock(Response::class);
        $logger = $this->mock(LoggerInterface::class);
        $requiredParameter = uniqid();
        $controller = new MockRoute($logger, $requiredParameter);
        $controller->setResponse($response);

        self::assertSame($response, $this->getInaccessiblePropertyValue('response', $controller));
    }
}
