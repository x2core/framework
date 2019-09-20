<?php

namespace X2Core\Preset\Exceptions;


use Throwable;

class CacheConnectorException extends \Exception
{
    public function __construct($connector = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("This cache connector is not available :" . $connector, $code, $previous);
    }

}