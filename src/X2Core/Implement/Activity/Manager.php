<?php

namespace X2Core\Implement\Activity;


use Closure;
use X2Core\Implement\Application;
use X2Core\Preset\AbstractManager;

/**
 * Class Manager
 * @package X2Core\Implement\Activity
 */
class Manager extends AbstractManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * Manager constructor.
     * @param Application $app
     * @param array $config
     */
    public function __construct(Application $app, array $config)
    {
        parent::__construct($config, true);
        $this->app = $app;
    }

    /**
     * Extend the system
     *
     * @param $name
     * @param Closure $extension
     * @return mixed
     */
    public function extend($name, Closure $extension)
    {
        $this->drivers[$name] = $extension($this);
    }

    /**
     * Take the message emitted for other manager
     *
     * @param $manager
     * @param $data
     * @return mixed
     */
    public function emitted(AbstractManager $manager, $data)
    {
        // TODO: Implement emitted() method.
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function initDriver(array $params)
    {
        return new $params['handler']();
    }

    /**
     * @param $tag
     * @param $payload
     */
    public function point($tag, $payload){

    }
}