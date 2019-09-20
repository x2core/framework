<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;
use X2Core\Validator\Validator;

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