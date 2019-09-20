<?php

namespace X2Core\Http;

/**
 * Class Middleware
 * @package X2Core\Http
 */
abstract class Middleware
{
    /**
     * @abstract
     * @return mixed
     */
    abstract public function onRequest();

}