<?php

namespace Eyrene\Database\Modeling;


use Eyrene\Database\Modeling\Relations\HasMany;
use Eyrene\Database\Modeling\Relations\HasOne;
use Eyrene\Database\Modeling\Relations\ManyToMnay;
use Eyrene\Validator\ArrayValidator;

abstract class Model
{
    const HAS_ONE = '1';
    const HAS_MANY = '2';
    const MANY_TO_MANY = '3';
    const PRIMARY_KEY = '4';
    const UNIQUE_INDEX = '5';
    const INDEX = '6';
    const STRING = '7';
    const INTEGER = '8';
    const FLOAT = '9';
    const TEXT = '10';
    const ENUM = '11';
    const DATE = '12';

    /**
     * @var string
     */
    private $key;

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var array
     */
    protected $models = [];

    /**
     * @var boolean
     */
    private $sofDelete = false;

    /**
     * @var array
     */
    protected $translatable = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    private $indexes = [];

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $field
     */
    public function enableSofDelete($field = null)
    {
        $this->sofDelete = $field ?? true;
    }

    public function setRelationShip($class, $type, $bind = null){
        $relation = null;

        switch($type){
            case self::HAS_ONE:
                $relation[$class] = new HasOne(static::class, $class, $bind);
                break;

            case self::HAS_MANY:
                $relation[$class] = new HasMany(static::class, $class, $bind);
                break;

            case self::MANY_TO_MANY:
                $relation[$class] = new ManyToMnay(static::class, $class, $bind);
                break;
        }

        $this->models[$class] = $relation;
    }

    public function hasString($name, $length = 255){
        $this->createField('',$name, $length);
        return $this;
    }

    public function hasText($name, $length = 255){
        $this->createField('',$name, $length);
        return $this;
    }

    public function hasInteger($name, $length = 255){
        $this->createField('',$name, $length);
        return $this;
    }

    public function hasEnum($name, $length = 255){
        $this->createField('',$name, $length);
        return $this;
    }

    public function hasDate($name, $length = 255){
        $this->createField('',$name, $length);
        return $this;
    }

    public function hasKey($name, $length = 255){
        $this->createField('',$name, $length);
        $this->createIndex($name, self::PRIMARY_KEY );
        return $this;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return array(
            'translatable' => $this->translatable,
            'indexes' => $this->translatable,
            'fields' => $this->translatable,
            'soft-delete' => $this->sofDelete
        );
    }

    /**
     * @param Descriptor $descriptor
     * @return mixed
     */
    abstract protected function onValidate(ArrayValidator $validator);

    /**
     * @return void
     */
    abstract protected function onBuildMetadata();

    /**
     * @param $type
     * @param $name
     * @param $length
     * @param bool $nullable
     * @param bool $unique
     * @param bool $index
     */
    private function createField($type, $name, $length, $nullable = true, $unique = false, $index = false){
        $this->fields[$name] = [$type, $length, $nullable, $unique, $index];
    }

    /**
     * @param $name
     * @param $type
     */
    private function createIndex($name, $type)
    {
        $this->indexes[$name] = $type;
    }

    protected function required($fields){
      foreach ((array) $fields as $field){
          $this->fields[$field][2] = false;
      }
    }

    protected function uniques($fields){
        foreach ((array) $fields as $field){
            $this->fields[$field][3] = true;
        }
    }

    protected function indexes($fields){
        foreach ((array) $fields as $field){
            $this->fields[$field][4] = true;
        }
    }
}