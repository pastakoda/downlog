<?php
use Farpost\Downlog\SliceMerge;
use Farpost\Downlog\Slice;
class SliceMergeTest extends PHPUnit\Framework\TestCase
{

    /**
     * Value for interval generation
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $startTime;

    function setUp(): void{
        parent::setUp();
        $this->startTime = new DateTimeImmutable();
    }


    /**
     * @covers SliceMerge::merge
     */
    function testShouldMergeSequentSlicesWithSameStatus(){

        $accessLevel = 50;
        $source = [
            [$accessLevel+10,$accessLevel+20],   // success
            [$accessLevel-10,$accessLevel-20],   // fail
            [$accessLevel+20]                   // success
        ];

        $slices = []; $expectation = [];

        foreach($source as $sequence){
            [$before,$after]=$this->createSlices($sequence);
            $slices = array_merge($slices, $before);
            $expectation[]=$after;
        }

        $merge = new SliceMerge($accessLevel);
        $result = iterator_to_array($merge->merge($slices));

        $this->assertSameSize($source,$result);
        $this->assertEquals($expectation,$result);

    }


    /**
     * Create source input and expected output from sequence of access levels
     * @param array $accessLevels
     * @return array
     * @throws Exception
     */

    private function createSlices(array $accessLevels):array{

        $before = [];
        $fails = 0;
        $success = 0;

        foreach($accessLevels as $al){
            $before[] = $slice = $this->createSlice($al);
            $fails += $slice->fails;
            $success += $slice->success;
        }

        $after = new Slice(reset($before)->start);
        $after->finish = end($before)->finish;
        $after->success = $success;
        $after->fails = $fails;

        return [$before,$after];
    }

    /**
     * Sequently creates and fills slice with defined access level
     * @param $accessLevel
     * @return Slice
     * @throws Exception
     */

    function createSlice(int $accessLevel):Slice{
        [$start,$finish] = $this->getNextInterval();
        $slice = new Slice($start);
        $slice->finish = $finish;

        if($accessLevel<0 || $accessLevel>100 )throw new Exception('Access level value should be in [0,100]');

        $slice->success = $accessLevel;
        $slice->fails = 100 - $accessLevel;

        return $slice;
    }

    /**
     * Returns new interval greater than previous
     * @return array
     */
    function getNextInterval():array{
        $start = $this->startTime;
        $interval = new DateInterval('PT1M');
        $finish = $start->add($interval);
        $this->startTime = $finish;
        return [$start,$finish];
    }


}