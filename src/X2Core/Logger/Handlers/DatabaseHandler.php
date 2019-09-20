<?php

namespace X2Core\Logger\Handlers;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use X2Core\Preset\Contracts\DatabaseManager;

/**
 * Class DatabaseHandler
 * @package X2Core\Logger\Handlers
 */
class DatabaseHandler extends AbstractProcessingHandler
{
    /**
     * @var DatabaseManager
     */
    private $database;
    /**
     * @var string
     */
    private $tableName;

    /**
     * EventHandler constructor.
     * @param DatabaseManager $database
     * @param bool|int $level
     * @param bool $bubble
     */
    public function __construct(DatabaseManager $database, $level = Logger::DEBUG, $bubble = true, $tableName = 'logs')
    {
        parent::__construct($level, $bubble);
        $this->database = $database;
        $this->tableName = $tableName;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $this->database->insert($this->tableName, [
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U')
        ]);
    }
}