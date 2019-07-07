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
     * @abstract
     * @return void
     */
    abstract protected function deploy();

}