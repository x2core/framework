<?php

namespace X2Core\Container;

use Closure;

/**
 * Class InjectRule
 * @package X2Core\Container
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class InjectRule
{
    /**
     * An array associative with function to resolve the implements
     *
     * @var callable[]
     */
    private $distribute = [];

    /**
     * An array associative with function to resolve the implements
     *
     * @var callable
     */
    private $default;

    /**
     * @param $name
     * @param Closure $func
     */
    public function on($name, Closure $func){
        $this->distribute[$name] = $func;
    }

    /**
     * @return callable
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param Closure $default
     */
    public function setDefault( Closure $default)
    {
        $this->default = $default;
    }

    /**
     * @return callable[]
     */
    public function getDistribute()
    {
        return $this->distribute;
    }

    /**
     * @return Closure
     */
    public function findContext($name)
    {
        return $this->distribute[$name];
    }
}