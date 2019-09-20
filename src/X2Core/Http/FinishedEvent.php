<?php

namespace X2Core\Http;


use X2Core\Preset\Contracts\Response;
/**
 * Class HttpFinishedEvent
 * @package X2Core\Http
 */
class FinishedEvent
{
    /**
     * @var Response
     */
    private $response;

    /**
     * HttpFinishedEvent constructor.
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