<?php

namespace A3l\Deployer;

use A3l\Deployer\Configurator;
use A3l\Deployer\Exception\ProjectNotFoundException;
use A3l\Deployer\Util\Inflector;
use Symfony\Component\EventDispatcher\Event;
//use Bandroidx\XMPPHP\XMPPHP_XMPP;

require_once 'vendor/bandroidx/xmpphp/XMPPHP/XMPP.php';

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
        $this->configurator = $configurator;
        $this->attachEvents();

    }


    protected function attachEvents()
    {
        $this->deployer->addListener(AbstractDeployer::EVENT_DEPLOY_PREPARE, array($this,'onPrepare'));
    }

    /**
     * onPrepare event
     * @param Event $event
     */
    public function onPrepare(Event $event)
    {

// $conn = new XMPPHP_XMPP('talk.google.com', 5222, 'reputationlevel', 'r3put4t10nD3f4ult', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
// echo "hola";
// try {
//     $conn->connect();
//     $conn->processUntil('session_start');
//     $conn->presence();
//     $conn->message('zetaweb@gmail.com', 'This is a test message!');
//     $conn->disconnect();
// } catch(XMPPHP_Exception $e) {
//     die($e->getMessage());
// }


//         $users = $this->configurator->getNotifyUsers();
//         foreach ($users as $alias => $email)
//         {

//         }
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