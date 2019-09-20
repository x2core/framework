<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class Type extends AbstractRule
{

    public function onValidate($value)
    {
       return gettype($value) === $type;
    }
}