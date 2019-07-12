<?php

namespace X2Core\Foundation\Providers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use X2Core\Types\ServiceBundle;
use X2Core\Contracts\ProviderInterface;
use X2Core\Foundation\Services\Log as AppLog;

class Log implements ProviderInterface
{

    /**
     * @param ServiceBundle $bundle
     *
     * @desc this method is to register services
     * @return void
     */
    public function register(ServiceBundle $bundle)
    {
        $bundle->factory(Logger::class, function($name, $handler){
            return new Logger($name, $handler);
        });

        $bundle->singleton(AppLog::class, function() use ($bundle) {
           return $bundle->make(Logger::class,
               'AppMainLog', new StreamHandler($this));
        });
    }

    /**
     * @return void
     */
    public function destroy(ServiceBundle $bundle)
    {
        if(isset($bundle->{AppLog::class}))
            unset($bundle->{AppLog::class});
    }
}