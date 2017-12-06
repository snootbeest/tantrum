<?php
namespace SnootBeest\Tantrum\Route;

/**
 * Class ControllerLocator
 * This class is responsible for iterating into the routes directory and locating instantiable controllers
 * @package SnootBeest\Tantrum\Route
 */
class ControllerLocator
{
    /** @var string  */
    private $path;

    /**
     * ControllerLocator constructor.
     * @param $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }
    /**
     * Recursively get all classes within the given path
     * @return array An array of namespaces
     */
    public function getControllerNamespaces(): array
    {
        $namespaces = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $path => $fileInfo) {

            // Filter out any swap files etc
            if($fileInfo->getExtension() === 'php') {
                // Get the namespace of the class for autoloading
                $relativePath = substr($path, strpos($path, 'search-api'), strlen($path));
                $relativePathWithoutExtension = substr($relativePath, 0, -4);
                // @todo: Refactor this line so it's testable using the test/Build/Route/Mock directory
                $escapedNamespace = 'WestwingNow\\Search'.str_replace('search-api/src/', '\\', $relativePathWithoutExtension);
                $namespaces[] = str_replace('/', '\\', $escapedNamespace);
            }
        }

        return $namespaces;
    }
}