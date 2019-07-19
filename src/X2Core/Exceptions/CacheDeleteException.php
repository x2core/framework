<?php

namespace Eyrene\Cache\Exceptions;


use Throwable;

class CacheDeleteException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Has been problem to try delete cache: ".$message, $code, $previous);
    }

}