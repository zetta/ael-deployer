<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractDeployer extends EventDispatcher
{
    const EVENT_DEPLOY_PREPARE    = 'evt.deploy.prepare';
    const EVENT_DEPLOY_INIT       = 'evt.deploy.init';
    const EVENT_DEPLOY_END        = 'evt.deploy.end';
    const EVENT_DEPLOY_ON_CANCEL  = 'evt.deploy.on.cancel';
    const EVENT_DEPLOY_ON_EXTRACT = 'evt.deploy.on.extract';

    protected $output;
    protected $input;
    protected $dialog;
    protected $name;
    protected $config;
    protected $projectDir;

    public function __construct($name, $config, $input, $output, $dialog)
    {
        $this->name = $name;
        $this->output = $output;
        $this->input = $input;
        $this->config = $config;
        $this->dialog = $dialog;
        $this->prepare();
        $this->attachEvents();
    }

    /**
     * Attach Events
     */
    protected abstract function attachEvents();

    /**
     * Prepares the temp directory
     */
    protected function prepare()
    {
        $root = '/tmp/deployment-workspace';
        $project = '/tmp/deployment-workspace/'.$this->name;
        if (!is_dir($root))
            mkdir($root);

        if (is_dir($project))
            exec('rm -Rf '.$project);

        mkdir($project);
        $this->projectDir = $project;
    }

    /**
     * Deployment job
     * @param string $username the user who runs the deploy job
     */
    public function deploy($username)
    {
        $this->dispatch(self::EVENT_DEPLOY_PREPARE);

        chdir('..');
        if (!is_dir($this->config['path']))
            throw new \InvalidArgumentException("Invalid path ({$this->config['path']}) specified for project {$this->name}");
        chdir($this->config['path']);


        $this->dispatch(self::EVENT_DEPLOY_INIT);

        $log = exec('git log --pretty=format:"%h %an %ad %s" -n 1');

        $this->output->writeln('<comment>Deploying from</comment>');
        $this->output->writeln("<info>${log}</info>");

        if (!$this->dialog->askConfirmation($this->output,'<question>Do you want to continue (y/n)?</question> ',false)) {
            $this->dispatch(self::EVENT_DEPLOY_ON_CANCEL);
            return;
        }

        $this->output->writeln('<comment>Creating archive</comment>');
        exec("git archive master | tar x -p -C {$this->projectDir}");

        chdir($this->projectDir);
        $this->dispatch(self::EVENT_DEPLOY_ON_EXTRACT);

        if (isset($this->config['rev']))
        {
            $this->createRevisionFile($this->config['rev'], $log, $username);
        }

        $this->output->writeln('<comment>Synchronizing</comment>');
        $command = sprintf('rsync -trzh --rsh=\'ssh -p %d\' %s/ %s@%s:/home/%3$s/',
                $this->config['port'],
                $this->projectDir,
                $this->config['user'],
                $this->config['host']
            );
        exec($command);

        $this->dispatch(self::EVENT_DEPLOY_END);

        $this->output->writeln('<comment>Cleaning workspace</comment>');
        exec('rm -Rf '.$this->projectDir);

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
}