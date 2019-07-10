<?php

namespace Foundation\Database;


use Eyrene\Database\Connector\DatabaseAccessor;
use X2Core\Contracts\ActiveRecordInterface;
use X2Core\Foundation\Database\Model;

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
}