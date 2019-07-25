<?php

namespace X2Core\Foundation\Events;


use X2Core\Application;
/**
 * Class AppError
 * @package X2Core\Foundation\Events
 */
class AppForceExit extends AbstractEvent
{
    /**
     * AppForceExit constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $app->dispatch(new AppFinished($app));
    }


}