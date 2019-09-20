<?php

namespace X2Core\Logger\Handlers;


class EventLog
{
    private $level;

    /**
     * EventLog constructor.
     * @param $level
     */
    public function __construct($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }
}