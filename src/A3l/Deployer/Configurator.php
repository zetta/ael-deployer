<?php

namespace A3l\Deployer;

use Symfony\Component\Yaml\Yaml;

class Configurator
{

    const FILE_NAME = 'app/config/application.yml';

    protected $config;

    /**
     * Class Constructor
     * @param string $filename
     */
    public function __construct($filename = Configurator::FILE_NAME)
    {
        $this->config = Yaml::parse($filename);
    }

    /**
     * Return entire config file
     */
    public function getConfig()
    {
        return $this->config;
    }
}