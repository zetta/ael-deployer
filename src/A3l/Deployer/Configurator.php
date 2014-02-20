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

    /**
     * Is the project defined?
     * @param string $name
     * @return boolean
     */
    public function hasProject($name)
    {
        return isset($this->config['projects'][$name]);
    }

    /**
     * Returns project configuration
     * @param string name
     * @return array
     */
    public function getProjectConfiguration($name)
    {
        return array_merge($this->config['project'], $this->config['projects'][$name]);
    }

    /**
     * Return the registered project names
     * @return array
     */
    public function getProjectNames()
    {
        return array_keys($this->config['projects']);
    }

    /**
     * Returns the users who need to notiy when a deployment starts
     * @return array
     */
    public function getNotifyUsers()
    {
        return $this->config['users']['notify'];
    }

    /**
     * get the log filename
     * @return string
     */
    public function getLogFilename()
    {
        return isset($this->config['app']['log']) ? $this->config['app']['log'] : null;
    }

}