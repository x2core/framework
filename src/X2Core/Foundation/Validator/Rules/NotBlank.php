<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;

class NotBlank extends AbstractRule
{

    public function onValidate($value)
    {
       return trim($value) !== "";
    }
}