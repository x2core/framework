<?php

namespace X2Core\Foundation\Events;
use X2Core\QuickApplication;


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
    public function __construct(QuickApplication $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}