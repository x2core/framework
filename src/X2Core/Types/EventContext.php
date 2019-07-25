<?php

namespace X2Core\Foundation\Events;


/**
 * Class EventContext
 * @package X2Core\Foundation\Events
 */
class EventContext
{
    /**
     * @var mixed
     */
    private $event;

    /**
     * EventContext constructor.
     * @param $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }
}