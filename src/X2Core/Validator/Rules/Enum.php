<?php

namespace X2Core\Validator\Rules;

use X2Core\Validator\AbstractRule;

class Enum extends AbstractRule
{
    /**
     * @var array
     */
    private $values;

    /**
     * Enum constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function onValidate($value)
    {
       return in_array($value, $this->values);
    }
}