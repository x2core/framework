<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;

class HttpError extends AppError
{
    /**
     * @var
     */
    private $code;

    /**
     * HttpError constructor.
     * @param Application $application
     * @param $code
     */
    public function __construct(Application $application, $code)
    {
        parent::__construct($application);
        $this->code = $code;
    }

}