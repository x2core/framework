<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class NotBlank extends AbstractRule
{

    public function onValidate($value)
    {
       return trim($value) !== "";
    }
}