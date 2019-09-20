<?php

namespace X2Core\Database;

/**
 * Class Mapper
 * @package X2Core\Database
 */
class Mapper
{
    /**
     * @var string[]
     */
    private $map;

    /**
     * @param $fields
     * @return $this
     */
    public function autoMap($fields){
        foreach ((array) $fields as $field){
            $this->map[$field] = $field;
        }
        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setField($key, $value){
        $this->map[$key] = $value;
    }

    /**
     * Set a relation to map
     *
     * @param $key
     * @param $model
     * @param $field
     */
    public function oneToOne($key, $model, $field){
        $this->map[$key] = [
            'class' => $model,
            'field' => $field,
            'relation' => __METHOD__
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function belongTo($key, $model, $field){
        $this->map[$key] = [
            'class' => $model,
            'field' => $field,
            'relation' => __METHOD__
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function oneToMany($key, $model, $field){
        $this->map[$key] = [
            'class' => $model,
            'field' => $field,
            'relation' => __METHOD__
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function manyToMany($key, $model, $field){
        $this->map[$key] = [
            'class' => $model,
            'field' => $field,
            'relation' => __METHOD__
        ];
    }

    /**
     * Return all map configure
     *
     * @return string[]
     */
    public function get(){
        return $this->map;
    }

}