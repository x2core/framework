<?php

namespace X2Core\Validator;

abstract class AbstractRule
{
    /**
     * @param $value
     * @return mixed
     * @internal param Validator $validator
     */
    abstract public function onValidate($value);
}