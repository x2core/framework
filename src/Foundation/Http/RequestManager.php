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
     * @var RequestRule[]
     */
    private $rules;

    /**
     * @var  Request
     */
    private $request;

    /**
     * @param $classHandle
     * @return mixed
     */
    public function createRuleToHandle($classHandle){
        if (!isset($this->rules[$classHandle])){
            $this->rules[$classHandle] = [];
        }

        return $this->rules[$classHandle][] = new RequestRule();
    }

    /**
     * @param $classHandle
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

    public function dispatchRequest(Request $request = NULL){
        if(is_null($request)){
            if(is_null($this->request))
                $this->request = Request::createFromGlobals();
        }else{
            $this->request = $request;
        }
        foreach ($this->rules as $handle => $rules){
            $this->matchRule($rules);
        }
    }

    private function matchRule($rule)
    {
    }

}