<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

/**
 * Class AppError
 * @package X2Core\Foundation\Events
 */
class AppFinished
{
    /**
     * @var Application
     */
    private $app;

    /**
     * AppError constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

}