<?php

namespace A3l\Deployer\Notifier;

use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Message;

class NotifierManager
{
    protected $configurator;
    protected $mailer;

    public function __construct($configurator)
    {
        $this->configurator = $configurator;
        $transporter = \Swift_SmtpTransport::newInstance(
            $configurator->getConfig()['app']['mail']['host'],
            $configurator->getConfig()['app']['mail']['port'],
            $configurator->getConfig()['app']['mail']['security'])
                ->setUsername($configurator->getConfig()['app']['mail']['username'])
                ->setPassword($configurator->getConfig()['app']['mail']['password']);
        $this->mailer = \Swift_Mailer::newInstance($transporter);

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
    public function sendSummary($projectName, $username)
    {
        $content = file_get_contents($this->configurator->getLogFilename());

        // Create a message
        $message = \Swift_Message::newInstance(sprintf('Deploy [%s]', $projectName))
          ->setFrom(array($this->configurator->getConfig()['app']['mail']['username'] => sprintf('%s via deployer', $username)))
          ->setBody($content)
        ;

        $recipients = array();
        foreach ($this->configurator->getNotifyUsers() as $key =>  $user)
        {
            if (isset($user['email']))
                $recipients[$user['email']] = $key;
        }
        $message->setTo($recipients);

        // Send the message
        $result = $this->mailer->send($message);

    }

}