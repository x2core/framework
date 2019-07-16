<?php

namespace X2Core\Foundation\Database;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use X2Core\Util\Str;

/**
 * Class DatabaseAccessor
 * @package Eyrene\Database\Connector
 */
class DatabaseAccessor
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * DatabaseAccessor constructor.
     * @param Connection $connection
     * @internal param Connection $connections
     */
    public function __construct(Connection $connection )
    {

        $this->connection = $connection;
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        if($this->connection->isConnected())
            $this->connection->close();
    }

    /**
     * @param $table
     * @param $id
     * @param $field
     * @return array
     */
    public function find($table, $id, $field = Model::STD_KEY_NAME){
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where("{$field} = :value")
            ->setParameter('value', $id)
            ->execute()->fetchAll();
    }

    /**
     * @param $table
     * @param $rules
     * @return array
     */
    public function findByRules($table, $rules, $values = NULL){
        $query = $this->connection->createQueryBuilder()
            ->from($table)
            ->select($values ? $values : '*');
        $this->processRules($rules, $query);
        return $query->execute()->fetchAll();
    }

    /**
     * @param $table
     * @param $data
     */
    public function insert($table, $data){
           $this->connection->createQueryBuilder()->insert($table)
               ->values($data)
               ->execute();
    }

    /**
     * @param $table
     * @param $data
     * @return void
     */
    public function update($table, array $data, $id, $key = Model::STD_KEY_NAME){
            $update = $this->connection->createQueryBuilder()->update($table)
                ->where("{$key} = :id")->setParameter('id', $id);
            foreach ($data as $key => $value){
                $update->set($key, $value);
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
            $this->connection->createQueryBuilder()->delete($table)
                ->where("{$field} = :id")
                ->setParameter('id', $id)
                ->execute();
    }

    /**
     * @param QueryBuilder $query
     * @param $table
     * @param string $from
     * @param string|null $to
     */
//    private function join(QueryBuilder $query, $table, $from = 'id', $to = null){
//        $query->innerJoin('', $table, '', $query->expr()->eq($from, $to));
//    }

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
    public function ifHasError($fn){
        if($this->connection->errorCode()){
            $fn($this->connection->errorInfo());
        }
    }
}