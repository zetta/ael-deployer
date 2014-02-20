<?php

namespace A3l\Deployer\Notifier;

use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Message;

class NotifierManager
{

    protected $configurator;

    public function __construct($configurator)
    {
        $this->configurator = $configurator;

/*
        $options = new Options('tcp://talk.google.com:5222');
        $options->setUsername('reputationlevel')
            ->setPassword('r3put4t10nD3f4ult');



            $client = new Client($options);
// optional connect manually
//xdebug_break();
$client->connect();


// send a message to another user
$message = new Message;
$message->setMessage('test')
    ->setTo('zetaweb@gmail.com');
$client->send($message);

    $client->disconnect();
*/
    }

    public function sendMessage($message)
    {

    }

    /**
     * @todo
     */
    public function sendSummary()
    {

    }

}