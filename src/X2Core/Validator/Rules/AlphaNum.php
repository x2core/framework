<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class AlphaNum extends AbstractRule
{

    public function onValidate($value)
    {
       return preg_match("/^[A-z]|[0-9]*$/", $value);
    }
}