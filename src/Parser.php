<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 2017-08-10
 * Time: 22:35
 */

namespace Bolstad\CsvGrep;



class Parser
{

    private $args;
    private $execBundle = array();
    private $csvParse;

    private $evaluators = array('negative' => 'evaluateNegativeMatch');


    function __construct($args = array())
    {
        $this->args = $args;
    }

    function evaluateNegativeMatch($field, $pattern, $data)
    {
#        echo "field: '$field' pattern: '$pattern' \n";
        if (!isset($data[$field])) {
            echo "field '$field' is not set\n";
            return 0;
        }
#        echo "data to eval '" . $data[$field] . "'\n";
#        print_r($pattern);
#        print_r($data);

        if (strpos($data[$field], $pattern) === false) {

#            echo "the string does not contain our pattern\n";

            return $data;
        }

        return 0;
    }

    function handler($data)
    {
        $foundMatch = 0;

#        print_r($data);
        $matchRules = $this->execBundle['matchrules'];

        foreach ($matchRules['matchpatterns'] as $field => $patterns) {
#            echo "field:" . $field . "\n";

            foreach ($patterns as $pattern) {
                #               echo "pattern: $pattern\n";
                #              echo "type:" . $matchRules['type'] . "\n";

                if ($return = $this->evaluate($field, $pattern, $matchRules['type'], $data)) {
                    $foundMatch++;
                }
            }
        }
        if ($foundMatch) {
            $exec = $this->execBundle['stash'];
            $exec->stash($data);
        }
    }

    function evaluate($field, $pattern, $type, $data)
    {
        if (!isset($this->evaluators[$type])) {
            throw new \Exception("No evaluator available for match type '$type");
        }
        $evalFunction = $this->evaluators[$type];
        $returnData = $this->$evalFunction($field, $pattern, $data);
        return $returnData;
    }

    /**
     * Execute
     */
    function run()
    {

        $matchRules = $this->getMatchRules();
        $outputter = new $this->args['output'](array('filename' => $this->args['output_param']));

        foreach ($this->getFilenames() as $file) {

            $this->execBundle = array();
            $this->execBundle['matchrules'] = $matchRules;
            $this->execBundle['filename'] = $file;
            $this->execBundle['stash'] = $outputter;
            $this->csvParse = new \CsvParser\Simple();
            $this->csvParse->parseRowByRow($file, array($this, 'handler'));


#            echo "$file\n";
        }

    }

    function getMatchRules()
    {
        $rules = array();
        $rules['matchpatterns'] = $this->args['match'];
        $rules['type'] = (isset($this->args['type'])) ? $this->args['type'] : 'exact';

        return $rules;
    }

    /**
     *
     * Returns an array of the files that we should inspect
     * @return array
     * @throws \Exception
     *
     */
    function getFilenames()
    {
        $fileNames = array();
        foreach ($this->args['files'] as $fileName) {
            if (file_exists($fileName)) {
                $fileNames[] = $fileName;
            }
            if (!file_exists($fileName)) {
                throw new \Exception("The file $fileName does not exist");
            }
        }
        return $fileNames;
    }
    /*

    Parameters for parseRowByRow:

    * @param string  $filename  File to parse
    * @param string  $callback  Callback function to send data to
    * @param int     $line      Length - Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters).
    * @param str     $delimiter Set the field delimiter (one character only).
    * @param str     $enclosure Set the field enclosure character (one character only)
    * @param type    $escape    Set the escape character (one character only). Defaults as a backslash.
    * @return bool
    */


}
