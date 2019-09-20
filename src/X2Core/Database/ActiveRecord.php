<?php

namespace X2Core\Database;


use Doctrine\DBAL\Connection;
use X2Core\Preset\Contracts\DatabaseManager;
use X2Core\Validator\ArrayValidator;
use X2Core\Validator\Validator;

/**
 * Class ActiveRecord
 * @package Foundation\Database
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class ActiveRecord extends DatabaseAccessor
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $keyId = DatabaseManager::STD_KEY_NAME;

    /**
     * @var ArrayValidator
     */
    protected $validator = NULL;

    /**
     * ActiveRecord constructor.
     *
     * @param Connection $connection
     * @param $table
     * @param $keyId
     */
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
        if($this->getConnections()->errorCode()){
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
        if($this->getConnections()->errorCode()){
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
        if($this->getConnections()->errorCode()){
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

    /**
     * @param array $data
     * @return bool|Validator
     */
    public function validate(array $data)
    {
        if($this->validator !== NULL){
            return $this->validator->validate($data);
        }
        return true;
    }

    /**
     * @param ArrayValidator $validator
     */
    public function setValidator(ArrayValidator $validator)
    {
        $this->validator = $validator;
    }
}