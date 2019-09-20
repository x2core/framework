<?php

namespace X2Core\Logger\Handlers;


use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use X2Core\Preset\Contracts\Container;

/**
 * Class ConditionalHandler
 * @package X2Core\Logger\Handlers
 */
class ConditionalHandler extends AbstractHandler
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Closure
     */
    private $conditional = [];

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * ConditionalHandler constructor.
     * @param Container $container
     * @param bool|int $level
     * @param bool $bubble
     */
    public function __construct(Container $container, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->container = $container;
    }

    /**
     * @param \Closure $condition
     * @return $this
     */
    public function when(\Closure $condition){
        $this->conditional = $condition;
        return $this;
    }

    /**
     * @param HandlerInterface $handler
     * @return $this
     */
    public function push(HandlerInterface $handler){
        $this->handlers[] = $handler;
        return $this;
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
     */
    public function handle(array $record)
    {
        if($this->conditional->call($this->container)){
            foreach ($this->handlers as $handler){
                $handler->handle($record);
            }
        }
    }
}