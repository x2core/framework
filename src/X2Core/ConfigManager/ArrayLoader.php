<?php

namespace X2Core\ConfigManager;

/**
 * Class ArrayLoader
 * @package X2Core\ConfigManager
 */
class ArrayLoader implements ConfigLoader
{
    /**
     * ArrayLoader constructor.
     */
    public function __construct($cache)
    {

    }

    /**
     * Return all config from the sources
     *
     * @param $name
     * @return mixed
     */
    public function loadConfig($name)
    {
        // TODO: Implement loadConfig() method.
    }

    /**
     * Reset
     *
     * @return void
     */
    public function free()
    {
        // TODO: Implement free() method.
    }

    /**
     * @return void
     */
    public function unload()
    {
        // TODO: Implement unload() method.
    }
}