<?php

require_once "vendor/autoload.php";

chdir(dirname(__FILE__));

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();
$console->addCommands(array(
    new A3l\Deployer\Command\ProjectListCommand(),
    new A3l\Deployer\Command\ProjectDeployCommand(),
    new A3l\Deployer\Command\UserCreateCommand()
));
$console->run();