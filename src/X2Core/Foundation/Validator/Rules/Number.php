<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;
use X2Core\Foundation\Validator\Validator;

class Number extends AbstractRule
{
    const INTEGER = 'int';

    /**
     * @var
     */
    private $type;

    /**
     * Number constructor.
     * @param $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function onValidate($value)
    {
       return (bool) filter_var($value, $this->type === self::INTEGER ? FILTER_VALIDATE_INT : FILTER_VALIDATE_FLOAT);
    }
}