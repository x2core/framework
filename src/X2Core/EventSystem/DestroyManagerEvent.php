<?php

namespace X2Core\EventSystem;

/**
 * Class DestroyManagerEvent
 * @package X2Core
 */
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