<?php

namespace Test;


use X2Core\Contracts\ListenerInterface;

class Listener implements ListenerInterface
{
    /**
     * @var object
     */
    private $event;

    /**
     * Listener constructor.
     * @param object $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     *
     * Return true if is possible execute an event for several aspect
     *
     * @return boolean
     */
    public function isValid()
    {
       return true;
    }

    /**
     *
     * Execute a action with data an event
     * @param array $bundle
     * @return mixed|void
     */
    public function exec($bundle, $context)
    {
       $bundle->testEvent = $this->event->data;
    }
}