<?php

namespace X2Core\Util;
use BadMethodCallException;
use Error;
use Closure;

/**
 * Class Runtime
 * @package X2Core\Util
 *
 * Class to manager runtime and handler errors and provide tools to runtime control
 */
class Runtime
{
    /**
     * @var mixed
     */
    private static $runTarget;

    /**
     * @var callable
     */
    private static $detectHandle;

    /**
     * @var array[]
     */
    private static $bufferTags = [];

    /**
     * Set to handle to errors
     *
     * @param callable $handle
     * @return void
     */
    public static function handleError($handle){
       set_error_handler($handle);
    }

    /**
     * Set to handle to critical errors
     *
     * @param callable $handle
     * @return void
     */
    public static function handleErrorStrict($handle){
        set_error_handler($handle, E_STRICT);
    }

    /**
     * @param callable $handle
     *
     *
     * @desc Set to handle to exceptions
     * @return void|mixed
     */
    public static function handleExceptions($handle){
        set_exception_handler($handle);
    }

    /**
     * To prepare mode to detect run tags
     *
     * @param null|string|array|callable
     * @param null|callable
     * @return void
     */
    public static function detectRunTags(){
        $arguments = func_get_args();
        if (is_string($arguments[0]) || is_array($arguments[0])){
            self::$runTarget = $arguments[0];
        }elseif (is_callable($arguments[0])){
            self::$runTarget = 1;
            self::$detectHandle = $arguments[1];
            return;
        }else{
            return;
        }

        if(is_callable($arguments[1])){
            self::$detectHandle = $arguments[1];
        }
    }

    /**
     * Emmit a tag to trigger handle with payload context
     *
     * @param $tag
     * @param $payload
     * @return void
     */
    public static function trigger($tag, $payload){
        if(self::$runTarget === 1 ||
            is_string($tag) && $tag === self::$runTarget ||
            is_array(self::$runTarget) && in_array($tag, self::$runTarget)){
            self::$bufferTags[] = [$tag, $payload];
            if(is_callable(self::$runTarget)){
                (self::$runTarget)($tag, $payload);
            }
        }
    }

    /**
     * Execute and reset buffer array of tags
     *
     * @param callable $fn
     * @return void
     */
    public static function flush($fn){
        $length = count(self::$bufferTags);
        for($i = 0; $i < $length; $i++){
            $fn(...self::$bufferTags[$i]);
        }
    }

    /**
     * Reset buffer array of tags
     *
     * @return void
     */
    public static function resetBufferTags(){
        self::$bufferTags = [];
    }

    /**
     * Execute a method call to the given object.
     *
     * @param  object $object
     * @param  string $method
     * @param  array $parameters
     * @param callable|null $fallback
     * @return mixed
     * @throws Error
     */
    public static function executeCall($object, $method, $parameters, callable $fallback = NULL)
    {
        try {
            return $object->{$method}(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            if($fallback === NULL){
                throw $e;
            }
           return $fallback($e, $object, $method, $parameters);
        }
    }

    /**
     * Make an action from of distinct types of sources
     *
     * @param Closure|callable|string $srcAction
     * @param array|NULL $inject
     * @return Closure
     */
    public static function call($srcAction, array $inject = NULL){
            if (is_callable($srcAction)){
                return $srcAction(...$inject);
            }elseif(is_string($srcAction)){
                $chunk = explode('@', $srcAction);
                $instance =  new $chunk[0];
                if(isset($chunk[1])){
                    return $instance->{$chunk[1]}(...$inject);
                }else{
                    return $inject(...$inject);
                }
            }elseif ($srcAction instanceof  Closure){
                return $srcAction(...$inject);
            }
            throw new \RuntimeException('the action if not valid');
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
       if($name[0] === 'i' && $name[0] === 's'){
           $name = Str::slice($name, 2);
           if(!isset($arguments[0])){
               throw new \Exception("Runtime method magic 'is' need a argument");
           }
           return is_a($name, $arguments[0], TRUE);
       }else{
           throw new \Exception("Runtime method not found");
       }
    }
}