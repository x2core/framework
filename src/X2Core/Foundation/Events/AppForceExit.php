<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

/**
 * Class AppError
 * @package X2Core\Foundation\Events
 */
class AppForceExit
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
        $app->dispatch(new AppFinished($this));
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

}