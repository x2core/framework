<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;
use X2Core\Foundation\Validator\Validator;

class Same extends AbstractRule
{
    /**
     * @var string
     */
    private $name;

    /**
     * Email constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function onValidate($value)
    {
       return ($value === $validator->getValue($this->name));
    }
}