<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;

class Ip extends AbstractRule
{

    public function onValidate($value)
    {
       return (bool) filter_var($value, FILTER_VALIDATE_IP);
    }
}