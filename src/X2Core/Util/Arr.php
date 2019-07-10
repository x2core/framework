<?php

namespace X2Core\Util;
use Exception;

/**
 * Class Arr
 * @package Eyrene\Support
 */
class Arr implements \Countable, \Iterator
{
    /**
     * @var array
     */
    private $arr;

    /**
     * @var boolean
     */
    private $isEnd;

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

    /**
     * @param array $arr
     * @param $indexes
     * @param $exception
     */
    public static function require(array $arr, $indexes, $exception){
        if(true !== $msg = self::contains($arr,$indexes,true))
            throw new $exception("the index {$msg} not is require");
    }

    /**
     * @param array $source
     * @param $target
     * @param array $hydration
     * @return string
     * @throws Exception
     */
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

    /**
     * @param $arr
     * @param $elm
     * @return bool
     */
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

    /**
     * @param $offset
     * @param $length
     */
    public function slice($offset, $length){
        $this->arr = array_slice($this->arr,$offset, $length);
    }

    /**
     * @param $offset
     * @param $length
     */
    public function splice($offset, $length){
        $this->arr = array_splice($this->arr,$offset, $length);
    }

    /**
     * @param $value
     */
    public function push($value){
        array_push($this->arr,$value);
    }

    /**
     * @return mixed
     */
    public function pop(){
        return array_pop($this->arr);
    }

    /**
     * @return mixed
     */
    public function shift(){
        return array_shift($this->arr);
    }

    /**
     * @param $value
     */
    public function unshift($value){
        $this->arr = array_unshift($this->arr, $value);
    }

    /**
     * @param $arr
     */
    public function merge($arr){
        $this->arr = array_merge($this->arr, $arr);
    }

    /**
     * @param $elm
     * @return false|int|string
     */
    public function search($elm){
        return array_search($this->arr, $elm);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->arr);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
      return current($this->arr);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->isEnd = (bool) next($this->arr);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->arr);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->isEnd;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->arr);
    }
}