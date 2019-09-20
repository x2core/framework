<?php

namespace X2Core\Database;

use Doctrine\Common\Inflector\Inflector;
use ErrorException;
use X2Core\Validator\ArrayValidator;
use X2Core\Validator\Validator;
use X2Core\Util\Str;

/**
 * Class Model
 * @package X2Core\Database
 *
 * @@author Oliver Valiente <oliver021val@gmail.com>
 * @abstract
 *
 * This class implement a simple orm to map record on database
 * This class should be inherited to take a name and configure a map
 */
abstract class Model implements \Serializable
{
    /**
     * std field name to primary key
     */
    const STD_KEY_NAME = 'id';

    /**
     * constants to info the available relationship
     */
    const oneToOne = 1;
    const oneToMany = 2;
    const BelongTo = 3;
    const ManyToMany = 4;

    /**
     * @var bool
     */
    private static $plural;

    /**
     * @var null|string
     */
    private $table;

    /**
     * @var string
     */
    private $key = self::STD_KEY_NAME;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var ArrayValidator
     */
    private $validator;

    /**
     * @var DatabaseAccessor
     */
    private $db;

    /**
     * @var string[]|mixed[]
     */
    private $map;

    /**
     * @var Validator
     */
    private $details;

    /**
     * Model constructor.
     *
     * @param DatabaseAccessor $db
     * @param null $key
     * @throws ModelException
     */
    public function __construct( DatabaseAccessor $db, $key = NULL)
    {
        $this->db = $db;
        $this->table = self::tableName(static::class);
        $this->validator = new ArrayValidator();
        $this->buildValidator($this->validator);
        $map= $this->buildMap();
        if ($map){
            $this->map = $map;
        }elseif ($map instanceof Mapper){
            $this->map = $map->get();
        }else{
            throw new ModelException('Cannot possible build this model because the map is invalid');
        }
        $this->map['id'] = $key ?? Model::STD_KEY_NAME;
    }

    /**
     * This method is to create a validator to save in database
     *
     * @abstract
     * @param ArrayValidator $desc
     * @return void
     */
    abstract protected function buildValidator(ArrayValidator $desc);

    /**
     * This method should to return an array with mapper strategy
     *
     * @abstract
     * @return string[]
     */
    abstract protected function buildMap();

    /**
     * @return bool
     */
    public static function isPlural()
    {
        return self::$plural;
    }

    /**
     * return void
     */
    public static function enablePlural()
    {
        self::$plural = true;
    }

    /**
     * return void
     */
    public static function disablePlural()
    {
        self::$plural = false;
    }

    /**
     * Helper to resolve a std name for tables
     *
     * @param $class
     * @return string
     */
    public static function tableName($class)
    {
        $table = Inflector::tableize(Str::lastSubString("\\", $class));
        if(self::$plural){
            $table = Inflector::pluralize($table);
        }
        return $table;
    }

    /**
     * Shortcut to create instance of Mapper
     *
     * @return Mapper
     */
    public static function mapper(){
        return new Mapper;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if(method_exists($this, $method = 'get' . Inflector::classify($name)))
            return $this->{$method}($this->data[$name]);
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return  $this->data;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        if(method_exists($this, $method = 'set' . Inflector::classify($name)))
            $this->data[$name] = $this->{$method}($value);
        else
            $this->data[$name] = $value;
    }

    /**
     * @param $name
     */
    public function reset($name)
    {
        $this->data[$name] = NULL;
    }

    /**
     * @return void
     */
    public function resetAll()
    {
        $this->data[] = [];
    }

    /**
     * @param $name
     * @return mixed|object
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return mixed|object
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Return true if the model has a id and then is bind with database
     *
     * @return bool
     */
    public function isRegistered(){
        return isset($this->data['id']);
    }

    /**
     * Return true if the model not has been saved
     *
     * @return bool
     */
    public function isNewRecord(){
        return !isset($this->data['id']);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Commit data to send to database
     *
     * return bool
     */
    public function save()
    {
        $newPackage = [];
        foreach ($this->data as $key => $value){
            if(!isset($this->map[$key]))
                continue;
            $keyMapper = $this->map[$key];
            if(is_subclass_of($value, self::class)){
                $value->save();
                continue;
            }elseif (is_array($value)){
                foreach ($this->data as $model){
                    if(is_subclass_of($value, self::class)){
                        $model->save();
                    }
                }
            }
            $newPackage[$keyMapper] = $value;
        }
        if(isset($this->data['id']) && $this->data['id'])
            $this->db->update($this->table, $newPackage, $this->data['id'], $this->key );
        else
            $this->db->insert($this->table, $newPackage);
    }

    /**
     * Load a register of the database in the model for std primary key or custom field
     *
     * @param $id
     * @param null $custom_key
     * @return $this
     */
    public function find($id, $custom_key = NULL)
    {
        $data = $this->db->find($this->table, $id, $custom_key ?? $this->key);
        if(count($data) < 1){
            return $this;
        }
        foreach ($data[0] as $key => $value){
            if(!in_array($key, $this->map))
                continue;
            $key = array_search($key, $this->map);
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Load the model with external data bundle of array
     * This method return true if the data is valid
     *
     * @param array $data
     * @param bool $omittedValidator
     * @return bool
     */
    public function hydrate(array $data, $omittedValidator = false)
    {
        if(!$omittedValidator){
            $result = $this->validator->validate($data);
            $this->details = $result;
            if($result->notValid()){
                return false;
            }
        }
        foreach ($data as $key => $value){
            if(!in_array($key, $this->map))
                continue;
            $key = array_search($key, $this->map);
            $this->set($key, $value);
        }
        return true;
    }


    /**
     * This method load data through the relations of the model
     *
     * @param $modelName
     * @return $this
     * @throws ModelException
     */
    public function include($modelName)
    {
        if(isset($this->map[$modelName])){
            throw new ModelException('The model to include not exists');
        }

        if(is_array($this->map[$modelName])){
            throw new ModelException('The model configuration is should be an array');
        }

        $model = $this->map[$modelName];

        if($model['relation'] === self::oneToOne){
            $this->data[$modelName] = $this->oneToOne($model);
        }

        if($model['relation'] === self::oneToMany){
            $this->data[$modelName] = $this->oneToMany($model);
        }

        if($model['relation'] === self::BelongTo){
            $this->data[$modelName] = $this->belongTo($model);
        }

        return $this;
    }

    /**
     * This method is helper to resolve the relationship query
     *
     * @param array|string $model
     * @param null $custom_key
     * @return Model
     * @throws ModelException
     */
    public function oneToOne( $model, $custom_key = NULL){
        if(is_string($model)){
            $model = ['class' => $model];
        }elseif (!is_array($model)){
            throw new ModelException('The argument to fetch a relationship of model is not valid');
        }
        return (new $model['class']($this->db, $model['field'] ?? $this->key))
            ->find($this->data[$this->key], $custom_key ?? $this->resolveBindName());
    }

    /**
     * {@inheritdoc}
     */
    public function belongTo( $model, $custom_key = NULL, $fieldMatch = NULL){
        try {
            if(is_string($model)){
                $model = ['class' => $model];
            }elseif (!is_array($model)){
                throw new ModelException('The argument to fetch a relationship of model is not valid');
            }
            return (new $model['class']($this->db, $model['field'] ?? $this->key))
                ->find($this->data[ self::tableName( $fieldMatch ?? self::class) . '_' . $this->key], $custom_key);
        }catch (ErrorException $error){
            throw new ModelException('This model '. self::class . ' not contains with field to bind with '. $model['class']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function oneToMany( $model, $custom_key = NULL){
        if(is_string($model)){
            $model = ['class' => $model];
        }elseif (!is_array($model)){
            throw new ModelException('The argument to fetch a relationship of model is not valid');
        }
        /* @var Model $modelObject*/
        $data = $this->db->findByRules(self::tableName($model['class']), [
            $custom_key ?? $this->resolveBindName() . ':equal' => $this->data[$this->key]
        ]);

        $result = [];

        foreach ($data as $item){
            $modelObject = new $model['class']($this->db, $model['field'] ?? $this->key);
            $modelObject->hydrate($item, true);
            $result[] = $modelObject;
        }

        return $result;
    }

    /**
     * Delete current register that is bind to this model
     */
    public function delete()
    {
        $this->db->delete($this->table, $this->data['id'], $this->key);
    }

    /**
     * @return null|string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param null|string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Retrieves the field to bind model
     *
     * @return null|string
     */
    private function resolveBindName()
    {
        $table = (self::$plural) ? Inflector::singularize($this->table) : $this->table;
        return $table . '_' . $this->key;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize(){
        return serialize([
            'data' => $this->data,
            'table' => $this->table,
            'key' => $this->key,
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized){
        $data = unserialize($serialized);
        $this->key = $data['key'];
        $this->data = $data['data'];
        $this->table = $data['table'];
    }
}