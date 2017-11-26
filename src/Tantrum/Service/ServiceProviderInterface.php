<?php

namespace SnootBeest\Tantrum\Service;

use Noodlehaus\ConfigInterface;


interface ServiceProviderInterface
{
    /**
     * Invoke the factory when it is called from the container
     * @return mixed
     */
    public function __invoke();

    /**
     * Set the config for the provider / dependency
     * @param ConfigInterface $config
     * @return void
     */
    public function setConfig(ConfigInterface $config);

    /**
     * Get the container key for this factory
     * @return string
     */
    public static function getKey(): string;
}