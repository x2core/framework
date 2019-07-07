<?php

namespace Test;

use Symfony\Component\HttpFoundation\Request;
use X2Core\Foundation\Http\RequestHandler;

class HttpHandleTest extends RequestHandler
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request|Request $result
     * @param $bundle
     * @return mixed|void
     */
    function onRequest(Request $result, $bundle)
    {
        $bundle->test->assert(1,1);
    }

    /**
     * @param $result
     * @param $bundle
     * @return mixed|void
     */
    function onReject($result, $bundle)
    {
        // TODO: Implement onReject() method.
    }
}