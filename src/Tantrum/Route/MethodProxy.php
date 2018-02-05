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

namespace SnootBeest\Tantrum\Route;

use phpDocumentor\Reflection\DocBlock;
use SnootBeest\Tantrum\Exception\BuildException;

/**
 * Class MethodProxy
 * @package SnootBeest\Tantrum\Route
 */
class MethodProxy implements MethodProxyInterface
{
    /** @var array $allowedHttpMethods */
    private static $allowedHttpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

    /** @var string $name */
    private $name;

    /** @var array $methods */
    private $methods;

    /** @var string $route */
    private $route;

    /**
     * {@inheritdoc}
     * @param string $name
     * @param DocBlock $docBlock
     */
    public function __construct(string $name, DocBlock $docBlock)
    {
        $this->name    = $name;
        $this->methods = $this->getHttpRequestMethods($docBlock);
        $this->route   = $this->getPattern($docBlock);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Get the http request method from the docBlock
     * @param DocBlock $docBlock
     * @throws BuildException
     * @return array
     */
    private function getHttpRequestMethods(DocBlock $docBlock): array
    {
        $tags = $docBlock->getTagsByName('httpMethod');
        $annotations = [];
        foreach($tags as $tag) {
            $annotations[] = $tag->getDescription()->__toString();
        }

        $badMethods  = array_diff($annotations, self::$allowedHttpMethods);
        if(count($annotations) === 0) {
            throw new BuildException('No httpMethod annotation found');
        } elseif(count($badMethods) > 0) {
            $plural = count($badMethods) > 1;
            throw new BuildException(sprintf('HTTP methods ["%s"] are not allowed. Allowed methods are ["%s"]',
                implode('"," ', $annotations), implode('", "', self::$allowedHttpMethods)));
        }

        return $annotations;
    }

    /**
     * Get the pattern for named parameters from the docBlock
     * @param DocBlock $docBlock
     * @throws BuildException
     * @return string
     */
    private function getPattern(DocBlock $docBlock): string
    {
        $annotations = $docBlock->getTagsByName('route');
        if(count($annotations) === 1) {
            return $annotations[0]->__toString();
        } elseif(count($annotations) > 1) {
            throw new BuildException('Only one route annotation is allowed');
        } else {
            return '';
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            'name'   => $this->name,
            'methods' => $this->methods,
            'route'  => $this->route,
        ]);
    }

    /**
     * @inheritdoc
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->name   = $data['name'];
        $this->methods = $data['methods'];
        $this->route  = $data['route'];
    }
}
