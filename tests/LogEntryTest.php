<?php
use Farpost\Downtime\LogEntry;
class LogEntryTest extends PHPUnit\Framework\TestCase
{


    function setUp(): void{
        parent::setUp();
    }


    /**
     * @dataProvider getLogEntryCases
     * @covers LogEntry
     */
    function testEntryCases(string $status,float $duration, float $responseTimeLimit, bool $isStatusFail,  bool $isFail): void{

        $date = new DateTimeImmutable();
        $entry = new LogEntry($date, $status,$duration);

        $this->assertEquals($date, $entry->getDate());
        $this->assertEquals($status, $entry->getStatus());
        $this->assertEquals($duration, $entry->getDuration());
        $this->assertEquals($isStatusFail,$entry->isStatusFail());
        $this->assertEquals($isFail,$entry->isFail($responseTimeLimit));


    }

    function getLogEntryCases():array{

        return [
            ['200',1, 2, false, false],
            ['200',2.1, 2, false, true],
            ['503',1, 2, true, true],
            ['503',2.5, 2.4, true, true],
        ];


    }

}