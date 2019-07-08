<?php

use X2Core\Util\Arr;
use X2Core\Util\Str;

class IntroTest extends TestsBasicFramework
{

    /**
     * @return void
     */
    public function run()
    {
        // should be equal
        $arr = [2,3,5,3,2];
        $this->assertToTrue(Arr::has($arr, 5));

        // should be equal
        $this->assert(Str::toDashCase('helloWorld'), 'hello_world');
    }
}