<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface Container
 * @package X2Core\Preset\Contracts
 *
 *
 *
 * Container Dependency Inject Interface to declare a contracts
 * that follow the philosophy of X2Core Components
 */
interface Container
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $name
     * @return mixed
     */
    public function has($name);

    /**
     * @param $name
     * @param \Closure $closure
     * @return mixed
     */
    public function differed($name, \Closure $closure);

    /**
     * @param $service
     * @param null $alt
     * @return mixed
     */
    public function resolve($service, $alt = NULL);

    /**
     * @param $service
     * @param $objService
     * @return $this
     */
    public function register($service, $objService);

    /**
     * {@inheritdoc}
     */
    public function alias($service, $objService);

    /**
     * @param $service
     * @param $context
     * @return $this
     */
    public function context($service, array $context);

    /**
     * Remove a instance of container
     *
     * @param $service
     */
    public function remove($service);

    /**
     * Register a factory
     *
     * @param $name
     * @param $action
     */
    public function factory($name, $action);

    /**
     * Create an object through a factory
     *
     * @param $name
     * @param array[] $arguments
     * @param bool $noInject
     * @return mixed
     */
    public function create($name, array $arguments = [], $noInject = false);

    /**
     * Create an instance from a class and dependency injection
     *
     * @param $source
     * @param array $arguments
     * @param array|null $override
     * @return mixed
     */
    public function inject($source, array $arguments = NULL, array $override = NULL);

    /**
     * Call a action from several types sources and inject dependencies
     *
     * @param $source
     * @param array $arguments
     * @param array|null $override
     * @return mixed
     */
    public function call($source, array $arguments = NULL, array $override = NULL);
}