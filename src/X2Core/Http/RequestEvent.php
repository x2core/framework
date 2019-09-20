<?php

namespace X2Core\Http;


use X2Core\Preset\Contracts\Request;

/**
 * Class RequestEvent
 * @package X2Core\Http
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
    public function getRequest()
    {
        return $this->request;
    }
}