<?php
namespace X2Core\Application;

use X2Core\Container\Container;

/**
 * Class Application
 * @package X2Core\Application
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * Base to implements a web application
 * The web or another application need 
 * a core to manager services, modules, flows and more features
 * This application support the inject dependecy and service deffred, 
 * with set of more functionality to make more easy the control flow
 */
abstract class Application extends Container
{
    /**
     * constant number version
     */
    const VERSION = "1.0.1";

    /**
     * const DEVELOPMENT
     */
    const DEVELOPMENT = 1;

    /**
     * const PRODUCTION
     */
    const PRODUCTION = 2;

    /**
     * @var int
     */
    protected $environment;

    /**
     * @var Module[]
     */
    protected $installed = [];

    /**
     * @var mixed[]
     */
    private $features;

    /**
     * Return the version tag
     *
     * @return string
     */
    public static function version(){
        return static::VERSION;
    }

    public function __construct()
    {
        $this->register(static::class, $this);
        $this->alias('app', static::class);
        $this->alias(\X2Core\Preset\Contracts\Container::class, static::class);
    }

    /**
     * Install a module to this application
     * This method is call installation of a module
     *
     * @param $module
     * @throws ApplicationException
     */
    public function bootModule($module){
        if(in_array(self::class, class_parents($module))){
            throw new ApplicationException('This class is not module because not inherit of ' . self::class);
        }

        /* @var Module $moduleInstance */
        $moduleInstance = new $module($this);
        $dependencies = $moduleInstance->getDependencies();
        foreach ($dependencies as $dependency){
            if(!isset($this->installed[$module])){
                $this->bootModule($dependency);
            }
        }

        $this->installed[$module] = $moduleInstance;
        $moduleInstance->install();
    }

    /**
     * Detach all services and variables of a module
     *
     * @param $module
     */
    public function removeModule($module){
        if(isset($this->installed[$module])){
            $this->installed[$module]->uninstall();
        }
    }

    /**
     * Invoke a functionality preset from a module
     *
     * @param string $feature
     * @param array|null $payload
     * @throws ApplicationException
     */
    public function bridge($feature,  array $payload = NULL){
        if(!isset($this->installed[$feature])){
            throw new ApplicationException('The bridge with ' . $feature . 'is not possible because this module is not installed');
        }

        $this->features[$feature]->execute($payload);
    }

    /**
     * Return true if the module is installed
     *
     * @param $module
     * @return bool
     */
    public function isInstalled($module){
        return in_array($module, $this->installed);
    }

    /**
     * Deploy Application
     * This method should implement the action that going to initialize all flow
     *
     * @abstract
     */
    abstract function run();

    /**
     * Return a register with all modles installed.
     * Onnly names
     *
     * @return Module[]
     */
    public function getInstalled()
    {
        return array_keys($this->installed);
    }

    /**
     * @return int
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param int $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Return true if environment mode is development
     *
     * @return boolean
     */
    public function isDevelopment()
    {
        return $this->environment === self::DEVELOPMENT;
    }
}