<?php

use X2Core\Foundation\Services\View;

/**
 * Class ViewTest
 * @desc test system view
 */
class ViewTest extends TestsBasicFramework
{
    /**
     * @return mixed
     */
    public function run()
    {
        $viewPath = __DIR__ . DIRECTORY_SEPARATOR . 'views';
        $cache = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
        $v = new View($viewPath, $cache);
        $result = $v->render("hello.html.twig", ['num' => 42]);
        $this->assert("Hello and number is 42", trim($result));
        $v->setPipeFilter('square', function($str){
            $str = (int) $str;
            return (string) $str * $str;
        });
        $result = $v->render("first.html.twig", ['num' => 3]);
        $this->assert( "Hello and number is 9", trim($result) );
    }
}