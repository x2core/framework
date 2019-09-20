<?php

namespace X2Core\Implement;

use X2Core\Application\Application as AbstractApplication;
use X2Core\Application\ApplicationException;
use X2Core\ConfigManager\Config as ConfigService;
use X2Core\EventSystem\Dispatcher as DispatcherService;
use X2Core\Preset\Contracts\Config;
use X2Core\Preset\Contracts\Dispatcher;
/**
 * Class Application
 * @package X2Core\Implement
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * This is a implementation of Application
 * This application use base service and http kit implementation
 */
class Application extends AbstractApplication
{
    /**
     * This property should be override in base class
     *
     * @var string
     */
    protected $bootstrapModule;

    /**
     * Application constructor.
     *
     * @param $bootstrapModule
     */
    public function __construct($bootstrapModule = NULL)
    {
        parent::__construct();
        if($bootstrapModule)
            $this->bootstrapModule = $bootstrapModule;
    }

    /**
     * Deploy Application
     * This method should implement the action that going to initialize all flow
     *
     */
    public function run()
    {
        $this->registerBaseSupport();
        try{
            $this->bootModule($this->bootstrapModule);
        }catch (ApplicationException $exception){
            throw new ApplicationException('The bootstrap module install has been failed');
        }
    }

    /**
     * Init workflow of the application
     *
     * @return void
     */
    private function registerBaseSupport()
    {
        // register base services
        $this->register(Config::class, ConfigService::class);
        $this->register(Dispatcher::class, DispatcherService::class);

        // set an alias to services
        $this->alias('config', Config::class);
        $this->alias('events', Dispatcher::class);
    }
}