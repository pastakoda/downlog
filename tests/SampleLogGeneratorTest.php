<?php
use Farpost\Downtime\LogParser;
use Farpost\Downtime\SampleLogGenerator;
class SampleLogGeneratorTest extends PHPUnit\Framework\TestCase
{

    /**
     * @covers \Farpost\Downtime\SampleLogGenerator::getLogLineStream
     */
    function testLineGenerator(){
        $start = new DateTimeImmutable('2019-10-10 00:00');$finish = new DateTimeImmutable('2019-10-10 10:00');

        $intervals = [$this->interval($start->format(DATE_ISO8601),$finish->format(DATE_ISO8601),50,10,100)];
        $lines = iterator_to_array(SampleLogGenerator::getLineGenerator($intervals));

        $this->assertCount(100,$lines);
        $this->assertStringContainsString($start->format(LogParser::TIME_FORMAT), reset($lines));
        $this->assertStringContainsString($finish->format(LogParser::TIME_FORMAT), end($lines));

    }


    /**
     * @covers \Farpost\Downtime\SampleLogGenerator::formatLine
     */
    function testFormatLine(){
        $date = new DateTimeImmutable('2020-10-20T20:00:00');
        $status = '500';
        $duration = 24.4;

        $line = SampleLogGenerator::formatLine($date,$status,$duration);

        $this->assertStringContainsString($date->format(LogParser::TIME_FORMAT),$line);
        $this->assertStringContainsString($status,$line);
        $this->assertStringContainsString($duration,$line);
    }

    /**
     * @dataProvider sampleIntervalProvider
     * @throws Exception
     * @covers \Farpost\Downtime\SampleLogGenerator::testGetLogStream
     */
    function testGetArrayGenerator(string $start,string $finish, float $accessLimit,float $duration, int $requestCount){

        $stream = SampleLogGenerator::getArrayGenerator([$this->interval($start, $finish,$accessLimit,$duration, $requestCount)]);

        $start = new DateTimeImmutable($start);
        $finish = new DateTimeImmutable($finish);

        $success = 0; $fails = 0; $durationSum = 0;

        foreach($stream as $row){
            [$date, $status, $rduration] = $row;

            $this->assertGreaterThanOrEqual($start,$date);
            $this->assertLessThanOrEqual($finish,$date);
            if($status=='500')$fails++;else $success++;
            $durationSum+=$rduration;
        }

        $count = ($success+$fails);

        $this->assertEquals($requestCount, $count);
        $this->assertEquals($duration, $durationSum/$count);
        $this->assertEquals($accessLimit, 100*$success/$count, "Relation: $success/$count");

    }

    private  function interval(string $start, string $finish, float $accessLimit, float $duration=1, int $requestCount = 10):array{
        return SampleLogGenerator::interval($start,$finish,$accessLimit,$duration,$requestCount);
    }


    function sampleIntervalProvider():array{
        return [
            $this->interval('0:0:0', '0:0:59',  100,4.4,100),
            $this->interval('0:1:0', '0:1:59',  95.5,3.3,1000),
            $this->interval('0:2:0', '0:2:59',  90,2.2,100),
            $this->interval('0:3:0', '0:4:59',  85,1.1,100)
        ];
    }


}