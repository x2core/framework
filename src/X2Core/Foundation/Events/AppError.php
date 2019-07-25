<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

/**
 * Class AppError
 * @package X2Core\Foundation\Events
 */
class AppError extends AbstractEvent
{
    /**
     * @var int
     */
    private $code;

    /**
     * AppError constructor.
     * @param Application $application
     * @param $code
     */
    public function __construct(Application $application, $code)
    {
        parent::__construct($application);
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
}