<?php

namespace A3l\Deployer\Command;

use A3l\Deployer\Project;
use A3l\Deployer\Configurator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:deploy')
            ->setDescription('Deploy a project')
            ->addArgument('project', InputArgument::OPTIONAL, 'project to deploy')
            ->addOption('list', 'l',InputOption::VALUE_NONE, 'project list')
        ;
    }

    /**
     * Command Execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('list'))
        {
            $configurator = new Configurator();
            $output->writeln('<info>Registered Projects</info>');
            $output->writeln($configurator->getProjectNames());
        }else{
            if (!$input->getArgument('project'))
                throw new \InvalidArgumentException('You must specify a project name to deploy');
            $project = new Project($input->getArgument('project'), $output);
            $project->deploy();
        }
    }
}