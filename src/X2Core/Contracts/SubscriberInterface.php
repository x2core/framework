<?php

namespace X2Core;


/**
 * Interface SubcriberInterface
 * @package X2Core
 */
interface SubscriberInterface
{
    public function subscribe(Dispatcher $e);

}