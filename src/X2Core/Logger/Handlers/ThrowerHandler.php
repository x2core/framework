<?php

namespace X2Core\Logger\Handlers;


use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use X2Core\Logger\ThrowableLog;

class ThrowerHandler extends AbstractHandler
{
    public function __construct($level = Logger::CRITICAL, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     * @return bool true means that this handler handled the record, and that bubbling is not permitted.
     *                        false means the record was either not processed or that this handler allows bubbling.
     * @throws ThrowableLog
     */
    public function handle(array $record)
    {
        throw new ThrowableLog($record['channel'] . ': ' . $record['formatted'], $record['level']);
    }
}