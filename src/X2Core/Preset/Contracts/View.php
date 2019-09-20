<?php

namespace X2Core\Contracts;

/**
 * Interface ViewInterface
 * @package X2Core\Contracts
 */
interface View
{
    public function render($string, $data);

    /**
     * @return mixed
     */
    public function getEngine();

    /**
     * @return string
     */
    public function getSuffix();

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix);

    /**
     * @return string[]
     */
    public function getPaths();

    /**
     * @param string $path
     */
    public function addPath($path);
}