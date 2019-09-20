<?php

namespace X2Core\Preset;


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