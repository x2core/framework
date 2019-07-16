<?php

namespace X2Core\Foundation\Database;


/**
 * Class DataDescriptor
 * @package X2Core\Foundation\Database
 */
class DataDescriptor
{
    /**
     * @var array
     */
    private $desc = [];

    /**
     * @var bool
     */
    private $inclusive;

    /**
     * @return $this
     */
    public function isEqual($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'equal'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function isNotEqual($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'not-equal'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function isLower($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'lower-than'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function isGreater($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'greater-than'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function possibleValuesTo($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'in'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function matchTo($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'like'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function noMatch($field, $value){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'not-equal'] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function isNotNull($field){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'not-null'] = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function isNull($field){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'null'] = true;
        return $this;
    }

    /**
     * @desc add closure or to description
     * @return $this
     */
    public function or(){
        $this->inclusive = true;
        return $this;
    }

    /**
     * @return string
     */
    private function haveInclusiveClsr()
    {
        if($this->inclusive){
            $this->inclusive = false;
            return '|';
        }
        return '';
    }

    /**
     * @return array
     */
    public function getDescriptor()
    {
        return $this->desc;
    }
}