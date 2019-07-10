<?php

namespace X2Core\Foundation\Validator\Rules;


use X2Core\Foundation\Validator\AbstractRule;
use X2Core\Foundation\Validator\Validator;

class Length extends AbstractRule
{
    private $options;

    /**
     * Length constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function onValidate($value)
    {
        $length = strlen($value);
        $maxEvaluated = ($minExist = isset($this->options['min'])) && ($this->options['min']) <= $length || !$minExist;
        $minEvaluated = ($maxExist = isset($this->options['max'])) && ($this->options['max']) >= $length || !$maxExist;
        return $maxEvaluated AND $minEvaluated;
    }
}