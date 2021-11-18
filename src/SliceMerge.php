<?php
namespace Farpost\Downtime;

use Generator;

class SliceMerge
{

    private float $accessLevel;

    function __construct(float $accessLevel){
        $this->accessLevel = $accessLevel;
    }

    function merge(iterable $slices): Generator{

        $current  = null;
        /** @var $current Slice */

        foreach($slices as $slice){

            /** @var $slice Slice */

            if(is_null($current)){
                $current=clone $slice;
                continue;
            }

            if($this->haveSameStatus($current,$slice)){
                $this->extend($current,$slice);
            }else{
                yield $current;
                $current = clone $slice;
            }

        }

        if($current)yield $current;

    }


    public function haveSameStatus(Slice $slice1, Slice $slice2):bool{
        return $this->isFail($slice1)==$this->isFail($slice2);
    }

    public function isFail(Slice $slice):bool{
        return $slice->isFail($this->accessLevel);
    }

    public function extend(Slice $current, Slice $new):void{
        $current->finish = $new->finish;
        $current->success += $new->success;
        $current->fails += $new->fails;
    }

}