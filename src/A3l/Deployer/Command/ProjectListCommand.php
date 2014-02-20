<?php

namespace A3l\Deployer\Command;

use A3l\Deployer\Project;
use A3l\Deployer\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:list')
            ->setDescription('list the configured projects')
        ;
    }

    /**
     * Command Execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurator = new Configurator();
        $output->writeln('<info>Registered Projects</info>');
        foreach ($configurator->getProjectNames() as $project) {
            $output->writeln("<comment>${project}</comment>");
        }
    }
}