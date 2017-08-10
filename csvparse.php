#!/usr/bin/env php
<?php

/**
 *  quick and dirty first version
 *
 */
date_default_timezone_set('Europe/Stockholm');

require "vendor/autoload.php";

function showHelp()
{
    global $argv;
    echo $argv[0] . " <infile> <field> <pattern> <outfile>\n";
}

if (!isset($argv[4])) {
    showHelp();
    die;
}


$args = array(
    'match' => array($argv[2] => array($argv[3])),
    'files' => array($argv[1]),
    'type' => 'negative',
    'output' => '\Bolstad\CsvGrep\CsvWriter',
    'output_param' => $argv[4]
);


$parser = new \Bolstad\CsvGrep\Parser($args);
$parser->run();