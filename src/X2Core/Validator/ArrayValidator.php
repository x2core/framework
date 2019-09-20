<?php

namespace X2Core\Validator;

/**
 * Class ArrayValidator
 * @package X2Core\Validator
 */
class ArrayValidator
{
    /**
     * @var Descriptor[]
     */
    private $rules = [];

    /**
     * @var array
     */
    private $requireElms = [];

    /**
     * // TODO
     *
     * @param $key
     * @return Descriptor
     */
    public function to($key){
        if(!isset($this->rules[$key])){
            $this->rules[$key] = new Descriptor;
        }

        return $this->rules[$key];
    }

    /**
     * @return void
     */
    public function reset(){
        $this->rules = [];
    }

    /**
     * @param $elms
     * @return $this
     */
    public function require($elms){
        if(is_array($elms)){
            $this->requireElms = $elms;
        }

        if(is_string($elms)){
            $this->requireElms[] = $elms;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param Descriptor|Descriptor[] $rules
     */
    public function setRules($key, Descriptor $rules)
    {
        $this->rules[$key] = $rules;
    }

    /**
     * @return Validator
     */
    public function validate($data){
        return Validator::doValidate($data,
            $this->rules,
            $this->requireElms);
    }

}