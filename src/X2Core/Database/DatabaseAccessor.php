<?php

namespace X2Core\Database;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use X2Core\Preset\Contracts\DatabaseManager;
use X2Core\Util\Str;
/**
 * Class DatabaseAccessor
 * @package Eyrene\Database\Connector
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class DatabaseAccessor implements DatabaseManager
{
    /**
     * Available drivers
     */
        const PDOMySQLDriver  = 'pdo_mysql';
        const PDOSQLiteDriver  = 'pdo_sqlite';
        const PDOPgSQLDriver  = 'pdo_pgsql';
        const PDOOCIDriver    = 'pdo_oci';
        const OCI8Driver     = 'oci8';
        const DB2Driver    = 'ibm_db2';
        const PDOSQLSrvDrive  = 'pdo_sqlsrv';
        const MySQLiDriver    = 'mysqli';
        const DrizzlePDOMySQLDriver = 'drizzle_pdo_mysql';
        const SQLAnywhereDriver= 'sqlanywhere';
        const SQLSrvDriver   = 'sqlsrv';

    /**
     * @var ConnectionInterface[]
     */
    protected $connections;

    /**
     * @var Connection
     */
    private $current;

    /**
     * Factory to create a connection object
     *
     * @param $typeConnector
     * @param $host
     * @param $user
     * @param $password
     * @param $db
     * @param callable|null $fn
     * @return Connection
     */
    static function makeConnection($typeConnector, $host, $user, $password, $db, callable $fn = NULL){
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => $db,
            'user' => $user,
            'password' => $password,
            'host' => $host,
            'driver' => $typeConnector,
        );
        if($fn)
            $fn($config);
        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * DatabaseAccessor constructor.
     *
     * @param Connection $connection
     * @internal param Connection $connections
     */
    public function __construct(Connection $connection )
    {

        $this->connections['default'] = $this->current =  $connection;
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        if($this->current->isConnected())
            $this->current->close();
    }

    /**
     * @param $table
     * @param $id
     * @param $field
     * @return array
     */
    public function find($table, $id, $field = Model::STD_KEY_NAME){
        return $this->current->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where("{$field} = :value")
            ->setParameter('value', $id)
            ->execute()->fetchAll();
    }

    /**
     * @param $table
     * @param $rules
     * @param null $values
     * @param null|string $limit
     * @param null|string $offset
     * @return array
     */
    public function findByRules($table, $rules, $values = NULL,  $limit = NULL, $offset = NULL){
        $query = $this->current->createQueryBuilder()
            ->from($table)
            ->select($values ? $values : '*');
        $this->processRules($rules, $query);
        if($limit)
            $query->setMaxResults($limit);
        if($offset)
            $query->setFirstResult($offset);
        return $query->execute()->fetchAll();
    }

    /**
     * Return a record to match base on relation of column
     * Only return target record
     *
     * @param $from
     * @param $target
     * @param array $relation
     * @param array|null $rules
     * @param null|string $limit
     * @param null|string $offset
     * @return array
     */
    public function findByRelation($from, $target, array $relation, array $rules = NULL, $limit = NULL, $offset = NULL){
        $query = $this->current->createQueryBuilder()
            ->select('t.*')
            ->from($from, 'f')
            ->join('f', $target, 't', $this->makeCondition($relation));
        if($rules)
            $this->processRules($rules, $query);
        if($limit)
            $query->setMaxResults($limit);
        if($offset)
            $query->setFirstResult($offset);
        return $query->execute()->fetchAll();
    }

    /**
     * @param $table
     * @param $data
     * @return bool|void
     */
    public function insert($table, $data){
        $query = $this->current->createQueryBuilder()->insert($table);

        foreach ($data as $key => $value){
            $query->setValue($key, ':' . $key)->setParameter($key, $value);
        }
        $query->execute();
    }

    /**
     * @param $table
     * @param array $data
     * @param $id
     * @param string $key
     * @return void
     */
    public function update($table, array $data, $id, $key = Model::STD_KEY_NAME){
            $update = $this->current->createQueryBuilder()->update($table)
                ->where("{$key} = :id")->setParameter('id', $id);
            if(isset($data[$key])){
                unset($data[$key]);
            }
            foreach ($data as $key => $value){
                $update->set($key, ':' . $key)->setParameter($key, $value);
            }
            $update->execute();
    }

    /**
     * @param $table
     * @param $id
     * @param string $field
     * @return void
     */
    public function delete($table, $id, $field = "id"){
            $this->current->createQueryBuilder()->delete($table)
                ->where("{$field} = :id")
                ->setParameter('id', $id)
                ->execute();
    }

    /**
     * @param array $rules
     * @param QueryBuilder $query
     */
    private function processRules(array $rules, QueryBuilder $query)
    {
        foreach ($rules as $rule => $value){
            $index = explode(':',$rule);
            if(!isset($index[1])){
                $index[1] = "equal";
            }
            $mode = ($index[0][0] === "|") ? 'orWhere' : 'andWhere';
            if($mode === 'orWhere'){
                $index[0] = Str::slice($index[0], 1);
            }
            $query->{$mode}($this->resolveExp($index[1],$query,$index[0],$value));
        }
    }

    /**
     * @param $exp
     * @param QueryBuilder $query
     * @param $x
     * @param $y
     * @return string
     */
    private function resolveExp($exp, QueryBuilder $query, $x, $y)
    {
        $result = "";
        switch ($exp){
            case 'equal':
                $result = $query->expr()->eq($x,$y);
                break;

            case 'like':
                $result = $query->expr()->like($x,$y);
                break;

            case 'not-like':
                $result = $query->expr()->notLike($x,$y);
                break;

            case 'not-equal':
                $result = $query->expr()->neq($x,$y);
                break;

            case 'in':
                $result = $query->expr()->in($x,$y);
                break;

            case 'not-in':
                $result = $query->expr()->notIn($x,$y);
                break;

            case 'lower-than':
                $result = $query->expr()->lt($x,$y);
                break;

            case 'greater-than':
                $result = $query->expr()->gt($x,$y);
                break;

            case 'not-null':
                $result = $query->expr()->isNull($x);
                break;

            case 'null':
                $result = $query->expr()->isNotNull($x);
                break;
        }
        return $result;
    }

    /**
     * @param callable $fn
     */
    public function ifHasError(callable $fn){
        if($this->current->errorCode()){
            $fn($this->current->errorInfo());
        }
    }

    /**
     * @param string $name
     * @return ConnectionInterface
     */
    public function getConnections($name = "")
    {
        return ($name !== "") ? $this->connections[$name] : $this->current;
    }

    /**
     * @param string $connection
     */
    public function changeConnection($connection)
    {
        $this->current = $this->connections[$connection];
    }

    /**
     * @param $name
     * @param ConnectionInterface $connection
     */
    public function addConnection($name, ConnectionInterface $connection)
    {
       $this->connections[$name] = $connection;
    }

    /**
     * Execute a query quick on the current connection
     *
     * @param string $query
     * @param array $data
     * @return array
     */
    public function raw($query, array $data = [])
    {
        $statement = $this->current->prepare($query);
        if(count($data) > 0){
            $statement->execute($data);
        }else{
            $statement->execute();
        }
        return $statement->fetchAll();
    }

    /**
     * @param $model
     * @param null $key
     * @return mixed
     * @throws ModelException
     */
    public function model($model, $key = NULL){
        if(!is_subclass_of($model, Model::class)){
            throw new ModelException('This class' . $model . ' is not inherit of Model');
        }
        return new $model($this, $key);
    }

    /**
     * @param string $name
     */
    public function close($name = ""){
        $this->getConnections($name)->close();
    }

    /**
     * @return QueryBuilder
     */
    public function builder()
    {
        return $this->current->createQueryBuilder();
    }

    /**
     * Make statement to declare the condition to a relation
     *
     * @param $relation
     * @return string
     */
    private function makeCondition($relation)
    {
        $result = [];
        foreach ($relation as $key => $value){
            $result[] = "f.$key = t.$value";
        }
        return implode(' AND ', $result);
    }
}