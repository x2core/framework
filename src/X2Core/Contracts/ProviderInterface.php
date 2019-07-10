<?php

namespace X2Core\Contracts;


use X2Core\Types\ServiceBundle;

interface ProviderInterface
{
    /**
     * @param ServiceBundle $bundle
     *
     * @desc this method is to register services
     * @return void
     */
    public function register(ServiceBundle $bundle);

    /**
     * @return void
     */
    public function destroy(ServiceBundle $bundle);
}