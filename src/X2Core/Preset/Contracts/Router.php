<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface Router
 * @package X2Core\Preset\Contracts
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
interface Router
{
    /**
     * Register a route with a handler
     *
     * @param $method
     * @param $matchRoute
     * @param $handle
     * @param array $options
     * @return mixed
     */
    public function addRoute($method, $matchRoute, $handle, array $options = []);

    /**
     * Return a generator to iterate with routes that matched
     *
     * @param $method
     * @param $url
     * @return \Generator
     */
    public function fetch($method, $url): \Generator;

    /**
     * Create a url with name route and supplied params
     *
     * @param $name
     * @param null $values
     * @return mixed
     */
    public function generate($name, $values = NULL);

}