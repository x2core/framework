<?php

namespace X2Core\Preset\Contracts;


interface LinearSourceInterface
{
    public function length();

    public function next();

    public function current();

    public function isEnd();

    public function currentToEqual();

    public function makeBuffer($len);

}