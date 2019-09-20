<?php

namespace X2Core\Preset\Contracts;


interface ListenerInterface
{
    /**
     * ListenerInterface constructor.
     * @param $event
     */
    public function __construct($event = NULL);

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
     * @param $context
     * @return mixed
     * @internal param $bundle
     */
    public function exec( $context);

}