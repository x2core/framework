<?php

namespace X2Core\EventSystem;


class InvalidSubscriber extends \Exception
{
    public function __construct()
    {
        parent::__construct('The argument pass to subscribe is not implement interface', 2);
    }
}