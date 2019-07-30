<?php

namespace X2Core\Util;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use TypeError;

/**
 * Class Reflection
 * @package X2Core\Util
 *
 * Utilities to make a reflection about class or function
 */
class Reflection
{
    /**
     * Return instance of ReflectionClass
     *
     * @param $class
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public static function classAnalyzer($class){
       return new ReflectionClass($class);
    }

    /**
     * @param $class
     * @return array
     * @throws ReflectionException
     */
    public static function classMethods($class){
        return (new ReflectionClass($class))->getMethods();
    }

    /**
     * @param $class
     * @return array
     * @throws ReflectionException
     */
    public static function classProperties($class){
        return (new ReflectionClass($class))->getProperties();
    }

    /**
     * Get basic info of all parameter of method, callable, function or Closure
     *
     * @param $target
     * @return mixed[]
     * @throws \Exception
     * @throws TypeError
     */
    public static function scanParameter($target){
        if($target instanceof Closure || is_callable($target)){
            $reflection = new ReflectionFunction($target);
        }elseif (is_object($target) && method_exists($target,'__invoke')){
            $reflection = new ReflectionClass($target);
            $reflection = $reflection->getMethod('__invoke');
        }elseif(is_string($target)){
            $elms = explode('@',$target);

            if(!isset($elms[1])){
                $elms[1] = '__invoke';
            }

            $class = new ReflectionClass($elms[0]);

            try{
                $reflection = $class->getMethod($elms[1]);
            }catch (ReflectionException $exception){
                throw new \Exception("class:method",0,$exception);
            }

        }else{
            throw new TypeError("the scanParameter method need Closure instance or string class:method}");
        }

        $result = [];
        foreach($reflection->getParameters() as $parameter){
            $result[$parameter->name] = [
                'type' => $parameter->hasType() ? $parameter->getType()->getName() : null,
                'nullable' => $parameter->allowsNull(),
                'position' => $parameter->getPosition(),
                'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null
            ];
        }

        return $result;
    }

}