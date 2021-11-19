<?php
namespace Farpost\Downlog;

use DateTimeImmutable;

class Slice
{
    public DateTimeImmutable $start;
    public DateTimeImmutable $finish;
    public int $success = 0;
    public int $fails = 0;
    function __construct(DateTimeImmutable $start){
        $this->start = $start;
    }


    function getSize():int{
        return $this->success + $this->fails;
    }

    function getAccessLevel():float{
        return 100 * $this->success / $this->getSize();
    }

    function isFail($accessLevel):bool{
         return $this->getAccessLevel() < $accessLevel;
    }

    function toString():string{
        $format = 'H:i:s ';
        return $this->start->format($format).'    '.$this->finish->format($format).'    '.number_format($this->getAccessLevel(),1);
    }

}