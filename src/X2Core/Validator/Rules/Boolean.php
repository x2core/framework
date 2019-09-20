<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

class Boolean extends AbstractRule
{

    /**
     * @param $value
     * @return bool
     * @internal param Validator $validator
     */
    public function onValidate($value)
    {
       return (bool) filter_var($value,  FILTER_VALIDATE_BOOLEAN);
    }
}