<?php

namespace SnootBeest\Tantrum\Service;

use Noodlehaus\ConfigInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    /** @var ConfigInterface $config */
    protected $config;

    /**
     * A config object for this factory
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }
}