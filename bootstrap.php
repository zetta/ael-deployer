<?php

function get_argument($argument_key, $default_vaue = null)
{
    global $argv;
    return isset($argv[$argument_key]) ? $argv[$argument_key] : $default_vaue;
}
chdir( dirname(__FILE__) );

$_SERVER['argv'] = build_arguments(get_argument(1));

function build_arguments($action)
{
    unset($_SERVER['argv'][0]);
    switch ($action) {
        case 'list':
            return ['deploy', 'project:list'];
        default:
            return array_merge(['deploy','project:deploy'], $_SERVER['argv']);
    }
}