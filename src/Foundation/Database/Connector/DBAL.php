<?php

namespace X2Core\Foundation\Database\Connector;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DBAL
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * DBAL constructor.
     * @param $typeConnector
     * @param $host
     * @param $user
     * @param $password
     * @param $db
     */
    public function __construct($typeConnector, $host, $user, $password, $db)
    {
        $this->connection = self::makeConnection($typeConnector, $host, $user, $password, $db);
    }

    /**
     * @param $sql
     * @param $params
     * @param bool $fetch
     * @param $fail
     * @param null $default
     * @return array|null
     */
    public function getQuery($sql, $params, $fetch = true, $fail = null, $default = null){
        $data = null;
        $query = $this->connection->prepare($sql);
        $success = $query->execute($params);
        if($success AND  $fetch){
            $data = $query->fetchAll();
        }elseif(!$success AND $fail instanceof \Closure){
            $fail($this,$sql,$params);
        }
        return ($success) ? $data : $default;
    }

    public function doQuery($sql, $params, $fail){
        $this->getQuery($sql,$params,false,$fail);
    }

    /**
     * @param $typeConnector
     * @param $host
     * @param $user
     * @param $password
     * @param $db
     * @return  \Doctrine\DBAL\Connection
     */
    static function makeConnection($typeConnector, $host, $user, $password, $db){
        $config = new Configuration();
//..
        $connectionParams = array(
            'dbname' => $db,
            'user' => $user,
            'password' => $password,
            'host' => $host,
            'driver' => $typeConnector,
        );
        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

}