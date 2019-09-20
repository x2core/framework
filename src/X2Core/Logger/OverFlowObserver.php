<?php

namespace X2Core\Logger;


use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\OverflowHandler;

class OverFlowObserver
{
    public static function observer(Logger $logger, HandlerInterface $handler, array $options){
//         $logger->pushHandler(new OverflowHandler($handler, $options);
    }

}