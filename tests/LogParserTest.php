<?php
use Farpost\Downtime\LogParser;
use Farpost\Downtime\LogEntry;
class LogParserTest extends PHPUnit\Framework\TestCase
{

    private LogParser $parser;

    function setUp(): void{
        parent::setUp();
        $this->parser = new LogParser ();
    }

    function logSample(): array{
        return array_map(function($v){return [$v];},file(__DIR__.'/sample.log'));
    }

    /**
     * @param string $line
     * @dataProvider logSample
     * @covers \Farpost\Downtime\LogParser::parse
     */

    function testShouldParse(string $line): void{

        $record = $this->parser->parse($line);
        $this->assertEquals(LogEntry::class, get_class($record));
        $this->assertStringContainsString($record->getDate()->format(LogParser::TIME_FORMAT),$line);
        $this->assertStringContainsString($record->getDuration(),$line);
        $this->assertStringContainsString($record->getStatus(),$line);

    }

}