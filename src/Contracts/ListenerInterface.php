<?php

namespace X2Core\Contracts;


interface ListenerInterface
{
    /**
     *
     * Return true if is possible execute an event for several aspect
     *
     * @return boolean
     */
    public function isValid();

    /**
     *
     * Execute a action with data an event
     * @param $bundle
     * @param $context
     * @return mixed
     */
    public function exec( $bundle, $context);

}