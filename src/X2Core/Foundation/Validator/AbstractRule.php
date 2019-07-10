<?php

namespace X2Core\Foundation\Validator;

abstract class AbstractRule
{
    /**
     * @param $value
     * @return mixed
     * @internal param Validator $validator
     */
    abstract public function onValidate($value);
}