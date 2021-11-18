<?php
namespace Farpost\Downtime;
use DateInterval;
use DateTimeImmutable;
use Farpost\Downtime\LogEntry;
use Generator;

class Slicer
{

    private DateInterval $interval;

    function __construct(int $intervalInSeconds){
        $this->interval = new DateInterval("PT{$intervalInSeconds}S");
    }

    function getSlices($entries, float $responseTimeLimit): Generator{

        $next = $slice = null;
        /* @var $next DateTimeImmutable */
        /* @var $slice Slice */

        foreach($entries as $entry){
            /**
             *  @var $entry LogEntry
             */

            if(is_null($next)){
                [$next,$slice] = $this->split($entry->getDate());
            }
            if($entry->getDate()->getTimestamp() >= $next->getTimestamp()){
                yield $slice;
                [$next,$slice] = $this->split($entry->getDate());
            }

            $this->add($slice, $entry, $responseTimeLimit);
        }

        if($slice)yield $slice;

    }


    private function add(Slice $slice,LogEntry $entry, float $responseTimeLimit){

        $slice->finish = $entry->getDate();

        if($entry->isFail($responseTimeLimit)){
            $slice->fails++;
        }else{
            $slice->success++;
        }
    }

    private function split(DateTimeImmutable $start):array
    {
        return [
            $start->add($this->interval),
            new Slice($start)
        ];
    }

}