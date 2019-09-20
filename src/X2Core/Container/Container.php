<?php

namespace X2Core\Container;

use Closure;
use InvalidArgumentException;
use X2Core\Preset\Contracts\Container as ContainerInterface;
use X2Core\Preset\Exceptions\FactoryNotFound;
use X2Core\Preset\Exceptions\RuntimeException;
use X2Core\Util\Reflection;
use X2Core\Util\Runtime;

/**
 * Class Application
 * @package X2Core
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * This a class to flow services, instances and
 * dependencies a cover to wrapper a webApp or several system
 */
 class Container implements ContainerInterface
{
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
     * @var array
     */
    private $alias = [];

    /**
     * @var array
     */
    private $loaders = [];

     /**
      * @var mixed[]
      */
     private $contexts;

     /**
      * @var InjectRule[]
      */
     private $rules = [];

     /**
     * Set a Closure to execute to build dinamyc dependencies
     *
     * @param $typeTarget
     * @param Closure $builder
     */
    public function setBuilder($typeTarget, Closure $builder)
    {
        $this->builders[$typeTarget] = $builder;
    }

     /**
      * @param $id
      * @return mixed
      */
    public function get($id){
        return $this->resolve($id, NULL);
    }

    /**
     * @param $service
     * @param null $alt
     * @return mixed
     */
    public function resolve($service, $alt = NULL){
        if(!$this->has($service) && isset($this->factoriesFn[$service])){
            $serviceWorking = $this->inject($this->factoriesFn[$service], $this->resolveContext($service));
            $this->register($service, $serviceWorking);
            return $serviceWorking;
        }
        return $this->services[$service]
            ??
            (isset($this->alias[$service]) ? $this->services[$this->alias[$service]] : $alt);
    }

    /**
     * @param $service
     * @return bool
     */
    public function has($service){
        return isset($this->services[$service]) || isset($this->alias[$service]);
    }

    /**
     * @param $service
     * @param $objService
     * @return $this
     */
    public function register($service, $objService){
        if(is_string($objService)){
            $this->services[$service] = $this->inject($objService);
        }else{
            $this->services[$service] = $objService;
            $this->execLoaders($service);
        }
        return $this;
    }

     /**
      * {@inheritdoc}
      */
     public function alias($service, $objService){
         $this->alias[$service] = $objService;
         return $this;
     }

     /**
      * @param $service
      * @param $context
      * @return $this
      */
     public function context($service, array $context){
         $this->contexts[$service] = $context;
         return $this;
     }

     /**
      * Remove a instance of container
      *
      * @param $service
      */
     public function remove($service){
         if (isset($this->services[$service]))
             unset($this->services[$service]);
         if(isset($this->alias[$service]))
             unset($this->services[$service]);
     }

     /**
      * Register an service that can has multi implementations
      *
      * @param $target
      * @return InjectRule
      */
    public function whenInjected($target){
        return $this->rules[$target] = new InjectRule;
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
     * @param $action
     */
    public function factory($name, $action){
        $this->factories[$name] = $action;
    }

     /**
      * Create an object through a factory
      *
      * @param $name
      * @param array[] $arguments
      * @param bool $noInject
      * @return mixed
      * @throws ContainerException
      * @throws FactoryNotFound
      */
    public function create($name, array $arguments = [], $noInject = false){
        if(!isset($this->factories[$name])) throw new FactoryNotFound("the factory {$name} not found");
        $factor = $this->factories[$name];
        if($factor instanceof Closure){
            return $noInject ?
                ($this->factories[$name])->call($this, ...$arguments) : $this->call($this->factories[$name], $arguments);
        }elseif (is_string($factor)){
            try{
                return $noInject ? new $factor(...$arguments) : $this->inject($factor, $arguments);
            }catch (ContainerException $exception)
            {
                throw new ContainerException('The factory class is not exist');
            }
        }else{
            throw new ContainerException('The factory is a invalid type');
        }
    }

     /**
      * Create an instance from a class and dependency injection
      *
      * @param $source
      * @param array $arguments
      * @param array|null $override
      * @return mixed
      * @throws ContainerException
      */
     public function inject($source, array $arguments = NULL, array $override = NULL)
     {
         if(!class_exists($source)){
             throw new ContainerException('The class that intended to inject not exists');
         }
         // fetch argument to resolve dependencies that to be inject
         $arguments = $this->resolveDependencies($source, $arguments, $override);
         return new $source(...$arguments);
     }

     /**
      * Call a action from several types sources and inject dependencies
      *
      * @param $source
      * @param array $arguments
      * @param array|null $override
      * @return mixed
      * @throws RuntimeException
      */
    public function call($source, array $arguments = NULL, array $override = NULL)
    {
        // fetch argument to resolve dependencies that to be inject
        $arguments = $this->resolveDependencies($source, $arguments, $override);

        // execute action base on type of source of the action
        if($source instanceof Closure || is_callable($source)){
            return $source(...$arguments);
        }elseif (is_object($source) && method_exists($source,'__invoke')){

            // to call a object this should has a method __invoke
            return $source->__invoke(...$arguments);
        }elseif(is_string($source)){
            $elms = explode('@',$source);
            if(!isset($elms[1])){
                $elms[1] = '__invoke';
            }
            return Runtime::executeCall(new $elms[0], $elms[1], $arguments);
        }else{

            // if source action is not string, object closure or callable then throw a exception
            throw new RuntimeException('The action is not possible call');
        }
    }

     /**
      * Resolve dependencies of a action through refection
      *
      * @param $action
      * @param array|null $preset
      * @param array|null $override
      * @return array
      */
    private function resolveDependencies($action, $preset, $override){
        $result = [];
        $metadata =  Reflection::scanParameter($action);
        foreach ($metadata as $name => $current){
            $type = $current['type'];
            if($type !== NULL){
                if( isset($preset[$name]) && (isset($this->builders[$type]) || $typeOver = $this->fetchBuilder($type))){
                    $inject = $this->call($this->builders[$typeOver ?? $type] , [
                        "data" => $preset[$name],
                        "name" => $name,
                        "class" => $type
                    ]);
                }else{
                    $inject = $override[$type] ?? $this->resolve($type, NULL);
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
            $result[] = $inject;
        }
        return $result;
    }

    /**
     * Register a callback that interact with an service when is loaded
     *
     * @param $class
     * @param callable $fn
     */
    public function whenLoaded($class, callable $fn)
    {
        if(!isset( $this->loaders[$class])){
            $this->loaders[$class] = [];
        }
        $this->loaders[$class][] = $fn;
    }

    /**
     * Execute a group the callable when a service is loaded
     *
     * @param $service
     */
    private function execLoaders($service){
        if(isset( $this->loaders[$service])){
            $loaders = $this->loaders[$service];
            foreach ($loaders as $loader){
                $this->call($loader);
            }
        }
    }

    /**
     * Return a builder if is subclass of type hint
     *
     * @param $type
     * @return bool
     */
    private function fetchBuilder($type)
    {
        $classes = class_parents($type);
        foreach ($classes as $class){
            if(isset($this->builders[$class]))
                return $class;
        }
        return false;
    }

     /**
      * Return a group the primitives argument or result of callable to inject
      *
      * @param $service
      * @return mixed|null
      */
     private function resolveContext($service)
     {
         if(!isset($this->contexts[$service])){
             return NULL;
         }

         // recovery the arguments to iterate and collect
         $arguments = $this->contexts[$service];

         foreach ($arguments as $key => $argument){
             if(($argument) instanceof Closure){
                $arguments[$key] = $this->call($argument);
             }
         }

         return $arguments;
     }
 }