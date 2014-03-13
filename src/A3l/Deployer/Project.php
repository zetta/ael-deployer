<?php

namespace A3l\Deployer;

use A3l\Deployer\Configurator;
use A3l\Deployer\Notifier;
use A3l\Deployer\Exception\ProjectNotFoundException;
use A3l\Deployer\Util\Inflector;
use A3l\Deployer\Notifier\NotifierManager;
use Symfony\Component\EventDispatcher\Event;
use A3l\Deployer\Events\DeployEvents;

class Project
{

    /**
     * @var AbstractDeployer
     */
    protected $deployer;

    protected $output;
    protected $input;
    protected $configurator;

    /**
     * Class Constructor
     */
    public function __construct($name, $input, $output, $dialog)
    {
        $configurator = new Configurator();
        $output->startLog($configurator->getLogFilename());
        $output->writeln('<info>Looking for project configuration</info>');


        if (!$configurator->hasProject($name))
            throw new ProjectNotFoundException("Project ${name} isn't configured");

        $config = $configurator->getProjectConfiguration($name);
        $output->writeln("<comment>Initializing deployment for ${name}</comment>");

        $inflector = new Inflector();
        $classname = $inflector->camelize($name);
        $classname = "A3l\\Deployer\\${classname}Deployer";
        $notifier = new NotifierManager($configurator);

        if (class_exists($classname))
            $this->deployer = new $classname($name, $config, $input, $output, $dialog, $notifier);
        else
            $this->deployer = new Deployer($name, $config, $input, $output, $dialog, $notifier);

        $this->output = $output;
        $this->input = $input;
        $this->notifier = $notifier;
        $this->configurator = $configurator;
        $this->attachEvents();

    }


    protected function attachEvents()
    {
        $this->deployer->addListener(DeployEvents::DEPLOY_PREPARE, array($this,'onPrepare'));
    }

    /**
     * onPrepare event
     * @param Event $event
     */
    public function onPrepare(Event $event)
    {
        #nothing
    }

    /**
     *
     */
    public function deploy($projectName, $username)
    {
        $this->output->writeln(sprintf('<info>$username starts a new deploy job</info>', $username));
        $this->deployer->deploy($username);
        $this->output->writeln('<info>Deploy job end</info>');
        $this->notifier->sendSummary($projectName, $username);
    }

}