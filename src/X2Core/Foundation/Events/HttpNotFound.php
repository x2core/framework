<?php

namespace X2Core\Foundation\Events;



class HttpNotFound extends HttpError
{
    /**
     * HttpNotFound constructor.
     */
    public function __construct()
    {
        parent::__construct(404);
    }

}