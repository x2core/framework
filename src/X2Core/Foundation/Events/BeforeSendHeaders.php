<?php

namespace X2Core\Foundation\Events;


use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BeforeSendHeaders
{
    /**
     * @var ResponseHeaderBag
     */
    private $headers;

    /**
     * BeforeSendHeaders constructor.
     * @param ResponseHeaderBag $headers
     */
    public function __construct(ResponseHeaderBag $headers)
    {
        $this->headers = $headers;
    }

    public function putHeader($name, $value){
        $this->headers->set($name, $value);
    }

    /**
     * @param $name
     * @return string|string[]
     */
    public function getHeaders($name)
    {
        return $this->headers->get($name);
    }

    /**
     * @return mixed
     */
    public function getCookie(){
        return $this->getCookie();
    }

}