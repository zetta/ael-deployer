<?php

namespace A3l\Deployer\Command;

use A3l\Deployer\Project;
use A3l\Deployer\Configurator;
use A3l\Deployer\Auth\Authentication;
use A3l\Deployer\Auth\UserManager;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectDeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('project:deploy')
            ->setDescription('Deploy a project')
            ->addArgument('project', InputArgument::REQUIRED, 'project to deploy')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'username')
        ;
    }

    /**
     * Command Execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $username = $input->getOption('username');
        if (!$username)
            $username = $dialog->ask($output, 'username: ');
        $password = $dialog->askHiddenResponse($output, 'password: ', false);

        $auth = new Authentication(new UserManager());
        $auth->login($username, $password);

        $project = new Project($input->getArgument('project'), $input, $output, $this->getHelperSet()->get('dialog'));
        $project->deploy($username);
    }
}