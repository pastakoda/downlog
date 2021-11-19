<?php
namespace Farpost\Downlog;

class Analyzer
{

    private LogParser $parser;
    private Slicer $slicer;

    function __construct(){
        $this->parser = new LogParser();
        $this->slicer = new Slicer(60);
    }

    function run(iterable $logLines, float $accessLevel, float $responseTimeLimit):Iterable {
        $log = $this->getLogEntries($logLines);
        $slices = $this->splitLog($log, $responseTimeLimit);
        $slices = $this->mergeSlices($slices,$accessLevel);
        foreach($slices as $slice){
            yield $slice;
        }
    }

    private function mergeSlices(Iterable $slices, float $accessLevel):Iterable{
        return (new SliceMerge($accessLevel))->merge($slices);
    }

    private function splitLog($logEntries, float $responseTimeLimit):Iterable{
        return $this->slicer->getSlices($logEntries,$responseTimeLimit);
    }

    private function getLogEntries(iterable $logLines):Iterable{
        foreach($logLines as $line){
            yield $this->parser->parse($line);
        }
    }


}