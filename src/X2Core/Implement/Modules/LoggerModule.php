<?php

namespace X2Core\Implement\Modules;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use X2Core\Application\Module;

/**
 * Class LoggerModule
 * @package X2Core\Implement\Modules
 */
class LoggerModule extends Module
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Init this module to register a workflow.
     * The implementations is required
     *
     * @return void
     */
    function install()
    {
        if(false){
            $this->bind(LoggerInterface::class, $this->logger = new Logger('app'), 'logger');
        }
    }
}