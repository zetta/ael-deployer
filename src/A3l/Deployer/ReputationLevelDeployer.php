<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;

class ReputationLevelDeployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(static::EVENT_DEPLOY_ON_EXTRACT, array($this,'onExtract'));
        $this->addListener(static::EVENT_DEPLOY_AFTER_SYNC, array($this,'afterSync'));
    }

    /**
     * onExtract event
     * @param Event $event
     */
    protected function onExtract(Event $event)
    {
        $this->output->writeln('<info>Preparing installation</info>');
        copy("{$this->projectDir}/app/config/parameters.yml.dist", "{$this->projectDir}/app/config/parameters.yml");

        //change symlinks
        $json = json_decode(file_get_contents("{$this->projectDir}/composer.json"), true);
        unset($json['extra']['symfony-assets-install']);
        file_put_contents("{$this->projectDir}/composer.json", json_encode($json));

        passthru("composer install --optimize-autoloader --no-dev");

        passthru("php app/console fos:js-routing:dump --env=prod");
        passthru("php app/console assetic:dump --env=prod");
        passthru("rm -Rf {$this->projectDir}/app/cache");
        passthru("rm -Rf {$this->projectDir}/app/logs");
        passthru("rm -v {$this->projectDir}/web/app_dev.php");

        unlink("{$this->projectDir}/app/config/parameters.yml");
    }

    /**
     * afterSync event
     * @param Event $event
     */
    protected function afterSync(Event $event)
    {
        $assetDir = 'web/bundles/reel/assets';
        $resourceDir = 'src/A3l/ReelBundle/Resources/public/assets';
        $vendorDir = 'vendor/keenthemes/metronic/template_content/assets/';

        $this->addPostCommand("rm /home/beta/${resourceDir}");
        $this->addPostCommand("rm /home/beta/${assetDir}");
        $this->addPostCommand("ln -s /home/beta/${vendorDir} /home/beta/${resourceDir}");
        $this->addPostCommand("ln -s /home/beta/${vendorDir} /home/beta/${assetDir}");
        $this->addPostCommand("php app/console cache:clear --env=prod");

    }

}