<?php

namespace X2Core\Database;
use X2Core\Util\Str;


/**
 * Class DataDescriptor
 * @package X2Core\Database
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
    public function possibleValuesTo($field, $values){
        $this->desc[$field . ':' . $this->haveInclusiveClsr() .'in'] = is_array($values) ? implode(',', $values) : $values;
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

    /**
     * Support to dynamic description base on camelcase notation
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
       $commands = Str::camelCaseParse($name);
       $len = count($commands);
       if(($double = $len !== 4) || $len !== 2){
           throw new \BadMethodCallException('The magic method is not match to support dynamic commands');
       }elseif ($double && $commands[2] !== 'And' || $commands[2] !== 'Or'){
           throw new \BadMethodCallException('The magic method is not support the commands: '. $commands[2]);
       }
       $arguments = (array) $arguments;

        $key = $commands[0];
        $rule1 = $commands[0];
        $this->{'is' . ucfirst($rule1)}($key, $arguments[0]);

        if($double){
            $rule2 = $commands[3];
            if($commands[2] === 'Or')
                $this->inclusive = true;
        }
    }
}