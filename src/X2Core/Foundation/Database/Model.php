<?php

namespace X2Core\Foundation\Database;

use X2Core\Foundation\Validator\Descriptor;
use X2Core\Util\Str;

/**
 * Class Model
 * @package X2Core\Foundation\Database
 */
abstract class Model
{
    const STD_KEY_NAME = 'id';
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
     * @var Descriptor
     */
    private $validator;

    /**
     * @var MapData
     */
    private $map;

    /**
     * Model constructor.
     * @param null $table
     */
    public function __construct($table = NULL)
    {
        $this->table = ($table) ? $table : Str::toDashCase($table);
        $this->buildValidator($this->validator);
    }


    /**
     * @param Descriptor $desc
     * @return void
     */
    abstract public function buildValidator(Descriptor $desc);

    /**
     * @param MapData $desc
     * @return void
     */
    abstract public function buildMap(MapData $desc);

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
     * @return mixed|object
     */
    public function &__get($name)
    {
        return ($this->map->isBuildable($name)) ? $this->map->build($this->data[$name]) : $this->data[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}