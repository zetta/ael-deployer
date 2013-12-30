<?php

namespace A3l\Deployer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use A3l\Deployer\Configurator;

class ProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:deploy')
            ->setDescription('Deploy a project')
            ->addArgument('project', InputArgument::REQUIRED, 'project to deploy')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurator = new Configurator();

        print_r($configurator->getConfig());

        /*
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
        */
    }
}