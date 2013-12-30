<?php

require_once "vendor/autoload.php";

chdir(dirname(__FILE__));

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();
$console->add(new A3l\Deployer\Command\ProjectCommand());
$console->add(new A3l\Deployer\Command\UserCommand());
$console->run();