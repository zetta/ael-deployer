<?php

namespace A3l\Deployer\Command;

use A3l\Deployer\Project;
use A3l\Deployer\Configurator;
use A3l\Deployer\Auth\PasswordValidator;
use A3l\Deployer\Auth\UserManager;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('User administration')
            ->addArgument('user-name', InputArgument::REQUIRED, 'username')
        ;
    }

    /**
     * Command Execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $password = $dialog->askHiddenResponse($output, 'please type the desired password: ', false);
        $passwordConfirm = $dialog->askHiddenResponse($output, 'confirm the password: ', false);
        $validator = new PasswordValidator($password, $passwordConfirm);
        if ($validator->isValid())
        {
            $manager = new UserManager();
            $manager->create($input->getArgument('user-name'), $password)->persist();
        }
    }
}