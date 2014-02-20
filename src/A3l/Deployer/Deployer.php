<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;
use A3l\Deployer\Events\DeployEvents;

class Deployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(DeployEvents::DEPLOY_PREPARE, array($this,'prepare'));
    }

    /**
     * onStart event
     * @param Event $event
     */
    protected function prepare(Event $event)
    {
        $this->output->writeln('<info>Generic DeployJob starting</info>');
    }
}