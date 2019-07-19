<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

class HttpNotFound
{
    /**
     * @var Application
     */
    private $app;

    /**
     * HttpNotFound constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $app->dispatch(new HttpError($app, 404));
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

}