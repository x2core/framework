<?php

namespace X2Core\Preset\Contracts;

/**
 * Interface Request
 * @package X2Core\Preset\Contracts
 */
interface Request
{
    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return mixed
     */
    public function getInput();

    /**
     * @return mixed
     */
    public function getQuery();

    /**
     * @return mixed
     */
    public function getBodyData();


}