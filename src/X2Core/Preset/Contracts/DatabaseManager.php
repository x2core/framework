<?php

namespace X2Core\Preset\Contracts;

use Doctrine\DBAL\Driver\Connection;
/**
 * Interface DatabaseManager
 * @package X2Core\Preset\Contracts
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
interface DatabaseManager
{
    const STD_KEY_NAME = 'id';

    /**
     * @param $query
     * @param array $data
     * @return array
     */
    public function raw($query, array $data = []);

    /**
     * @param $table
     * @param $data
     * @return bool
     */
    public function insert($table, $data);

    /**
     * @param $table
     * @param $id
     * @param string $field
     * @return mixed
     */
    public function find($table, $id, $field = DatabaseManager::STD_KEY_NAME);

    /**
     * @param $table
     * @param $rules
     * @param null $values
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function findByRules($table, $rules, $values = NULL, $limit = NULL, $offset = NULL);

    /**
     *
     * @param $from
     * @param $target
     * @param array $relation
     * @param array|null $rules
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function findByRelation($from, $target, array $relation, array $rules = NULL,  $limit = NULL, $offset = NULL);

    /**
     * @param $table
     * @param array $data
     * @param $id
     * @param string $key
     * @return bool
     */
    public function update($table, array $data, $id, $key = DatabaseManager::STD_KEY_NAME);

    /**
     * @param $table
     * @param $id
     * @param string $field
     * @return bool
     */
    public function delete($table, $id, $field = DatabaseManager::STD_KEY_NAME);

    /**
     * @param callable $fn
     */
    public function ifHasError(callable $fn);

    /**
     * @param string $name
     * @return Connection
     */
    public function getConnections($name = "");

    /**
     * @param string $connection
     */
    public function changeConnection( $connection);

    /**
     * @param $name
     * @param Connection $connection
     * @return
     */
    public function addConnection($name, Connection $connection);

    /**
     * @param $model
     * @param null $key
     * @return mixed
     */
    public function model($model, $key = NULL);
}