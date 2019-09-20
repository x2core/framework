<?php

namespace X2Core\EventSystem;

use Closure;
use X2Core\Preset\Contracts\ListenerInterface;
use X2Core\Preset\EventContext;

/**
 * Class Dispatcher
 * @package X2Core
 * @author Oliver Valiente <oliver021val@gmail.com>
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

    /**
     * Simple validator to listeners
     *
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
     * Dispatch an event to execute the listeners that were assigned
     *
     * @param $event
     * @param null $context
     * @return mixed[]|bool
     */
    public function dispatch($event, $context = NULL){
        $className = get_class($event);
        $result = [];
        if(!isset($this->listeners[$className])){
            return false;
        }

        foreach ($this->listeners[$className] as $listener){
            $result[] = $this->sendToListeners($event, $listener, $context);
        }

        return $result;
    }

    /**
     * Push a listen handler to an event
     *
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
     * When an event is dispatched then the concatenated events are dispatch
     *
     * @param $event
     * @param array $events
     * @param null $payload
     * @return $this
     */
    public function concat($event, array $events, $payload = NULL){
        $dispatcher = $this;
        $this->listen($event, function($prevEvent) use($payload, $events, $dispatcher){
            foreach ($events as $event){
                $dispatcher->dispatch(new $event($payload), new EventContext($prevEvent));
            }
        });

        return $this;
    }

    /**
     * This method register a binder object in listeners to dispatch with an event
     *
     * @param string $event
     * @param object $target
     * @return $this
     * @throws BinderException
     */
    public function bind($event, $target){
        $this->listen($event, function($event, $context) use($target){
            $target->onInteract($event, $context);
        });

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
        if (is_string($subscriber) && is_subclass_of($subscriber, SubscriberInterface::class) ) {
            $subscriber = new $subscriber;
        }elseif(!is_object($subscriber) || is_subclass_of($subscriber, SubscriberInterface::class)){
            throw new InvalidSubscriber();
        }

        $subscriber->subscribe($this);
        return $this;
    }

    /**
     * Check if current dispatcher has a listener
     *
     * @param $class
     * @return bool
     */
    public function hasListeners($class)
    {
        return isset($this->listeners[$class]);
    }

    /**
     * Remove a event of listeners record to forget it
     *
     * @param $event
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

    /**
     * @param $event
     * @param $listener
     * @param $context
     * @return bool
     */
    private function sendToListeners($event, $listener, $context)
    {
        /* @var ListenerInterface $listener */
        if(!is_object($listener)){
            $listener = new $listener($event);
        }

        if($listener instanceof Closure) {
            // if is a listener is a Closure then is invoked
            $result = $listener($event, $context);
        }elseif ($listener->isValid()){
            // if is a class should be an implementation of ListenerInterface
            // then exec metod is invoked
            $result = $listener->exec($context);
        }else{

            // the validateListener method should validate a type of listener but if
            // by a unknown cause is passed a listener invalid, simply is not executed
            $result = NULL;
        }

        return $result;
    }
}