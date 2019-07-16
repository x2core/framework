<?php

namespace Foundation\Database;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use X2Core\Contracts\ActiveRecordInterface;
use X2Core\Exceptions\RuntimeException;
use X2Core\Foundation\Database\DatabaseAccessor;
use X2Core\Foundation\Database\Model;
use X2Core\Util\Str;

class ActiveRecord extends DatabaseAccessor implements ActiveRecordInterface
{
    /**
     * @var string
     */
    private $table;


    /**
     * @var string
     */
    private $keyId = Model::STD_KEY_NAME;

    public function __construct(Connection $connection, $table, $keyId)
    {
        parent::__construct($connection);
        $this->table = $table;
        $this->keyId = $keyId;
    }

    /**
     * @param $data
     * @return boolean
     */
    public function add($data)
    {
        $this->insert($this->table, $data);
        if($this->connection->errorCode()){
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->find($this->table, $id, $id);
    }

    /**
     * @param $id
     * @param $data
     * @return boolean
     */
    public function save($id, $data)
    {
        $this->update($this->table, $data, $id, $this->keyId);
        if($this->connection->errorCode()){
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @return boolean
     */
    public function remove($id)
    {
        $this->delete($this->table, $id, $this->keyId);
        if($this->connection->errorCode()){
            return false;
        }
        return true;
    }

//    public function __call($name, $arguments)
//    {
//        $cmd = Str::camelCaseParse($name);
//       switch ($cmd[0]){
//           case 'get':
//               return $this->getJoin(array_slice($cmd, 1), $arguments);
//               break;
//           case 'belong':
//               return $this->getBelong(array_slice($cmd, 1), $arguments);
//               break;
//           default:
//               throw new RuntimeException('the magic method not match anything');
//               break;
//       }
//    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @param string $keyId
     */
    public function setKeyId($keyId)
    {
        $this->keyId = $keyId;
    }

//    /**
//     * @param $elms
//     * @param $opt
//     * @return QueryBuilder|mixed[]
//     */
//    private function getJoin($elms, $opt)
//    {
//        $tableJoin = implode('_', $elms);
//        $keyJoin = isset($opt['keyJoin']) ? $opt['keyJoin'] : $this->table .'_'. $this->keyId;
//        $query = $this->connection->createQueryBuilder()
//            ->from($tableJoin)
//            ->where( 't', "t.{$keyJoin} = f.{$this->keyId}")
//            ->select('*');
//            if($opt['fetch_query']){
//                return $query;
//            }
//        return $query->execute()->fetchAll();
//    }

}