<?php

namespace X2Core\Foundation\Events;


use X2Core\QuickApplication;

/**
 * Class AppDeploy
 * @package X2Core\Foundation\Events
 */
class AppDeploy
{
    /**
     * @var QuickApplication
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }
}