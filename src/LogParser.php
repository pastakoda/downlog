<?php
namespace Farpost\Downtime;

use DateTimeImmutable;
use RuntimeException;

class LogParser
{

    const TIME_FORMAT = '[d/m/Y:H:i:s';
    function parse(string $line):LogEntry{

        $fields = explode(' ',$line);
        $date = DateTimeImmutable::createFromFormat(self::TIME_FORMAT, @$fields[3]);

        if(count($fields)<10 || !$date) throw new RuntimeException('Bad access log format');

        return new LogEntry($date, $fields[8], (float)$fields[10]);
    }

}