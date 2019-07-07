<?php

use X2Core\Util\Arr;

class IntroTest extends TestsBasicFramework
{

    /**
     * @return mixed
     */
    public function run()
    {
        $arr = [2,3,5,3,2];
        $this->assertToTrue(Arr::has($arr, 5));
    }
}