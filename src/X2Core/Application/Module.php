<?php

namespace X2Core\Application;

/**
 * Class Module
 * @package X2Core\Application
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 * 
 * This class is the base to create a module.
 * The modules is solution to extend a core
 * and centralize the floe of a app
 * The module contains functionality to manager services, components, features, etc
 *
 */
abstract class Module
{
    /**
     * Core instance of centralized container
     *
     * @var Application
     */
    protected $app;

    /**
     * Experimental feature
     *
     * @var bool|array
     */
    public $reload = false;

    /**
     * @var string
     */
    private $defaultAction = NULL;

    /**
     * Module constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Init this module to register a workflow.
     * The implementations is required
     *
     * @return void
     */
    abstract function install();

    /**
     * This method in case if is needed detach functionality
     * This method should override in child class but his implementations is not required
     *
     * @return void
     */
    public function uninstall(){}

    /**
     * Return the dependencies of this module
     * This method should override in child class
     *
     * @return string[]
     */
     public function getDependencies(): array {
         return [];
     }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * This method register a differed service
     *
     * @param $tags
     * @param $service
     * @param \Closure $installer
     * @return $this
     */
    public function lazy($tags, $service, \Closure $installer){
        $this->app->differed($service, $installer);
        foreach ((array) $tags as $tag){
            $this->app->alias($tag, $service);
        }
        return $this;
    }

    /**
     * To bind a contract to service implement
     *
     * @param $contract
     * @param $object
     * @param string $varName
     * @return $this
     */
    public function bind($contract, $object, $varName = ''){
        if(is_string($object)){
            $object = function()use($object){
                return $this->app->call($object . '@' . '__construct');
            };
            $object->bindTo($this);
        }

        $this->app->differed($contract, $object);

        if($varName !== ''){
            $this->app->alias($varName, $contract);
        }
        return $this;
    }

    /**
     * Install a module that is required before action
     *
     * @param $module
     * @return $this
     * @throws ApplicationException
     */
    public function required($module){
        $this->app->bootModule($module);
        return $this;
    }

    /**
     * Register a function to execute when a service is starting
     *
     * @param $service
     * @param callable $elm
     * @return $this
     */
    public function starting($service, callable $elm){
        $this->app->whenLoaded($service, $elm);
        return $this;
    }

    /**
     * Call a functionality of this subclass module
     *
     * @param $feature
     * @param $payload
     */
    public function execute($feature, $payload){
        $this->app->call($this, $feature ?? $this->defaultAction, $payload);
    }
}