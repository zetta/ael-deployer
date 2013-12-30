<?php

namespace A3l\Deployer\Command;

use A3l\Deployer\Project;
use A3l\Deployer\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:admin')
            ->setDescription('User administration')
        ;
    }

    /**
     * Command Execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @todo **/
    }
}