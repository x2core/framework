<?php

namespace X2Core\Util;
use Exception;

/**
 * Class Arr
 * @package Eyrene\Support
 */

/**
 * Class Arr
 * @package Eyrene\Support
 */
class Arr
{
    /**
     * @var array
     */
    private $arr;

    /**
     * Arr constructor.
     * @param $arr
     */
    public function __construct($arr)
    {
        $this->arr = $arr;
    }

    /**
     * @param array $arr
     * @param $indexes
     * @param bool $flag
     * @return bool|array
     */
    static function contains(array $arr, $indexes,$flag = false){
        $collects = [];
        if(is_string($indexes)){
            $indexes = explode(',',$indexes);
        }

        foreach ($indexes as $index){
            if(!isset($arr[$index])){
               if ($flag){
                   $collects[] = $index;
               }
            }
        }
        return $flag ? $collects : true;
    }

    public static function require(array $arr, $indexes, $exception){
        if(true !== $msg = self::contains($arr,$indexes,true))
            throw new $exception("the index {$msg} not is require");
    }

    public static function hydrate(array $source, $target, array $hydration){
        if(!($is_object = is_object($target)) && !(is_string($target) && class_exists($target))){
            throw new Exception('The class not exists to hydrate');
        }

        $result = $is_object ? $target : new $target();

        foreach (array_keys($hydration) as $name => $value ){
            $result->{$name} = $source[$value];
        }

        return $result;
    }

    /**
     * @param $key
     * @param array ...$elms
     * @return bool
     */
    public static function testByKey($key, ...$elms){
        
        $length = count($elms);
        $result = true;
        $last = null;
        $i = 0;

        while ($i<$length) {
            $current = current($elms);
            if(isset($current[$key])){
                if($last === null){
                    $last = $current[$key];
                    continue;
                }

                if( $current[$key] !== $last){
                    $result = false;
                    break;
                }
            }else{
                    $result = false;
                    break;
            }
            next($elms);
            $i++;
        }
        return $result;
    }

    /**
     * @param array $arr
     * @return Arr
     */
    public static function to(array $arr){
        return new self($arr);
    }

    public static function has($arr, $elm)
    {
        return in_array($elm, $arr);
    }

    /**
     * @param $func
     * @return mixed
     * @internal param $name
     * @internal param array ...$arguments
     */
    public function map($func)
    {
        $result = [];
        $last = null;
        $i = 0;
        foreach ($this->arr as $current){
            if($last === false){
                break;
            }
            $last = $func($current,$i);
            $i++;
            $result[] = $last;
        }

        return $result;
    }

}