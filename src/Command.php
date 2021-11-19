<?php
namespace Farpost\Downlog;

use Generator;

class Command
{

    private Analyzer $analyzer;

    function __construct(){
        $this->analyzer = new Analyzer();
    }

    /**
     * @param $input input stream (stdin)
     * @param int $accessLevel access level in percent
     * @param float $responseTimeLimit time limit in seconds
     * @param $output output stream (stdout)
     */
    function run($input, float $accessLevel, int $responseTimeLimit, $output){
        $lines = $this->readLines($input);
        foreach($this->analyzer->run($lines,$accessLevel,$responseTimeLimit) as $slice){
            /** @var Slice $slice */
            if($slice->isFail($accessLevel)){
                fputs($output,$slice->toString().PHP_EOL);
                fflush($output);
            }
        }
    }

    private function readLines($input): Generator
    {
        while($line = fgets($input))yield $line;
    }



}