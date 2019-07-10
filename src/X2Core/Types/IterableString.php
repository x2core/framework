<?php

namespace X2Core\Types;

/**
 * Class IterableString
 * @package X2Core\Types
 */
class IterableString implements \Iterator, \Countable
{
    /**
     * @var
     */
    private $str;

    /**
     * @var int
     */
    private $pointer = 0;

    /**
     * @var int
     */
    private $length;

    /**
     * IterableString constructor.
     * @param $str
     */
    public function __construct($str)
    {
        $this->str = $str;
        $this->length = strlen($str);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->str[$this->pointer];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
       $this->pointer++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->pointer;
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
       return  $this->pointer < $this->length;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->pointer = 0;
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
       return $this->length;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->str;
    }

    /**
     * @return mixed
     */
    public function __sleep()
    {
        return $this->str;
    }

//    public function __wakeup()
//    {
//
//    }
}