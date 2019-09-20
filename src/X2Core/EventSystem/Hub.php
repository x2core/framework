<?php

namespace X2Core\EventSystem;

use RuntimeException;
use X2Core\Preset\Exceptions\IntegrityException;

/**
 * Class Hub
 * @package X2Core
 *
 * A hub is container event that processed the events in a moment
 * in fact is a dispatcher but delay fire action
 */
class Hub extends Dispatcher
{
    const QUEUE = 1;
    const STACK = 2;

    /**
     * @var mixed[]
     */
    private $hubRecord;

    /**
     * @desc reset Hub record
     */
    public function forgetPushed()
    {
        $this->hubRecord = [];
    }

    /**
     * @param $event
     * @param $context
     * @throws \TypeError
     */
    public function push($event, $context = NULL){
        if(!is_object($event))
            throw new RuntimeException("to push event should be instance of event");
        $name = get_class($event);
        if(!isset($this->hubRecord[$name])){
            $this->hubRecord[$name] = [];
        }
        $this->hubRecord[$name][] = [$event, $context];
    }

    /**
     * @param $event
     * @param $mode
     */
    public function dispatchAs($event, $mode)
    {
        if(isset($this->hubRecord[$event])){
            $this->fire($this->hubRecord[$event], $mode);
        }
    }

    /**
     * @param $collect
     * @param $mode
     * @throws IntegrityException
     */
    private function fire(&$collect, $mode)
    {
        $length = count($collect);
        for($i = 0; $i < $length; $i++){
            if($mode === self::STACK){
                $current = array_pop($collect);
            }elseif($mode === self::QUEUE){
                $current = array_shift($collect);
            }
            if(!isset($current) || !is_array($current)){
                throw new IntegrityException();
            }
            $this->dispatch($current[0], $current[1]);
        }
    }
}