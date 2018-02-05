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

namespace SnootBeest\Tantrum\Test\Route;

use Snootbeest\Tantrum\Route\ConstructorProxy;
use SnootBeest\Tantrum\Route\ConstructorProxyInterface;
use Snootbeest\Tantrum\Route\MethodProxy;
use Snootbeest\Tantrum\Route\ParameterProxy;
use Snootbeest\Tantrum\Test\TestCase;

abstract class RouteTestCase extends TestCase
{
    const TYPEHINTED_PARAMETER_KEY        = 'Psr\Log\LoggerInterface';
    const REQUIRED_PARAMETER_KEY          = 'required';
    const OPTIONAL_PARAMETER_KEY          = 'optional';
    const OPTIONAL_PARAMETER_VALUE        = 'defaultValue';
    const SECOND_OPTIONAL_PARAMETER_KEY   = 'secondOptional';
    const SECOND_OPTIONAL_PARAMETER_VALUE = 'secondDefaultValue';

    const MOCK_ROUTE_NAMESPACE = 'SnootBeest\Tantrum\Test\ConcreteMock\Route\MockRoute';

    /**
     * Asserts that the unserialized constructor from MockRouteAncestor is correct
     * @param ConstructorProxyInterface $test
     */
    protected function assertConstructor(ConstructorProxyInterface $test)
    {
        self::assertInstanceOf(ConstructorProxy::class, $test);
        $params = $test->getParams();
        self::assertTrue(is_array($params));
        self::assertCount(4, $params);
        foreach($params as $parameterProxy) {
            self::assertInstanceOf(ParameterProxy::class, $parameterProxy);
        }
        $this->assertTypeHintedParameter($params[0], self::TYPEHINTED_PARAMETER_KEY);
        $this->assertRequiredParameter($params[1], self::REQUIRED_PARAMETER_KEY);
        $this->assertOptionalParameter($params[2], self::OPTIONAL_PARAMETER_KEY, self::OPTIONAL_PARAMETER_VALUE);
        $this->assertOptionalParameter($params[3], self::SECOND_OPTIONAL_PARAMETER_KEY, self::SECOND_OPTIONAL_PARAMETER_VALUE);
    }

    /**
     * Asserts that the first parameter of the constructor from MockRouteAncestor is correct
     * @param ParameterProxy $parameterProxy
     * @param mixed $expectedKey
     */
    protected function assertTypeHintedParameter(ParameterProxy $parameterProxy, $expectedKey)
    {
        self::assertEquals($expectedKey, $parameterProxy->getKey());
        self::assertFalse($parameterProxy->hasDefault());
    }

    /**
     *  Asserts that the second parameter of the constructor from MockRouteAncestor is correct
     * @param ParameterProxy $parameterProxy
     * @param mixed $expectedKey
     */
    protected function assertRequiredParameter(ParameterProxy $parameterProxy, $expectedKey)
    {
        self::assertEquals($expectedKey, $parameterProxy->getKey());
        self::assertFalse($parameterProxy->hasDefault());
    }

    /**
     * Asserts that the third parameter of the constructor from MockRouteAncestor is correct
     * @param ParameterProxy $parameterProxy
     * @param mixed $expectedKey
     * @param mixed $expectedValue
     */
    protected function assertOptionalParameter(ParameterProxy $parameterProxy, $expectedKey, $expectedValue)
    {
        self::assertEquals($expectedKey, $parameterProxy->getKey());
        self::assertTrue($parameterProxy->hasDefault());
        self::assertEquals($expectedValue, $parameterProxy->getDefaultValue());
    }

    /**
     * Asserts the methods from the controller have been proxied correctly
     * @param array MethodProxy[] $methodProxies
     * @param array MethodProxy[] $methodProxies
     */
    protected function assertMethodProxies(array $methodProxies)
    {
        self::assertCount(7, $methodProxies);
        self::assertArrayHasKey('testGetMethod', $methodProxies);
        $this->assertGetControllerMethod($methodProxies['testGetMethod']);
        self::assertArrayHasKey('testPostMethod', $methodProxies);
        $this->assertPostControllerMethod($methodProxies['testPostMethod']);
        self::assertArrayHasKey('testPatchMethod', $methodProxies);
        $this->assertPatchControllerMethod($methodProxies['testPatchMethod']);
        self::assertArrayHasKey('testDeleteMethod', $methodProxies);
        $this->assertDeleteControllerMethod($methodProxies['testDeleteMethod']);
        self::assertArrayHasKey('testPutMethod', $methodProxies);
        $this->assertPutControllerMethod($methodProxies['testPutMethod']);
        self::assertArrayHasKey('testOptionsMethod', $methodProxies);
        $this->assertOptionsControllerMethod($methodProxies['testOptionsMethod']);
        self::assertArrayHasKey('testGetAndHeadMethod', $methodProxies);
        $this->assertGetHeadControllerMethod($methodProxies['testGetAndHeadMethod']);
    }

    /**
     * Assert that the name, method and route are correct for the mock get route
     * @param MethodProxy $methodProxy
     */
    private function assertGetControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testGetMethod', $methodProxy->getName());
        self::assertTrue(in_array('GET', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/get', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock post route
     * @param MethodProxy $methodProxy
     */
    private function assertPostControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testPostMethod', $methodProxy->getName());
        self::assertTrue(in_array('POST', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/post', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock put route
     * @param MethodProxy $methodProxy
     */
    private function assertPutControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testPutMethod', $methodProxy->getName());
        self::assertTrue(in_array('PUT', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/put', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock patch route
     * @param MethodProxy $methodProxy
     */
    private function assertPatchControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testPatchMethod', $methodProxy->getName());
        self::assertTrue(in_array('PATCH', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/patch', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock delete route
     * @param MethodProxy $methodProxy
     */
    private function assertDeleteControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testDeleteMethod', $methodProxy->getName());
        self::assertTrue(in_array('DELETE', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/delete', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock options route
     * @param MethodProxy $methodProxy
     */
    private function assertOptionsControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testOptionsMethod', $methodProxy->getName());
        self::assertTrue(in_array('OPTIONS', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/options', $methodProxy->getRoute());
    }

    /**
     * Assert that the name, method and route are correct for the mock options route
     * @param MethodProxy $methodProxy
     */
    private function assertGetHeadControllerMethod(MethodProxy $methodProxy)
    {
        self::assertEquals('testGetAndHeadMethod', $methodProxy->getName());
        self::assertTrue(in_array('GET', $methodProxy->getMethods()));
        self::assertTrue(in_array('HEAD', $methodProxy->getMethods()));
        self::assertEquals('/mock/route/get/head', $methodProxy->getRoute());
    }
}
