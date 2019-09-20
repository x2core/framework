<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface Config
 * @package X2Core\Preset\Contracts
 */
interface Config
{
    /**
     * @param $name
     * @return mixed
     */
    public function init($name);

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function config($name, $default = NULL);

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    public function setConfig($name, $data);
}