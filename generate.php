#!/usr/local/bin/php
<?php
use Farpost\Downlog\SampleLogGenerator;
require __DIR__ . '/vendor/autoload.php';


while($line = fgets(STDIN)){
    $interval = explode(' ',$line);
    if(count($interval)!=5)throw new \RuntimeException('Interval description should consist off 5 space separated numbers, see intervals.txt and SampleLogGenerator::interval ');

    foreach(SampleLogGenerator::getLineGenerator([$interval]) as $line){
        fputs(STDOUT,$line.PHP_EOL);
    }

}

