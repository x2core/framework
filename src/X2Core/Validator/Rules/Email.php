<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class Email extends AbstractRule
{

    public function onValidate($value)
    {
       return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}