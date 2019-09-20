<?php

namespace X2Core\Http;


use X2Core\Preset\Contracts\Response;

class BeforeSendResponse
{

    /**
     * @var Response
     */
    private $response;

    /**
     * BeforeSendResponse constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}