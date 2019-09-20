<?php

namespace X2Core\Logger;


use Throwable;

class ThrowableLog extends \Error
{
    /**
     * @var int
     */
    private $level;

    public function __construct($message, $level, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

}