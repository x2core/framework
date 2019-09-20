<?php

namespace X2Core\ConfigManager;


use X2Core\DotSupport;
use X2Core\Preset\Contracts\Config as ConfigInterface;
/**
 * Class Config
 * @package X2Core\ConfigManager
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * This class contains support to manager configure data
 */
class Config implements ConfigInterface
{
    use DotSupport;

    /**
     * @var ConfigLoader
     */
    private $loader;

    /**
     * Config constructor.
     * @param ConfigLoader $loader
     */
    public function __construct(ConfigLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param $name
     * @return void
     */
    public function init($name)
    {
        $this->configStorage = $this->loader->loadConfig($name);
    }

    /**
     * Return of data of configuration
     *
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function config($name, $default = NULL)
    {
        return $this->dot($name) ?? $default;
    }

    /**
     * Set a value to path of configuration
     *
     * @param $name
     * @param $data
     * @return void
     */
    public function setConfig($name, $data)
    {
         $this->dot($name, $data);
    }
}