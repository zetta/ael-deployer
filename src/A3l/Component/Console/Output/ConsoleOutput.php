<?php

namespace A3l\Component\Console\Output;

use Symfony\Component\Console\Output\ConsoleOutput as BaseOutput;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * ConsoleOutput is the default class for all CLI output. It uses STDOUT.
 *
 * This class is a convenient wrapper around `StreamOutput`.
 *
 *     $output = new ConsoleOutput();
 *
 * This is equivalent to:
 *
 *     $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * @author zetta
 *
 * @api
 */
class ConsoleOutput extends BaseOutput
{

    protected $logHandler;

    /**
     * Starts the log
     */
    public function startLog($filename)
    {
        if ($filename)
            $this->logHandler = fopen($filename, 'w');
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        $messages = (array) $messages;
        foreach ($messages as $message)
        {
            if (is_resource($this->logHandler))
            {
                $message = strip_tags($message);
                $time = date('[d-m-Y H:i:s] ');
                $message = $time . $message;
                fwrite($this->logHandler, $message."\n");
            }
        }
        parent::write($messages, $newline, $type);
    }
}
