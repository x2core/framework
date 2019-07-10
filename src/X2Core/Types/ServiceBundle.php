<?php

namespace X2Core\Types;

use Closure;
use X2Core\Contracts\ProviderInterface;
use X2Core\Contracts\ServiceNotFound;
use X2Core\Util\Reflection;

/**
 * Class ServiceBundle
 * @package X2Core\Types
 */
class ServiceBundle extends Bundle
{

    /**
     * @var Closure[]
     */
    private $factories;

    /**
     * @param $name
     * @override
     * @return mixed
     */
    public function __get($name)
    {
        $src = parent::__get($name);
        if($src instanceof Closure){
            $src = $this->{$name} = $src($this);
        }
        return $src;
    }

    /**
     * @param string $provider
     */
    public function install($provider){
        if(is_subclass_of($provider, ProviderInterface::class)){
            /** @var ProviderInterface $provider */
            $provider->register($this);
        }
    }

    /**
     * @param string $provider
     */
    public function uninstall($provider){
        if(is_subclass_of($provider, ProviderInterface::class)){
            /** @var ProviderInterface $provider */
            $provider->destroy($this);
        }
    }

    /**
     * @param $action
     * @return array
     * @throws ServiceNotFound
     */
    public function resolveDependencies($action){
        $result = [];
        $metadata =  Reflection::scanParameter($action);
        foreach ($metadata as $name => $current){
            $type = $current['type'];
            if($type !== null){
                $inject = $this->__get($type);
                if($inject === null && $current['nullable'] === false){
                    throw new ServiceNotFound("In the action is not possible inject {$type}");
                }
                $result[] = $inject;
            }elseif($current['nullable'] === true){
                $result[] = $current['default'];
            }
        }
        return $result;
    }

    /**
     * @param $name
     * @param Closure $action
     */
    public function factory($name, Closure $action){
        $this->factories[$name] = $action;
    }

    /**
     * @param $name
     * @throws ServiceNotFound
     * @return mixed
     */
    public function make($name, array $arguments = []){
        if(!isset($this->factories[$name])) throw new ServiceNotFound("the factory {$name} not found");
        return $this->factories[$name]->call($this, ...$arguments);
    }

    /**
     * @param $name
     * @param mixed $action
     */
    public function singleton($name, $action){
        $this->{$name} = $action;
    }
}