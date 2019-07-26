<?php

namespace X2Core;
use Closure;
use X2Core\Contracts\ListenerInterface;
use X2Core\Exceptions\InvalidListener;
use X2Core\Types\EventContext;

/**
 * Class Dispatcher
 * @package X2Core
 * @author Oliver Valiente <oliver021val@gmail.com>
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
     * @param string $event
     * @param object $target
     *
     * @desc This method register a binder object in listeners to dispatch with an event
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
     *
     * @desc Check if current dispatcher has a listener
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
        if(!is_object($listener)){
            $listener = new $listener($event);
        }
        if($listener instanceof Closure) {
            $result = $listener($event, $context);
        }elseif ($listener->isValid()){
            $result = $listener->exec($context);
        }else{
            $result = NULL;
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