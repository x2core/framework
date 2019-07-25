<?php

namespace X2Core\Exceptions;


use Throwable;

class NotImplementException extends \Exception
{
    public function __construct($method, $class)
    {
        parent::__construct("The method $method of class $class is not implemented" );
    }

}