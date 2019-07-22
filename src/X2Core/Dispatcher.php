<?php

namespace X2Core;
use Closure;
use X2Core\Contracts\ListenerInterface;
use X2Core\Exceptions\InvalidListener;

/**
 * Class Dispatcher
 * @package X2Core
 *
 * <p>
 * The manager provide centralized control listener and event to
 * handle flow, runtime, model data and support to different request to system.
 * This allow control data through runtime and flow events
 * </p>
 *
 */
class Dispatcher
{
    /**
     * @var string[][]
     */
    private $listeners;


    /*
     * @var mixed[]
     */
    private $bundle;

    /**
     * @param $listener
     * @throws InvalidListener
     */
    public static function isValidListener($listener)
    {
        if (!is_subclass_of($listener, ListenerInterface::class) && !($listener instanceof Closure)) {
            throw new InvalidListener("The listener not implement " . ListenerInterface::class);
        }
    }

    /**
     * Destruct to Dispatcher
     */
    public function __destruct()
    {
        if($this->hasListeners(DestroyManagerEvent::class))
            $this->dispatch(new DestroyManagerEvent($this));
    }

    /**
     * @param $event
     * @param null $context
     *
     * @desc Dispatch an event to execute the listeners that were assigned
     */
    public function dispatch($event, $context = NULL){
        $className = get_class($event);
        foreach ($this->listeners[$className] as $listener){
            $this->sendToListeners($event, $listener, $context);
        }
    }

    /**
     * @param $event
     * @param $classNameListener
     * @return $this
     * @throws InvalidListener
     */
    public function listen( $event, $classNameListener){
        if (!isset($this->listeners[$event])){
            $this->listeners[$event] = [];
        }
        $this->addListener($this->listeners[$event], $classNameListener);
        return $this;
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string $subscriber
     * @return $this
     * @throws InvalidSubscriber
     */
    public function subscribe($subscriber)
    {
        if (is_string($subscriber) &&is_subclass_of($subscriber, SubscriberInterface::class) ) {
            $subscriber = new $subscriber;
        }elseif(!is_object($subscriber) || is_subclass_of($subscriber, SubscriberInterface::class)){
            throw new InvalidSubscriber();
        }

        $subscriber->subscribe($this);
        return $this;
    }

    /**
     * @param $class
     * @return bool
     */
    public function hasListeners($class)
    {
        return isset($this->listeners[$class]);
    }

    /**
     * @param $event
     * @param $listener
     * @param $context
     * @return bool
     */
    private function sendToListeners($event, $listener, $context)
    {
        /* @var ListenerInterface $listener */
        if(!is_object($listener))
            $listener = new $listener($event);
        if($listener instanceof Closure) {
            $listener($this->bundle, $event);
            $result = true;
        }elseif ($listener->isValid()){
            $listener->exec($this->bundle, $context);
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param mixed $bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @param $event
     *
     * @desc Remove a event of listeners record to forget it
     * @return void
     */
    public function forgetEvent($event){
        if(isset($this->listeners[$event]))
            unset($this->listeners[$event]);
    }

    /**
     * @param array $eventRecord
     * @param $listener
     */
    private function addListener(array &$eventRecord, $listener)
    {
        self::isValidListener($listener);
        array_push($eventRecord, $listener);
    }
}