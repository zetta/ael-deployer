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
    /**
     * Returns true
     * because we need to colorize the output even if we are using tee command
     * @return Boolean true if the stream supports colorization, false otherwise
     */
    protected function hasColorSupport()
    {
        return true;
    }
}
