<?php

use X2Core\Util\Arr;
use X2Core\Util\Str;
use X2Core\Util\URL;

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

        $path = '/index/some_path';
        $result_url = URL::match(URL::MATCH_ARRAY_PARAM, ['index', '$path'], $path);
        $this->assertToTrue(isset($result_url['path']));
        $result_url = URL::match(URL::MATCH_ARRAY_PARAM, ['index_2', '$path'], $path);
        $this->assertToFalse(isset($result_url['path']));

    }
}