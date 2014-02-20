<?php

namespace A3l\Deployer\Events;

/**
 * Deploy Events
 */
final class DeployEvents
{

    /**
     * evt.deploy.prepare
     *
     * @var string
     */
    const DEPLOY_PREPARE = 'evt.deploy.prepare';

    /**
     * evt.deploy.start
     *
     * @var string
     */
    const DEPLOY_START = 'evt.deploy.start';


    /**
     * evt.deploy.cancel
     *
     * @var string
     */
    const DEPLOY_CANCEL = 'evt.deploy.cancel';

    /**
     * evt.deploy.clone
     *
     * @var string
     */
    const DEPLOY_CLONE = 'evt.deploy.clone';

    /**
     * evt.deploy.sync
     *
     * @var string
     */
    const DEPLOY_SYNC = 'evt.deploy.sync';

    /**
     * evt.deploy.install
     *
     * @var string
     */
    const DEPLOY_INSTALL = 'evt.deploy.install';

    /**
     * evt.deploy.end
     *
     * @var string
     */
    const DEPLOY_END = 'evt.deploy.end';

}