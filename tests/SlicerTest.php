<?php
use Farpost\Downlog\Slicer;
use Farpost\Downlog\LogEntry;
class SlicerTest extends PHPUnit\Framework\TestCase
{

    /**
     * @covers  Slicer::getSlices
     */
    function testSlicerTimeSplitLogByInterval(){

        $intervals=[
            ['00:00:00'], #
            ['00:01:00','00:01:10'],
            ['00:10:00'],
        ];

        $slicer = new Slicer(60);
        $logEntries = $this->createLogEntriesFromIntervals($intervals);
        $slices = iterator_to_array($slicer->getSlices($logEntries,100));

        $this->assertSameSize($intervals,$slices);

        foreach($slices as $k=>$slice){
            $this->assertEquals(count($intervals[$k]),$slice->getSize());
        }



    }


    /**
     * @covers SlicerTimeTest::sliceGenerate
     */
    function testSlicerGenerate(){

        $start = '2020-10-20 0:0:0';
        $expectation=[
            '2020-10-20 00:00:00',
            '2020-10-20 00:01:10',
            '2020-10-20 00:02:20'
        ];

        foreach($this->sliceGenerate(new DateTimeImmutable($start),70,3) as $k=> $record){
            $this->assertEquals($expectation[$k], $record->getDate()->format('Y-m-d H:i:s'));
        }
    }

    function sliceGenerate(DateTimeImmutable $start, $stepInSeconds, $entryCount):Iterable{
        $interval = new DateInterval("PT{$stepInSeconds}S");
        $date=$start;
        for($i=0; $i<$entryCount; $i++){
            yield $this->createLogEntry($date);
            $date = $date->add($interval);
        }
    }

    private function createLogEntry($date,string $status='200',float $duration=1):LogEntry{
        return new LogEntry($date,$status,$duration);
    }



    function createLogEntriesFromIntervals(array $intervals):Generator{
        foreach($intervals as $sequence)
            foreach($sequence as $time){
                yield $this->createLogEntry(new DateTimeImmutable($time));
            }


    }



}