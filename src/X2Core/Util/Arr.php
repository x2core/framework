<?php

namespace X2Core\Util;

use ArrayIterator;

/**
 * Class Arr
 * @package Eyrene\Support
 *
 * Several utilities to work with arrays
 */
class Arr implements \Countable, \Iterator, \ArrayAccess, \Serializable
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
     *
     * @param $arr
     */
    public function __construct($arr)
    {
        $this->arr = self::wrap($arr);
    }

    /**
     * if a value is not an array then wrap in array
     *
     * @param mixed $value
     * @return array
     */
    public static  function wrap($value){
        return is_array($value) ? $value : [$value];
    }

    /**
     * Check if in an array if found the keys
     *
     * @param array $arr
     * @param $indexes
     * @param bool $flag
     * @return bool|array
     */
    public static function contains(array $arr, $indexes,$flag = false){
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
     * The same that contains but throw a exception if is failed
     *
     * @param array $arr
     * @param $indexes
     * @param $exception
     */
    public static function require(array $arr, $indexes, $exception){
        if(true !== $msg = self::contains($arr,$indexes,true))
            throw new $exception("the index {$msg} not is require");
    }

    /**
     * Check if both array has same value by a key
     *
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
     * Method factory to create instance of self class
     *
     * @param array $arr
     * @return Arr
     */
    public static function to(array $arr){
        return new self($arr);
    }

    /**
     * Simple shortcut of in_array
     *
     * @param $arr
     * @param $elm
     * @return bool
     */
    public static function has($arr, $elm)
    {
        return in_array($elm, $arr);
    }

    /**
     * Simple implements of map function
     *
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
     * Simple shortcut of diff
     *
     * @param array ...$arr
     */
    public function diff( ...$arr){
        $this->arr = array_diff($this->arr,...$arr);
    }

    /**
     * Simple shortcut of slice
     *
     * @param $offset
     * @param $length
     */
    public function slice($offset, $length){
        $this->arr = array_slice($this->arr,$offset, $length);
    }

    /**
     * Simple shortcut of splice
     *
     * @param $offset
     * @param $length
     */
    public function splice($offset, $length){
        $this->arr = array_splice($this->arr,$offset, $length);
    }

    /**
     * Simple shortcut of push
     *
     * @param $value
     */
    public function push($value){
        array_push($this->arr,$value);
    }

    /**
     * Simple shortcut of pop
     *
     * @return mixed
     */
    public function pop(){
        return array_pop($this->arr);
    }

    /**
     * Simple shortcut of shift
     *
     * @return mixed
     */
    public function shift(){
        return array_shift($this->arr);
    }

    /**
     * Simple shortcut of unshift
     *
     * @param $value
     */
    public function unshift($value){
        $this->arr = array_unshift($this->arr, $value);
    }

    /**
     * Simple shortcut of merge
     *
     * @param $arr
     */
    public function merge($arr){
        $this->arr = array_merge($this->arr, $arr);
    }

    /**
     * Simple shortcut of search
     *
     * @param $elm
     * @return false|int|string
     */
    public function search($elm){
        return array_search($this->arr, $elm);
    }

    /**
     * Sort an array by keys
     *
     * @param bool $descending
     * @param null $sortFlags
     * @return bool
     */
    public function sortByKey($descending = false, $sortFlags = NULL){
       return $descending ? krsort($this->arr, $sortFlags) : ksort($this->arr, $sortFlags);
    }

    /**
     * Sort an array by values
     *
     * @param bool $descending
     * @param null $sortFlags
     * @return bool
     */
    public function sortByValue($descending = false, $sortFlags = NULL){
        return $descending ? rsort($this->arr, $sortFlags) : sort($this->arr, $sortFlags);
    }

    /**
     * Sort an array by custom comparision from user function or Closure
     *
     * @param callable $fn
     * @param int $target
     * @return bool
     */
    public function sortByComparison(callable  $fn, $target = 0){
        return $target ? uksort($this->arr, $fn) : usort($this->arr, $fn);
    }

    /**
     * @return mixed
     */
    public function min(){
        return min($this->arr);
    }

    /**
     * @return mixed
     */
    public function max(){
        return max($this->arr);
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

    /**
     * @return ArrayIterator
     */
    public function getIterator(){
        return new ArrayIterator($this->arr);
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0){
        return json_encode($this->arr, $options);
    }

    /**
     * @param null $name
     * @return mixed
     */
    public function toXml($name = NULL){
        return (new DOM("1.0", $name ?? "array"))->appendArray($this->arr)->toXML();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
       return isset($this->arr[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->arr[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
       $this->arr[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }

    public function __debugInfo()
    {
        var_dump($this->arr);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->arr);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
       $this->arr = unserialize($serialized);
    }
}