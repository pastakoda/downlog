<?php
use Farpost\Downtime\Analyzer;
use Farpost\Downtime\Slice;
use Farpost\Downtime\SampleLogGenerator;

class AnalyzerTest extends PHPUnit\Framework\TestCase
{


    /**
     * Huge general test
     * @dataProvider AnalyzerTest::analyzeDataProvider
     * @covers \Farpost\Downtime\Analyzer::run
     */
    function testAnalyze($intervals, float $accessLevel, float $responseTimeLimit,array $expectation): void{

        $logLines = $this->getLogLineGenerator($intervals);

        $command = new Analyzer();
        $slices = $command->run($logLines,$accessLevel,$responseTimeLimit);
        $slices = iterator_to_array($slices);
        $this->assertEquals($expectation,$slices, $this->slicesToString($slices));
    }

    private function slicesToString(array $slices):string{
        return 'Slices: '.PHP_EOL.implode(PHP_EOL,array_map(function(Slice $e){
            return $e->toString();
        },$slices)).PHP_EOL;

    }

    function sampleIntervalProvider():array{

        return [
            $this->interval('0:0:0', '0:1:59',  100,4,100),
            $this->interval('0:2:0', '0:3:59',  95,3,100),
            $this->interval('0:4:0', '0:5:59',  90,2,100),
            $this->interval('0:6:0', '0:7:59',  85,1,100)
        ];
    }

    function analyzeDataProvider():array{

        $intervals = $this->sampleIntervalProvider();

        return [
            [$intervals, 80,10,[$this->createSlice('0:0:0','0:7:59', 30, 370)]],
            [$intervals, 80,0.5,[$this->createSlice('0:0:0','0:7:59', 400, 0)]],
            [$intervals, 100,4,[
                $this->createSlice('0:0:0','0:1:59', 0, 100),
                $this->createSlice('0:02:0','0:7:59', 30, 270),
            ]],
            [$intervals, 90,3,[
                $this->createSlice('0:0:0','0:1:59', 100, 0),
                $this->createSlice('0:2:0','0:5:59', 15, 185),
                $this->createSlice('0:6:0','0:7:59', 15, 85),
            ]],
        ];
    }


    function getLogLineGenerator(array $intervals): Generator
    {
        return SampleLogGenerator::getLineGenerator($intervals);
    }

    private function createSlice(string $start,string $finish,int $fails,int $success):Slice{
        $slice = new Slice(new DateTimeImmutable($start));
        $slice->finish = new DateTimeImmutable($finish);
        $slice->fails = $fails;
        $slice->success = $success;
        return $slice;
    }

    private function getLogGenerator(array $intervals): Generator
    {
        return SampleLogGenerator::getArrayGenerator($intervals);
    }

    private function interval(string $start,string $finish, int $accessLimit, float $duration=1, int $requestCount = 10):array{
        return SampleLogGenerator::interval($start,$finish, $accessLimit,$duration, $requestCount);
    }

}