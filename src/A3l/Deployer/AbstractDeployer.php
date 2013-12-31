<?php

namespace A3l\Deployer;

abstract class AbstractDeployer
{

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
    }

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
        chdir('..');
        if (!is_dir($this->config['path']))
            throw new \InvalidArgumentException("Invalid path ({$this->config['path']}) specified for project {$this->name}");
        chdir($this->config['path']);


        $log = exec('git log --pretty=format:"%h %an %ad %s" -n 1');

        $this->output->writeln('<comment>Deploying from</comment>');
        $this->output->writeln("<info>${log}</info>");


        $this->output->writeln('<comment>Creating archive</comment>');
        exec("git archive master | tar x -p -C {$this->projectDir}");

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
Author: ${username}
        ";
        file_put_contents("{$this->projectDir}/{$filename}", $content);
    }
}