<?php
namespace Farpost\Downtime;

use DateTimeImmutable;

Class LogEntry{

    private DateTimeImmutable $date;
    private string $status;
    private float $duration;

    function __construct(DateTimeImmutable $date, $status, float $duration){
        $this->date = $date;
        $this->status = $status;
        $this->duration = $duration;
    }

    public function getDate(): DateTimeImmutable{
        return $this->date;
    }

    public function getStatus():string{
        return $this->status;
    }

    public function getDuration():float{
        return $this->duration;
    }

    function isFail(float $responseTimeLimit):bool{
        return $this->isStatusFail() || $this->getDuration() > $responseTimeLimit;
    }

    function isStatusFail():bool{
        return $this->getStatus()[0]=='5';
    }

}
