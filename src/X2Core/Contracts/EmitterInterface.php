<?php

namespace X2Core\Contracts;


/**
 * Interface EmitterInterface
 * @package X2Core\Contracts
 */
interface EmitterInterface
{
    /**
     * @param $wildcard
     * @param callable $handle
     *
     * @desc create a handle to name event with wildcard
     * @return mixed
     */
   public function on($wildcard, callable $handle);

    /**
     * @param $wildcard
     *
     * @desc emmit a event
     * @return mixed
     */
   public function emmit($wildcard, $payload = NULL);

    /**
     * @param $errors
     * @param callable $handle
     *
     * @desc create a handle to fix name error by name
     */
   public function fix($errors, callable $handle);

    /**
     * @param $errors
     *
     * @desc throw a error by name that must handle for fix handler
     * @return mixed
     */
   public function report($errors);

    /**
     * @param $wildcard
     *
     * @desc basically to delete event by wildcard
     * @return mixed
     */
   public function skip($wildcard);

    /**
     * @param $errors
     *
     * @desc basically to delete handle or ignore if is emitted
     */
    public function ignore($errors);

}