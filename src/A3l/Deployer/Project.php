<?php

namespace A3l\Deployer;

use A3l\Deployer\Configurator;
use A3l\Deployer\Exception\ProjectNotFoundException;
use A3l\Deployer\Util\Inflector;

class Project
{

    /**
     * @var AbstractDeployer
     */
    protected $deployer;

    protected $output;
    protected $input;
    protected $configuration;

    /**
     * Class Constructor
     */
    public function __construct($name, $input, $output, $dialog)
    {
        $output->writeln('<info>Looking for project configuration</info>');

        $configurator = new Configurator();
        if (!$configurator->hasProject($name))
            throw new ProjectNotFoundException("Project ${name} isn't configured", 1);

        $config = $configurator->getProjectConfiguration($name);
        $output->writeln("<comment>Initializing deployment for ${name}</comment>");

        $inflector = new Inflector();
        $classname = $inflector->camelize($name);
        $classname = "A3l\\Deployer\\${classname}Deployer";

        if (class_exists($classname))
            $this->deployer = new $classname($name, $config, $input, $output, $dialog);
        else
            $this->deployer = new Deployer($name, $config, $input, $output, $dialog);

        $this->output = $output;
        $this->input = $input;
    }

    /**
     *
     */
    public function deploy($username)
    {
        $this->output->writeln('<info>Deploy job start</info>');
        $this->deployer->deploy($username);
        $this->output->writeln('<info>Deploy job end</info>');
    }

}