<?php
// @formatter:off

namespace PHPSTORM_META {

    use Monolog\Logger;
    use X2Core\Application\Application;
    use X2Core\Preset\Contracts\Config;
    use X2Core\Preset\Contracts\Container;
    use X2Core\Preset\Contracts\Dispatcher;

    /**
     * PhpStorm Meta file, to provide autocomplete information for PhpStorm
     */
    override( Container::resolve(0), map([
            '' => '@',
            'config' => Config::class,
            'events' => Dispatcher::class,
        ])
    );

    /**
     * PhpStorm Meta file, to provide autocomplete information for PhpStorm
     */
    override( Application::bridge(), map([
            '' => '@',
            'getMonolog' => Logger::class
        ])
    );

    override( \resolve(0), map([
            '' => '@',
            'config' => Config::class,
            'events' => Dispatcher::class,
        ])
    );
}