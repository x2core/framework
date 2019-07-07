<?php

namespace X2Core\Foundation\ViewSystem;


class HandlerSrc
{

    /**
     * mixed[]
     */
    private $modules;

    /**
     * @param $src
     * @param $chuck
     */
    public function write(&$src, $chuck)
    {
        if($chuck[0] === ">"){
            array_splice($chuck, 1);
            $this->call(trim($chuck));
        }else{
            $src = preg_replace('', '', $src);
        }
    }

    private function call($chuck)
    {

    }
}