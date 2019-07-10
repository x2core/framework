<?php

namespace X2Core\Foundation\ViewSystem;

/**
 * Class RuleParse
 * @package X2Core\Foundation\ViewSystem
 *
 * This class represents interface of the properties to create a rule
 * to compile view source
 *
 * @property string name
 * @property string exp
 * @property string type
 * @property string|callable output
 */
class RuleParse
{
    /**
     * @param callable $fn
     */
    public function setCallback(callable $fn){
        $this->output = $fn;
    }

}