<?php
namespace Farpost\Downlog;

use DateTimeImmutable;
use Generator;

class SampleLogGenerator
{


    static function getLineGenerator(array $intervals): Generator{
        foreach(self::getArrayGenerator($intervals) as $row){
            [$date,$status,$duration] = $row;
            yield self::formatLine($date,$status,$duration);
        }
    }

    const lineFormat = '92.168.32.181 - - %s +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" %s 2 %s "-" "@list-item-updater" prio:0';

    static function formatLine(DateTimeImmutable $date, string $status, float $duration):string{
        return sprintf(self::lineFormat,$date->format(LogParser::TIME_FORMAT), $status, $duration);
    }


    static function getArrayGenerator(array $intervals): Generator{

        foreach($intervals as $row){
            [$start,$finish, $accessLimit,$duration,$count] = $row;

            $start = new DateTimeImmutable($start);
            $finish = new DateTimeImmutable($finish);
            $delta = ($finish->getTimestamp()-$start->getTimestamp())/($count-1);

            $success = $count*$accessLimit/100;
            $fails = $count-$success;
            $failsMod = $fails?floor($count / $fails):null;
            $date = new DateTimeImmutable();
            $failsCount = 0;

            for($i=0;$i<$count;$i++){

                $date = $date->setTimestamp($start->getTimestamp()+$i*$delta);
                $isFail = $fails && ($fails > $failsCount) && (($i % $failsMod)==0);
                $status = $isFail?'500':'200';
                yield [$date, $status, $duration];

                if($isFail)$failsCount++;

            }

        }

    }

    static function interval(string $start,string $finish, float $accessLimit, float $duration=1, int $requestCount = 10):array{
        return [$start,$finish, $accessLimit,$duration, $requestCount];
    }
}