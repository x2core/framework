<?php

namespace X2Core\Preset;

use Closure;
/**
 * Class AbstractManager
 * @package X2Core\Preset
 */
abstract class AbstractManager
{
    /**
     * All drivers to execute different activities
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Current drivers
     *
     * @var array
     */
    protected $current;

    /**
     * Stack of other manager to bind
     *
     * @var AbstractManager[]
     */
    protected $stack = [];

    /**
     * Basic config to initialize
     *
     * @var array
     */
    private $config;

    /**
     * AbstractManager constructor.
     * @param array $config
     * @param bool $preset
     */
    public function __construct(array $config, $preset = true)
    {
        $this->config = $config;
        if($preset){
            $default = $config['default'];
            $this->drivers[$default] = $this->current = $this->initDriver($config['drivers'][$default]);
        }
    }

    /**
     * Extend the system
     *
     * @abstract
     * @param $name
     * @param Closure $extension
     * @return mixed
     */
    abstract public function extend($name, Closure $extension);

    /**
     * Take the message emitted for other manager
     *
     * @param $manager
     * @param $data
     * @return mixed
     */
    abstract public function emitted($manager, $data);

    /**
     * This method should be override to implement a driver
     *
     * @param array $params
     * @return mixed
     */
    public function initDriver(array $params){return null;}

    /**
     * @param $key
     * @return mixed
     */
    protected function getDriver($key){
       return $this->drivers[$key];
    }

    /**
     * @param $key
     * @param $driver
     * @return $this
     */
    protected function addDriver($key, $driver){
        $this->drivers[$key] = $driver;
        return $this;
    }

    /**
     * @param $key
     * @param AbstractManager $manager
     * @return $this
     */
    protected function push(self $manager, $key){
        $this->stack[$key] = $manager;
        return $this;
    }

    /**
     * @param $stack
     * @param $message
     * @return $this
     */
    protected function message($stack, $message){
        $this->stack[$stack]->emitted($this, $message);
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    protected function emit($message){
        foreach ($this->stack as $stack){
            $stack->emitted($this, $message);
        }
        return $this;
    }
}