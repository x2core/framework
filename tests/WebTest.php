<?php

use X2Core\Foundation\Http\RequestManager;
use X2Core\Foundation\Http\RequestRule;
use X2Core\Types\Bundle;

class WebTest extends TestsBasicFramework
{

    /**
     * @return mixed
     *
     * @desc test to router handle
     */
    public function run()
    {
        $webtest = new RequestManager();
        $bundle = new Bundle();
        $bundle->test = $this;
        $webtest->setBundle($bundle);
        // in cli mode the $_SERVER['PATH_INFO'] IS EQUAL '/'
        $webtest->createRuleToHandle(Test\HttpHandleTest::class)
        ->setPath("/")->setMethod(RequestRule::GET_METHOD);
        $webtest->dispatchRequest();
    }
}