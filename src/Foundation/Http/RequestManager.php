<?php

namespace X2Core\Foundation\Http;


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
     * @var RequestRule[][]
     */
    private $rules;

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
     * @return RequestRule
     */
    public function createRuleToHandle($classHandle){
        if (!isset($this->rules[$classHandle])){
            $this->rules[$classHandle] = [];
        }

        return $this->rules[$classHandle][] = new RequestRule();
    }

    /**
     * @param $classHandle
     * @param $handle
     */
    public function appendRuleToHandle($classHandle, $handle){
        if (!isset($this->rules[$classHandle])){
            $this->rules[$classHandle] = [];
        }

        $this->rules[$classHandle][] = $handle;
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
        if(is_null($request)){
            if(is_null($this->request))
                $this->request = Request::createFromGlobals();
        }else{
            $this->request = $request;
        }
        foreach ($this->rules as $handle => $rules){
            $this->processRules($handle, $rules);
        }
    }

    /**
     * @param $handle
     * @param RequestRule[] $rules
     */
    private function processRules($handle, $rules)
    {

        $length = count($rules);
        for ($i = 0; $i < $length; $i++){
            if($rules[$i]->match($this->request )){
                $this->launchHandle($handle);
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