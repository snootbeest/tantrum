<?php
namespace SnootBeest\Tantrum\Route;

use phpDocumentor\Reflection\DocBlock;

/**
 * Class MethodProxy
 * @package SnootBeest\Tantrum\Route
 */
class MethodProxy implements \Serializable
{
    /** @var array $allowedHttpMethods */
    private static $allowedHttpMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /** @var string $name */
    private $name;

    /** @var string $method */
    private $method;

    /** @var string $route */
    private $route;

    public function __construct(string $name, DocBlock $docBlock)
    {
        $this->name   = $name;
        $this->method = $this->getHttpRequestMethod($docBlock);
        $this->route  = $this->getPattern($docBlock);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
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
     * @throws \Exception
     * @return string
     */
    private function getHttpRequestMethod(DocBlock $docBlock): string
    {
        $annotations = $docBlock->getTagsByName('httpMethod');
        if(count($annotations) === 0) {
            throw new \Exception('No httpMethod annotation found');
        } elseif(count($annotations) > 1) {
            throw new \Exception('Only one httpMethod annotation is allowed');
        } elseif(!in_array($annotations[0], self::$allowedHttpMethods)) {
            throw new \Exception(sprintf('httpMethod "%s" is not allowed. Allowed methods are ["%s"]', $annotations[0], implode('","', self::$allowedHttpMethods)));
        } else {
            return $annotations[0]->__toString();
        }
    }

    /**
     * Get the pattern for named parameters from the docBlock
     * @param DocBlock $docBlock
     * @throws \Exception
     * @return string
     */
    private function getPattern(DocBlock $docBlock): string
    {
        $annotations = $docBlock->getTagsByName('route');
        if(count($annotations) === 1) {
            return $annotations[0]->__toString();
        } elseif(count($annotations) > 1) {
            throw new \Exception('Only one route annotation is allowed');
        } else {
            return '';
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function serialize()
    {
        return serialize([
            'name'   => $this->name,
            'method' => $this->method,
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
        $this->method = $data['method'];
        $this->route  = $data['route'];
    }
}