<?php

namespace X2Core;


use X2Core\Types\Bundle;

class DestroyManagerEvent
{
    /**
     * @var Dispatcher
     */
    private $manager;

    /**
     * DestroyManagerEvent constructor.
     * @param Dispatcher $manager
     * @internal param Dispatcher $this
     */
    public function __construct(Dispatcher $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Dispatcher
     */
    public function getManager()
    {
        return $this->manager;
    }
}