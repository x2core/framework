<?php

namespace X2Core\Implement;

use X2Core\Http\Flow;

/**
 * Class Controller
 * @package X2Core\Http
 */
abstract class Controller
{
    /**
     * @var Flow
     */
    private $flowHttp;

    /**
     * Controller constructor.
     * @param Flow $flowHttp
     */
    public function __construct(Flow $flowHttp)
    {
        $this->flowHttp = $flowHttp;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract public function configure();

    public function ok($content){

    }

    public function noContent(){

    }

    public function notFound($content = ''){
    	
    }

    public function badRequest($content = ''){
    	
    }

    public function unauthorized($content = ''){
    	
    }

    public function problemsServer($content = ''){
    	
    }

    public function json($data, $code = 200){
    	
    }

    /**
     * @return Flow
     */
    public function getFlowHttp()
    {
        return $this->flowHttp;
    }

    public function pushMiddleware($middleware){
        $this->flowHttp->add($middleware);
    }

}