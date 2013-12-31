<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;

class Deployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(static::EVENT_DEPLOY_INIT, array($this,'onStart'));
    }

    /**
     * onStart event
     * @param Event $event
     */
    protected function onStart(Event $event)
    {
        $this->output->writeln('<info>Generic DeployJob starting</info>');
    }
}