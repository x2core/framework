<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;

class AlphaNum extends AbstractRule
{

    public function onValidate($value)
    {
       return preg_match("/^[A-z]|[0-9]*$/", $value);
    }
}