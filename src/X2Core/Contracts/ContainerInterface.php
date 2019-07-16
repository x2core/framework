<?php

namespace X2Core\Contracts;


interface ContainerInterface extends \ArrayAccess
{
    public function singleton($name, $closure);
    public function install(ProviderInterface $provider);
    public function uninstall(ProviderInterface $provider);
    public function make($name, array $params);
}