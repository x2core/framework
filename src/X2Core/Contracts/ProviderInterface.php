<?php

namespace X2Core\Contracts;


use X2Core\Contracts\ContainerInterface;

interface ProviderInterface
{
    /**
     * @param ContainerInterface $bundle
     *
     * @desc this method is to register services
     * @return void
     */
    public function register(ContainerInterface $bundle);

    /**
     * @return void
     */
    public function destroy(ContainerInterface $bundle);
}