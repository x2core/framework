<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface HttpFlow
 * @package X2Core\Preset\Contracts
 */
interface HttpFlow
{
    /**
     * Insert a callable or class to queue of middleware flow
     *
     * @param $middleware
     */
    public function add($middleware);

    /**
     * {@inheritdoc}
     */
    public function prepend($middleware);

    /**
     * Register a polity to apply a request
     *
     * @param $name
     * @param $polity
     * @return
     */
    public function apply($name, $polity);

    /**
     * Register a function to parse a value or object to obtain a response
     *
     * @param $type
     * @param $process
     */
    public function addParser($type, $process);

    /**
     * @param Request $request
     * @return mixed
     */
    public function execute(Request $request);

    /**
     * @param Response $response
     * @return mixed
     */
    public function terminate(Response $response);
}