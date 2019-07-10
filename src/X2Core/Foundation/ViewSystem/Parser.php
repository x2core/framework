<?php

namespace X2Core\Foundation\ViewSystem;


class Parser
{
    /**
     * @var HandlerSrc
     */
    private $handle;

    /**
     * Parser constructor.
     * @param $handle
     */
    public function __construct(HandlerSrc $handle)
    {
        $this->handle = $handle;
    }

    public function parseLine($src){
        preg_match("/\{\{(.*)\\}}/", $src, $matches);
        foreach ($matches as $chuck){
            $this->handle->write($src, trim($chuck));
        }
    }

}