<?php

namespace X2Core\Preset\Exceptions;

use Throwable;

class InvalidRuleTypeException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("The rule {$message} is not Descriptor Class Type", $code, $previous);
    }

}