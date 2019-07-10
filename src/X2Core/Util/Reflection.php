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
 */
class Reflection
{
    /**
     * @param $class
     * @return ReflectionClass
     * @throws ReflectionException
     */
    static function classAnalyzer($class){
       return new ReflectionClass($class);
    }

    /**
     * @param $class
     * @return array
     * @throws ReflectionException
     */
    static function classMethods($class){
        return (new ReflectionClass($class))->getMethods();
    }

    /**
     * @param $class
     * @return array
     * @throws ReflectionException
     */
    static function classProperties($class){
        return (new ReflectionClass($class))->getProperties();
    }

    /**
     * @param $target
     * @return mixed[]
     * @throws \Exception
     * @throws TypeError
     */
    static function scanParameter($target){
        if($target instanceof Closure){
            $reflection = new ReflectionFunction($target);
        }elseif (is_object($target) && method_exists($target,'__invoke')){
            $reflection = new ReflectionClass($target);
            $reflection = $reflection->getMethod('__invoke');
        }elseif(is_string($target)){
            $elms = explode(':',$target);

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