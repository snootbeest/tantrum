<?php
namespace SnootBeest\Tantrum\Route;

use Psr\Log\LoggerInterface;

/**
 * Class RouteManager
 * @package SnootBeest\Tantrum\Route
 */
class RouteManager
{
    //@todo: This path needs to change
    const ROUTES_CACHE_PATH = '/cache/routes.bin';

    /** LoggerInterface $logger */
    private $logger;

    /**
     * @param $logger LoggerInterface
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Build the routes and save them to file
     * @param ControllerLocator      $controllerLocator
     * @param ControllerProxyFactory $controllerProxyFactory
     * @return array
     */
    public function generateRoutes(ControllerLocator $controllerLocator, ControllerProxyFactory $controllerProxyFactory): array
    {
        $routes = [];
        $controllerNamespaces = $controllerLocator->getControllerNamespaces();
        foreach($controllerNamespaces as $namespace) {
            try {
                $controllerProxy = $controllerProxyFactory->create($namespace);
                $routes[$controllerProxy->getClassName()] = $controllerProxy;
            } catch(\Exception $ex) {
                $this->logger->debug($ex->getMessage());
            }
        }

        return $routes;
    }
}