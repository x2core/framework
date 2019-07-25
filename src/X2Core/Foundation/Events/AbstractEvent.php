<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

/**
 * Class AbstractEvent
 * @package X2Core\Foundation\Events
 */
abstract class AbstractEvent
{
    /**
     * @var Application
     */
    private $application;

    /**
     * BootstrapEvent constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}