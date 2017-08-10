<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 2017-08-10
 * Time: 22:35
 */

namespace Bolstad\CsvGrep;

class CsvWriter
{
    private $outFile;

    function __construct($args = array())
    {
        if (!isset($args['filename'])) {
            throw new \Exception("Parameter 'filename' not set");
        }

        $this->outFile = new \CsvWriter\SimpleCsvWrite($args['filename'], '.');

    }

    function stash($data = array())
    {
        if (!empty($data)) {
            $this->outFile->writeCsv($data);
        }
    }

}