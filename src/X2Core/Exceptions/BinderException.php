<?php

namespace X2Core;


use Throwable;

class BinderException extends \Exception
{

    /**
     * BinderException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = NULL, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? "The binder is not valid", $code, $previous);
    }
}