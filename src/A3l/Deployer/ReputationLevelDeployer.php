<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;

class ReputationLevelDeployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(static::EVENT_DEPLOY_ON_EXTRACT, array($this,'onExtract'));
    }

    /**
     * onStart event
     * @param Event $event
     */
    protected function onExtract(Event $event)
    {
        $this->output->writeln('<info>Preparing installation</info>');
        copy("{$this->projectDir}/app/config/parameters.yml.dist", "{$this->projectDir}/app/config/parameters.yml");
        passthru("composer install --optimize-autoloader --no-dev");

        passthru("php app/console fos:js-routing:dump --env=prod");
        passthru("php app/console assetic:dump --env=prod");
        passthru("rm -Rf {$this->projectDir}/app/cache");
        passthru("rm -Rf {$this->projectDir}/app/logs");

        unlink("{$this->projectDir}/app/config/parameters.yml");
    }

}