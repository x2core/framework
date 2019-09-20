<?php

namespace X2Core\Validator\Rules;


use X2Core\Validator\AbstractRule;

/**
 * Class Date
 * @package X2Core\Validator\Rules
 */
class Date extends AbstractRule
{
    /**
     * @var
     */
    private $format;

    /**
     * Date constructor.
     * @param null $format
     */
    public function __construct($format = NULL)
    {
        $this->format = $format;
    }

    /**
     * @param $value
     * @return bool
     * @internal param Validator $validator
     */
    public function onValidate($value)
    {
       return (($this->format) ? date_parse_from_format($this->format, $value)
               : date_parse($value)) !== false;
    }
}