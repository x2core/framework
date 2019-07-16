<?php

namespace X2Core\Foundation\Http;


use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestEvent
 * @package X2Core\Foundation\Http
 */
class RequestEvent
{
    /**
     * @var Request
     */
    private $request;

    /**
     * RequestEvent constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public static function current(){
        return Request::createFromGlobals();
    }
}