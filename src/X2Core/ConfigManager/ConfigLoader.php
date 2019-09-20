<?php

namespace X2Core\ConfigManager;

/**
 * Class ConfigLoader
 * @package X2Core\ConfigManager
 */
interface ConfigLoader
{
    /**
     * @param $name
     * @return mixed
     */
    public function loadConfig($name);

    /**
     * @return void
     */
    public function free();

    /**
     * @return void
     */
    public function unload();
}