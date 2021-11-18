<?php

class SomeClass
{
    public $aaa;
    public $bbb;
    public $ccc;
}

function p($i)
{
    echo '';
    print_r($i);
    echo '';
}


$t0 = microtime(true);
$arraysOf = array();
$inicio = memory_get_usage();
for ($i = 0; $i < 1000; $i++) {
    $z = array();
    for ($j = 0; $j < 1000; $j++) {
        $z['aaa'] = 'aaa';
        $z['bbb'] = 'bbb';
        $z['ccc'] = $z['aaa'] . $z['bbb'];
    }
    $arraysOf[] = $z;
}
$fin = memory_get_usage();
echo 'arrays: ' . (microtime(true) - $t0) . "\n";
echo 'memory: ' . ($fin - $inicio) . "\n";
p($z);

$t0 = microtime(true);
$arraysOf = array();
$inicio = memory_get_usage();
for ($i = 0; $i < 1000; $i++) {
    $z = new SomeClass();
    for ($j = 0; $j < 1000; $j++) {
        $z->aaa = 'aaa';
        $z->bbb = 'bbb';
        $z->ccc = $z->aaa . $z->bbb;
    }
    $arraysOf[] = $z;
}
$fin = memory_get_usage();
echo 'arrays: ' . (microtime(true) - $t0) . "\n";
echo 'memory: ' . ($fin - $inicio) . "\n";
p($z);

$t0 = microtime(true);
$arraysOf = array();
$inicio = memory_get_usage();
for ($i = 0; $i < 1000; $i++) {
    $z = new stdClass();
    for ($j = 0; $j < 1000; $j++) {
        $z->aaa = 'aaa';
        $z->bbb = 'bbb';
        $z->ccc = $z->aaa . $z->bbb;
    }
    $arraysOf[] = $z;
}
$fin = memory_get_usage();
echo 'arrays: ' . (microtime(true) - $t0) . "\n";
echo 'memory: ' . ($fin - $inicio) . "\n";
p($z);


