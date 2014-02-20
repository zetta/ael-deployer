<?php

namespace A3l\Deployer;

use Symfony\Component\EventDispatcher\Event;
use A3l\Deployer\Events\DeployEvents;

class ReputationLevelDeployer extends AbstractDeployer
{
    protected function attachEvents()
    {
        $this->addListener(DeployEvents::DEPLOY_CLONE, array($this,'clone'));
        $this->addListener(DeployEvents::DEPLOY_INSTALL, array($this,'install'));
    }

    /**
     * onExtract event
     * @param Event $event
     */
    protected function clone(Event $event)
    {
        // @todo
        //$this->output->writeln('<info>Checking for php errors</info>');
        //passthru("find . -name \\*.php -exec php -l \"{}\" \\; >> /dev/null ");

        $this->output->writeln('<info>Preparing installation</info>');
        copy("{$this->projectDir}/app/config/parameters.yml.dist", "{$this->projectDir}/app/config/parameters.yml");

        //change symlinks
        $json = json_decode(file_get_contents("{$this->projectDir}/composer.json"), true);
        unset($json['extra']['symfony-assets-install']);
        file_put_contents("{$this->projectDir}/composer.json", json_encode($json));

        passthru("composer install --optimize-autoloader --no-dev");
        passthru("composer update a3l/askatl --optimize-autoloader --no-dev");

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
    protected function install(Event $event)
    {
        $assetDir = 'web/bundles/reel/assets';
        $resourceDir = 'src/A3l/ReelBundle/Resources/public/assets';
        $vendorDir = 'vendor/keenthemes/metronic/template_content/assets/';

        $this->remoteCommand("rm /home/beta/${resourceDir}", 'Removing last assets');
        $this->remoteCommand("rm /home/beta/${assetDir}");
        $this->remoteCommand("ln -s /home/beta/${vendorDir} /home/beta/${resourceDir}", 'Installing new asset directory');
        $this->remoteCommand("ln -s /home/beta/${vendorDir} /home/beta/${assetDir}");
        $this->remoteCommand("php app/console cache:clear --env=prod", 'Clearing cache');
        $this->remoteCommand("chmod -R 777 app/cache/");
        $this->remoteCommand("crontab crontab", 'Installing new crontab');

    }

}