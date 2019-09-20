<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class Url extends AbstractRule
{

    public function onValidate($value)
    {
       return (bool) filter_var($value, FILTER_VALIDATE_URL);
    }
}