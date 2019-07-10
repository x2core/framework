<?php

namespace X2Core\Foundation\Http;

use \Closure;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestManager
 * @package X2Core\Foundation\Http
 *
 * This class manager system http request
 */
class RequestManager
{
    /**
     * @var string[]
     */
    private $rules = [];

    /**
     * @var  Request
     */
    private $request;

    /**
     * @var mixed
     */
    private $bundle;

    /**
     * @param $classHandle
     * @return void
     * @throws \Exception
     */
    public function pushHandle($classHandle){
        if(is_subclass_of($classHandle, RequestHandler::class)){
            $this->rules[] = $classHandle;
        }else{
            throw new \Exception("The class handler is not base " . RequestHandler::class);
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request|NULL $request
     */
    public function dispatchRequest(Request $request = NULL){
        foreach ($this->rules as $handler){
            $handler  = new $handler;
            /** @var RequestHandler $handler */
            if($handler->validate($this->request)){
                $handler->onRequest($this->request, $this->bundle);
            }else{
                $handler->onReject($this->request, $this->bundle);
            }
        }
    }

    /**
     * @param $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param $handle
     */
    private function launchHandle($handle)
    {
        /** @var RequestHandler $handleRequest */
        $handleRequest = new $handle;
        $handleRequest->onRequest($this->request, $this->bundle);
    }
}