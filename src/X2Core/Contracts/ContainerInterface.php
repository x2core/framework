<?php

namespace X2Core\Contracts;


interface ContainerInterface
{
    public function differed($name, \Closure $closure);
    public function service($name,  $service);
    public function install(ProviderInterface $provider);
    public function uninstall(ProviderInterface $provider);
    public function make($name, array $params);
}