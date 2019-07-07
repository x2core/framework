<?php

namespace X2Core\Foundation\Validator;


class ArrayValidator
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Descriptor[]
     */
    private $rules;

    /**
     * @var array
     */
    private $requireElms = [];

    /**
     * @var
     */
    private $container;

    /**
     * Validator constructor.
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->data = $data;
    }

    /**
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
     * @param $key
     * @return void
     */
    public function reset(){
        $this->rules = [];
    }

    /**
     * @param $elms
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
     * @param Descriptor[] $rules
     */
    public function setRules($key, Descriptor $rules)
    {
        $this->rules[$key] = $rules;
    }

    /**
     * @return Validator
     */
    public function validate(){
        return Validator::doValidate($this->data,
            $this->rules,
            $this->requireElms,
            $this->container);
    }

}