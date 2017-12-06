<?php
namespace SnootBeest\Tantrum;

use Noodlehaus\AbstractConfig;


class Config extends AbstractConfig
{
    /**
     * Set the config values
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}