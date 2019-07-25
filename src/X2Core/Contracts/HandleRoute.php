<?php

namespace X2Core\Contracts;


use Monolog\Handler\HandlerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface HandleRoute
 * @package X2Core\Contracts
 */
interface HandleRoute
{
    /**
     * @return void
     */
    public function next();

    /**
     * @param HandlerInterface $handler
     * @return mixed
     */
    public function pipe(HandlerInterface $handler);

    /**
     * @return void
     */
    public function finished();

    /**
     * @param $name
     * @return void
     */
    public function go($name);


}