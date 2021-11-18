<?php
use Farpost\Downtime\Slice;
class SliceTest extends PHPUnit\Framework\TestCase
{


    /**
     * @throws Exception
     * @covers Slice::toString
     */
    function testToString(){

        $start = '10:30:14';
        $finish = '11:40:00';


        $slice = new Slice(new DateTimeImmutable($start));
        $slice->finish = new DateTimeImmutable($finish);
        $slice->success = 10;
        $slice->fails = 20;

        $string = "$start     $finish     ".number_format($slice->getAccessLevel(),1);

        $this->assertEquals($string,$slice->toString());


    }

    /**
     * @param $start
     * @param $finish
     * @param $success
     * @param $fails
     * @param $size
     * @param $accessLevel
     * @throws Exception
     * @dataProvider getSlices
     * @covers \Farpost\Downtime\Slice
     */


    function testSlice($start, $finish, $success, $fails, $size, $accessLevel){

        $start = new DateTimeImmutable($start);
        $finish = new DateTimeImmutable($finish);

        $slice = new Slice($start);
        $slice->finish = $finish;
        $slice->success = $success;
        $slice->fails = $fails;

        $this->assertEquals($start, $slice->start);
        $this->assertEquals($size, $slice->getSize());
        $this->assertEquals($accessLevel, $slice->getAccessLevel());

        $this->assertTrue($slice->isFail($slice->getAccessLevel()+1));
        $this->assertFalse($slice->isFail($slice->getAccessLevel()));
        $this->assertFalse($slice->isFail($slice->getAccessLevel()-1));

    }

    function getSlices():array{
        # $start, $finish, $success, $fails, $size, $accessLevel
        return [
            ['2021-10-10 10:00','2021-10-10 15:00',10,10,20,50],
            ['2021-10-10 10:00','2021-10-10 15:00',10,0,10,100],
            ['2021-10-10 10:00','2021-10-10 15:00',0,10,10,0],
            ['2021-10-10 10:00','2021-10-10 15:00',1,0,1,100],
            ['2021-10-10 10:00','2021-10-10 15:00',999,1,1000,99.9]
        ];
    }

    /**
     * @covers Slice::getAccessLevel
     */
    function testDivisionByZero(){

        $slice = new Slice(new DateTimeImmutable());

        $this->expectException(DivisionByZeroError::class);
        $slice->getAccessLevel();
    }




}