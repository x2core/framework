<?php

namespace X2Core;

use Closure;
use InvalidArgumentException;
use X2Core\Contracts\ContainerInterface;
use X2Core\Contracts\ProviderInterface;
use X2Core\Contracts\ServiceNotFound;
use X2Core\Exceptions\FactoryNotFound;
use X2Core\Exceptions\RuntimeException;
use X2Core\Util\Reflection;
use X2Core\Util\Runtime;
use X2Core\Util\Str;

/**
 * Class Application
 * @package X2Core
 * @author Oliver Valiente <oliver021val@gmail.com>
 * @abstract
 *
 * This a class to extend a dispatcher to manager flow control, configures, container and
 * provide a cover to wrapper a webApp or several system
 */
abstract class Application extends Dispatcher implements ContainerInterface
{
    use ConfigSupport;

    /**
     * version of the library
     * const VERSION
     */
    const VERSION = '2.1.0';

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
    private $env;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Closure[]
     */
    private $factories = [];

    /**
     * @var Closure[]
     */
    private $factoriesFn = [];

    /**
     * @var Closure[]
     */
    private $services = [];

    /**
     * @var Closure[]
     */
    private $builders = [];

    /**
     * @param $filename
     * @param $ext
     * @param $process
     * @return mixed|null
     */
    private static function processConfig($filename, $ext, $process)
    {
        if(isset($process[$ext]))
            return $process[$ext]($filename);
        else
            return NULL;

    }

    /**
     * @abstract
     * @return void
     */
    abstract public function deploy();

    /**
     * @abstract
     * @return void
     */
    abstract public function exit();

    /**
     * This function register to all value of file (.php) as config or (.?) process
     *
     * @param $path
     * @param array $process
     * @param string $prefix
     * @return void
     */
    public function setConfigDir($path, array $process = [], $prefix = "")
    {
        $files = scandir($path);
        $length = count($files);
        for($i = 0; $i < $length; $i++){
            $filename = $path . DIRECTORY_SEPARATOR . ($name = $files[$i]);
            $ext = Str::split($filename, '.', true)->current();
            Str::pullFromEnd($name, $ext);
            if(is_file($filename)){
                if (Str::end($filename, '.php')) {
                    $values = (include $filename);
                } else {
                    $values = self::processConfig($filename, $ext, $process);
                }
                if($values !== NULL)
                    $this->config($prefix !== "" ? $prefix . '.' . $name : $name, $values);
            }elseif (is_dir($filename)){
                $this->setConfigDir($filename,$process, $name);
            }
        }
    }

    /**
     * @param $typeTarget
     * @param Closure $builder
     */
    public function setBuilders($typeTarget, Closure $builder)
    {
        $this->builders[$typeTarget] = $builder;
    }

    /**
     * @param $service
     * @param null $alt
     * @return mixed
     */
    public function resolve($service, $alt = NULL){
        return $this->services[$service] ?? $alt;
    }

    /**
     * @param $service
     * @return bool
     */
    public function has($service){
        return isset($this->services[$service]);
    }

    /**
     * @param $service
     * @param $objService
     * @return $this
     */
    public function service($service, $objService){
        $this->services[$service] = $objService;
        return $this;
    }

    /**
     * Register a lazy service
     *
     * @param $service
     * @param Closure $fn
     * @return $this
     */
    public function differed($service, Closure $fn){
        $this->factoriesFn[$service] = $fn;
        return $this;
    }

    /**
     * Register a factory
     *
     * @param $name
     * @param Closure $action
     */
    public function factory($name, Closure $action){
        $this->factories[$name] = $action;
    }

    /**
     * Make an object through a factory
     *
     * @param $name
     * @param array[] $arguments
     * @return mixed
     * @throws FactoryNotFound
     */
    public function make($name, array $arguments){
        if(!isset($this->factories[$name])) throw new FactoryNotFound("the factory {$name} not found");
        return $this->call($this->factories[$name], $arguments);
    }

    /**
     * Call a action from several types sources and inject dependencies
     *
     * @param $source
     * @param $arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function call($source, $arguments = NULL)
    {
        $arguments = $this->resolveDependencies($source, $arguments);
        if($source instanceof Closure || is_callable($source)){
           return $source(...$arguments);
        }elseif (is_object($source) && method_exists($source,'__invoke')){
           return $source->__invoke(...$arguments);
        }elseif(is_string($source)){
            $elms = explode('@',$source);
            if(!isset($elms[1])){
                $elms[1] = '__invoke';
            }
            return Runtime::executeCall(new $elms[0], $elms[1], $arguments);
        }else{
            throw new RuntimeException('The action is not possible call');
        }
    }

    /**
     * Invoke a method register from provider to install
     * @param ProviderInterface $provider
     * @return void
     */
    public function install($provider){
        if(is_subclass_of($provider, ProviderInterface::class)){
            /** @var ProviderInterface $provider */
            $provider->register($this);
        }
    }

    /**
     * Uninstall a provider
     *
     * @param string $provider
     * @return void
     */
    public function uninstall($provider){
        if(is_subclass_of($provider, ProviderInterface::class)){
            /** @var ProviderInterface $provider */
            $provider->destroy($this);
        }
    }

    /**
     * Resolve dependencies of a action through refection
     *
     * @param $action
     * @param array|null $preset
     * @return array
     * @throws ServiceNotFound
     */
    private function resolveDependencies($action, array $preset = NULL){
        $result = [];
        $metadata =  Reflection::scanParameter($action);
        foreach ($metadata as $name => $current){
            $type = $current['type'];
            if($type !== NULL){
               if(isset($this->builders[$type]) && isset($preset[$name])){
                   $inject = $this->call($this->builders[$type], ["data" => $preset[$name]]);
               }else{
                   $inject = $this->resolve($type);
                   if($inject === NULL){
                       throw new InvalidArgumentException("In this action is not possible inject {$type}");
                   }
               }
            }elseif (isset($preset[$name])){
                $inject = $preset[$name];
            }elseif($current['nullable'] === true){
                $inject = $current['default'];
            }else{
                throw new InvalidArgumentException("the Argument $name is not possible resolve service or value");
            }
//            $result[] = [$name, $inject]; to future develop
            $result[] = $inject;
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     * Check true if environment mode is development
     *
     * @return bool
     */
    public function isDevelopment()
    {
        return $this->env === self::DEVELOPMENT;
    }

    /**
     * @param int $env
     */
    public function setEnvironment($env)
    {
        $this->env = $env;
    }

    /**
     * @param string $target
     * @return mixed
     */
    public function getPath($target = "")
    {
        return $this->path . $target;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}