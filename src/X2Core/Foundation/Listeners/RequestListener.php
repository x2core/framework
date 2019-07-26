<?php

use X2Core\Contracts\ListenerInterface;

/**
 * Class RequestListener
 */
class RequestListener implements ListenerInterface
{

    /**
     * ListenerInterface constructor.
     * @param $event
     */
    public function __construct($event = NULL)
    {
        parent::__construct($event);
    }

    /**
     *
     * Return true if is possible execute an event for several aspect
     *
     * @return boolean
     */
    public function isValid()
    {
        // TODO: Implement isValid() method.
    }

    /**
     *
     * Execute a action with data an event
     * @param $context
     * @return mixed
     * @internal param $bundle
     */
    public function exec($context)
    {

    }
}