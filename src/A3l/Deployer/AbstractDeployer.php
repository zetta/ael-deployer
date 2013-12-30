<?php

namespace A3l\Deployer;

abstract class AbstractDeployer
{

    protected $output;
    protected $name;
    protected $config;
    protected $projectDir;

    public function __construct($name, $config, $output)
    {
        $this->name = $name;
        $this->output = $output;
        $this->config = $config;
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
     */
    public function deploy()
    {
        chdir('..');
        if (!is_dir($this->config['path']))
            throw new \InvalidArgumentException("Invalid path ({$this->config['path']}) specified for project {$this->name}");
        chdir($this->config['path']);


        $log = exec('git log --pretty=format:"%h%x09%an%x09%ad%x09%s" -n 1');

        $this->output->writeln('<comment>Deploying from</comment>');
        $this->output->writeln("<info>${log}</info>");


        $this->output->writeln('<comment>Creating archive</comment>');
        exec("git archive master | tar x -p -C {$this->projectDir}");


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
}