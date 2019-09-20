<?php

namespace X2Core\Logger\Handlers;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use X2Core\EventSystem\Dispatcher;

/**
 * Class EventHandler
 * @package X2Core\Logger\Handlers
 */
class EventHandler extends AbstractProcessingHandler
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * EventHandler constructor.
     * @param Dispatcher $dispatcher
     * @param bool|int $level
     * @param bool $bubble
     */
    public function __construct(Dispatcher $dispatcher, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->dispatcher = $dispatcher;
    }


    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $this->dispatcher->dispatch(new EventLog($this->level), $record);
    }
}