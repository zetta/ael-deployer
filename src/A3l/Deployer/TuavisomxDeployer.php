<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;
use A3l\Deployer\Events\DeployEvents;

class TuavisomxDeployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(DeployEvents::DEPLOY_INSTALL, array($this,'install'));
    }

    /**
     * afterSync event
     * @param Event $event
     */
    protected function install(Event $event)
    {
        $this->remoteCommand("crontab crontab", 'Installing new crontab');
    }
}