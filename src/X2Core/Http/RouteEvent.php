<?php

namespace X2Core\Http;

use Generator;
use Traversable;
/**
 * Class RouteEvent
 * @package X2Core\Http
 */
class RouteEvent implements \IteratorAggregate
{
    /**
     * @var Generator
     */
    private $matches;

    /**
     * RouteEvent constructor.
     * @param Generator $matches
     */
    public function __construct(Generator $matches)
    {
        $this->matches = $matches;
    }

    /**
     * @return mixed
     */
    public function shiftRoute(){
        return $this->matches->current();
    }

    /**
     * @return bool
     */
    public function have(){
        return $this->matches->valid();
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
       return $this->matches;
    }
}