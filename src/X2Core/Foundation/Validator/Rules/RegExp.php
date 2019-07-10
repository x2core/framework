<?php

namespace X2Core\Foundation\Validator\Rules;

use X2Core\Foundation\Validator\AbstractRule;

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