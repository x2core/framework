<?php

namespace X2Core\Foundation\Events;



class HttpError
{
    /**
     * @var int
     */
    private $code;

    /**
     * HttpError constructor.
     * @param int $code
     */
    public function __construct ($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

}