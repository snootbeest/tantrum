<?php

namespace SnootBeest\Tantrum\Route;

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