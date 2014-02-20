<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use A3l\Deployer\Events\DeployEvents;

abstract class AbstractDeployer extends EventDispatcher
{
    const WORKSPACE = '/tmp/deployment-workspace';

    protected $output;
    protected $input;
    protected $dialog;
    protected $name;
    protected $username;
    protected $config;
    protected $notifier;
    protected $projectDir;
    protected $sshCommands = array();
    protected $gitLog = '';

    public function __construct($name, $config, $input, $output, $dialog, $notifier)
    {
        $this->name = $name;
        $this->output = $output;
        $this->input = $input;
        $this->config = $config;
        $this->dialog = $dialog;
        $this->notifier = $notifier;
        $this->prepareWorkspace();
        $this->attachBaseEvents();
        $this->attachEvents();
    }

    /**
     * Attach Events
     */
    protected abstract function attachEvents();

    /**
     * Prepares the temp directory
     */
    protected function prepareWorkspace()
    {
        $root = self::WORKSPACE;
        $project = self::WORKSPACE.'/'.$this->name;
        if (!is_dir($root))
            mkdir($root);

        if (is_dir($project))
            exec('rm -Rf '.$project);

        mkdir($project);
        $this->projectDir = $project;
    }

    /**
     * Base abstract events
     */
    private function attachBaseEvents()
    {
        $this->addListener(DeployEvents::DEPLOY_PREPARE, array($this,'onEventPrepare'), 100);
        $this->addListener(DeployEvents::DEPLOY_START,   array($this,'onEventStart'),   100);
        $this->addListener(DeployEvents::DEPLOY_CANCEL,  array($this,'onEventCancel'),  100);
        $this->addListener(DeployEvents::DEPLOY_CLONE,   array($this,'onEventClone'),   100);
        $this->addListener(DeployEvents::DEPLOY_SYNC,    array($this,'onEventSync'),    100);
        $this->addListener(DeployEvents::DEPLOY_INSTALL, array($this,'onEventInstall'), 100);
        $this->addListener(DeployEvents::DEPLOY_END,     array($this,'onEventEnd'),     100);
    }

    /**
     * OnPrepare
     */
    protected final function onEventPrepare()
    {
        chdir('..');
        $path = $this->config['base-path'].$this->config['path'];
        if (!is_dir($path))
            throw new \InvalidArgumentException("Invalid path (${path}) specified for project {$this->name}");
        chdir($path);
    }

    /**
     * On Start Event
     */
    protected final function onEventStart()
    {
        $log = exec('git log --pretty=format:"%h %an %ad %s" -n 1');
        $this->output->writeln('<info>Are you sure you want to deploy from?</info>');
        $this->output->writeln("<comment>${log}</comment>");
        $this->gitLog;
    }


    protected final function onEventCancel()
    {
        $this->output->writeln('<info>Deploy canceled by user</info>');
    }

    protected final function onEventClone()
    {
        $this->output->writeln('<comment>Creating archive</comment>');
        exec("git archive master | tar x -p -C {$this->projectDir}");
        chdir($this->projectDir);
        if (isset($this->config['rev']))
        {
            $this->createRevisionFile($this->config['rev'], $this->gitLog, $this->username);
        }
    }

    protected final function onEventEnd()
    {
        $this->output->writeln('<comment>Cleaning workspace</comment>');
        exec('rm -Rf '.$this->projectDir);
    }

    protected final function onEventSync()
    {
        $this->output->writeln('<comment>Synchronizing</comment>');
        $command = sprintf('rsync -trzhlv --rsh=\'ssh -p %d \' %s/ %s@%s:/home/%3$s/',
                $this->config['port'],
                $this->projectDir,
                $this->config['user'],
                $this->config['host']
            );
        exec($command);
    }

    protected final function onEventInstall()
    {
        // no install commands
    }

    /**
     * Deployment job
     * @param string $username the user who runs the deploy job
     */
    public function deploy($username)
    {
        $this->username = $username;
        $this->dispatch(DeployEvents::DEPLOY_PREPARE);
        $this->dispatch(DeployEvents::DEPLOY_START);

        if (!$this->dialog->askConfirmation($this->output,'<question>Do you want to continue (y/n)?</question> ',false)) {
            $this->dispatch(DeployEvents::DEPLOY_CANCEL);
            $this->dispatch(DeployEvents::DEPLOY_END);
            return;
        }

        $this->dispatch(DeployEvents::DEPLOY_CLONE);
        $this->dispatch(DeployEvents::DEPLOY_SYNC);
        $this->dispatch(DeployEvents::DEPLOY_INSTALL);

        //$this->runPostCommands();
        $this->dispatch(DeployEvents::DEPLOY_END);
    }

    /**
     * Create Revision File
     */
    protected function createRevisionFile($filename, $log, $username)
    {
        $this->output->writeln("<comment>Writing rev file (${filename})</comment>");
        exec("touch {$this->projectDir}/{$filename}");
        $date = date('r');
        $content =
"Deployed: ${date}
Revision: ${log}
Deployer: ${username}
        ";
        file_put_contents("{$this->projectDir}/{$filename}", $content);
    }

    /**
     * Run remote command on server
     * @param string $command
     * @param string $title
     */
    protected function remoteCommand($command, $title = null)
    {
        if ($title)
            $this->output->writeln(sprintf('<info>%s</info>', $title));
        $sshCommand = sprintf("ssh -p %d %s@%s '%s'",
            $this->config['port'],
            $this->config['user'],
            $this->config['host'],
            $command
        );
        passthru($sshCommand);
    }

}