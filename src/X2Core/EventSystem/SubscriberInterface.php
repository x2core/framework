<?php

namespace X2Core\Preset\Contracts;


/**
 * Interface SubscriberInterface
 * @package X2Core
 */
interface SubscriberInterface
{
    /**
     * @param Dispatcher $e
     * @return mixed
     */
    public function subscribe(Dispatcher $e);
}