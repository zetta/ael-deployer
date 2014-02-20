<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;
use A3l\Deployer\Events\DeployEvents;

class TestDeployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(DeployEvents::DEPLOY_SYNC, array($this,'onSync'), 200);
    }

    /**
     * OnSync
     */
    protected function onSync(Event $event)
    {
        $event->stopPropagation();
        $this->output->writeln('<comment>This deploy doesnt sync</comment>');
    }

}