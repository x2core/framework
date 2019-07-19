<?php

namespace X2Core;


abstract class Application extends Dispatcher
{
    use ConfigSupport;

    /**
     * const VERSION
     * @desc version of the library
     */
    const VERSION = '2.1.0';

     /**
     * const DEVELOPMENT
     * @desc mode
     */
    const DEVELOPMENT = 1;

     /**
     * const PRODUCTION
     * @desc mode
     */
    const PRODUCTION = 2;

    /**
     * @abstract
     * @return void
     */
    abstract public function deploy();



    /**
     * return void
     */
    public function exit(){

    }
}