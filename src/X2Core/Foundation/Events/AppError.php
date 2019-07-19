<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

/**
 * Class AppError
 * @package X2Core\Foundation\Events
 */
class AppError
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var int
     */
    private $code;

    /**
     * AppError constructor.
     * @param Application $app
     * @param $code
     */
    public function __construct(Application $app, $code)
    {
        $this->app = $app;
        $this->code = $code;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

}