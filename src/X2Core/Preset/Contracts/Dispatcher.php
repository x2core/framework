<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface Dispatcher
 * @package X2Core\Preset\Contracts
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
interface Dispatcher
{
    /**
     * Dispatch an event to execute the listeners that were assigned
     *
     * @param $event
     * @param null $context
     * @return mixed[]|bool
     */
    public function dispatch($event, $context = NULL);


    /**
     * Push a listen handler to an event
     *
     * @param $event
     * @param $classNameListener
     * @return $this
     */
    public function listen( $event, $classNameListener);

    /**
     * When an event is dispatched then the concatenated events are dispatch
     *
     * @param $event
     * @param array $events
     * @param null $payload
     * @return $this
     */
    public function concat($event, array $events, $payload = NULL);

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string $subscriber
     * @return $this
     */
    public function subscribe($subscriber);

    /**
     * Check if current dispatcher has a listener
     *
     * @param $class
     * @return bool
     */
    public function hasListeners($class);

    /**
     * Remove a event of listeners record to forget it
     *
     * @param $event
     * @return void
     */
    public function forgetEvent($event);

}