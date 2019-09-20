<?php

namespace X2Core\Logger;

use Closure;
use X2Core\Preset\AbstractManager;

/**
 * Class Manager
 * @package X2Core\Logger
 */
class Manager extends AbstractManager
{
    /**
     * Extend the system
     *
     * @param $name
     * @param Closure $extension
     * @return mixed
     */
    public function extend($name, Closure $extension)
    {
        // TODO: Implement extend() method.
    }

    /**
     * Take the message emitted for other manager
     *
     * @param $manager
     * @param $data
     * @return mixed
     */
    public function emitted($manager, $data)
    {
        // TODO: Implement emitted() method.
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function initDriver(array $params)
    {
        return $this->{$params['type']}($params['parameters']);
    }

    /**
     * Create a handler to single logging
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function fileHandler(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(
                new StreamHandler(
                    $config['path'], $this->level($config),
                    $config['bubble'] ?? true, $config['permission'] ?? null, $config['locking'] ?? false
                ), $config
            ),
        ]);
    }

    /**
     * Create an instance of the Slack log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSlackDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new SlackWebhookHandler(
                $config['url'],
                $config['channel'] ?? null,
                $config['username'] ?? 'Laravel',
                $config['attachment'] ?? true,
                $config['emoji'] ?? ':boom:',
                $config['short'] ?? false,
                $config['context'] ?? true,
                $this->level($config),
                $config['bubble'] ?? true,
                $config['exclude_fields'] ?? []
            ), $config),
        ]);
    }

    /**
     * Create an instance of the syslog log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSyslogDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new SyslogHandler(
                Str::snake($this->app['config']['app.name'], '-'),
                $config['facility'] ?? LOG_USER, $this->level($config)
            ), $config),
        ]);
    }

    /**
     * Create an instance of the "error log" log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createErrorlogDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new ErrorLogHandler(
                $config['type'] ?? ErrorLogHandler::OPERATING_SYSTEM, $this->level($config)
            )),
        ]);
    }
}