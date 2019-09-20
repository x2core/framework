<?php

namespace X2Core\Implement;
use X2Core\Application\ApplicationException;

/**
 * Class ProxyClass
 * @package X2Core\Implement
 *
 * @author Oliver Valiente
 *
 * ProxyClass is special utility to implement a facade pattern
 * This classic pattern of POO is a helpful to developer yours applications
 */
class ProxyClass
{
    /**
     * Instance singleton of target Application
     *
     * @var Application
     */
    private static $app;

    /**
     * Record of available proxies
     *
     * @var callable $proxies
     */
    private static $proxies = [];

    /**
     * Init a target proxy implementation
     *
     * @param Application $application
     * @return void
     * @throws ApplicationException
     */
    public static function init(Application $application){
        if(!self::$app)
            self::$app = $application;
        else
            throw new ApplicationException('The application is singleton object and tried make a second action of start');
    }

    /**
     * Install an autoload to create the proxies
     *
     * @return void
     */
    public static function autoload(){
        spl_autoload_register(function($class){
            if(ProxyClass::has($class))
               ProxyClass::createClass($class);
        });
    }

    /**
     * Return true if exists a proxy class
     *
     * @param $class
     * @return bool
     */
    public static function has($class)
    {
        return isset(self::$proxies[$class]);
    }

    /**
     * Set an Implementation of a Proxy
     *
     * @param $class
     * @param $implements
     * @return mixed
     */
    public static function set($class, $implements)
    {
        return self::$proxies[$class] = $implements;
    }

    /**
     * Create a Proxy Class to a Implementation
     *
     * @param $class
     */
    private static function createClass($class)
    {
    }

}