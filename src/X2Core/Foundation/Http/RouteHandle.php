<?php

namespace X2Core\Foundation\Http;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteHandle
 * @package X2Core\Foundation\Http
 */
abstract class RouteHandle extends RequestHandler
{

    /**
     * @param Request $result
     * @param $bundle
     * @return mixed
     */
    function onRequest(Request $result, $bundle)
    {
        $result->getPathInfo();
    }

    /**
     * @param $result
     * @param $bundle
     * @return mixed
     */
    function onReject($result, $bundle)
    {
        // TODO: Implement onReject() method.
    }

    /**
     * @return void
     */
    abstract public function configure();
}