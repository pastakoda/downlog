#!/usr/local/bin/php
<?php
require __DIR__ . '/vendor/autoload.php';

$options = getopt('u:t:m');
$u = @$options['u'];
$t = @$options['t'];

if(!$options){
    echo PHP_EOL;
    echo "{$argv[0]} -u <access level> -t <response time limit> [-m]".PHP_EOL;
    echo PHP_EOL;
    echo "Parse access log from standard input for inaccessible intervals according to options:".PHP_EOL;
    echo "-u <access level> - percentage of acceptable accessibility".PHP_EOL;
    echo "-t <response time limit> - acceptable response time in milliseconds".PHP_EOL;
    echo "-m - show memory peak usage".PHP_EOL;
    echo PHP_EOL;
    die;
}

if(!is_numeric($u) || $u<=0 || $u >100 ){
    echo "-u should be numeric between 0 and 100".PHP_EOL;
    die;
}
if(!is_numeric($t) || $u<=0 || $u >100 ){
    echo "-t should be numeric".PHP_EOL;
    die;
}

$command = new \Farpost\Downlog\Command();
$command->run(STDIN,$u,$t,STDOUT);

if(key_exists('m',$options)){
    echo number_format(memory_get_peak_usage()/(1024*1024),3, ',',' ').' Mb'.PHP_EOL;
}