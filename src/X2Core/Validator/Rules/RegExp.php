<?php

namespace X2Core\Validator\Rules;

use X2Core\Validator\AbstractRule;

class RegExp extends AbstractRule
{
    /**
     * @var
     */
    private $regexp;

    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    public function onValidate($value)
    {
       return (bool) preg_match("/{$this->regexp}/",$value);
    }
}