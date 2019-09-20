<?php

namespace X2Core\Validator;


class Descriptor
{
    protected $rules = [];

    /**
     * @return AbstractRule[]
     */
    public function getRules(){
        return $this->rules;
    }

    /**
     * @param $int
     * @return $this
     */
    public function lengthMin($int){
        $this->rules[] = new Rules\Length(['min' => $int]);
        return $this;
    }

    /**
     * @param $int
     * @return $this
     */
    public function lengthMax($int){
        $this->rules[] = new Rules\Length(['max' => $int]);
        return $this;
    }

    /**
     * @param $config
     * @return $this
     */
    public function length($config){
        $this->rules[] = new Rules\Length($config);
        return $this;
    }

    /**
     * @return $this
     */
    public function mustEmail(){
        $this->rules[] = new Rules\Email();
        return $this;
    }

    /**
     * @return $this
     */
    public function mustIp(){
        $this->rules[] = new Rules\Ip();
        return $this;
    }

    /**
     * @param $match
     * @return $this
     */
    public function matchWith($match){
        $this->rules[] = new Rules\RegExp($match);
        return $this;
    }

    /**
     * @return $this
     */
    public function isNotBlank(){
        $this->rules[] = new Rules\NotBlank();
        return $this;
    }

    /**
     * @return $this
     */
    public function isAlphaNum(){
        $this->rules[] = new Rules\AlphaNum();
        return $this;
    }

    /**
     * @return $this
     */
    public function isNumber($type = null){
        $this->rules[] = new Rules\Number($type);
        return $this;
    }

    /**
     * @return $this
     */
    public function enum( array $values){
        $this->rules[] = new Rules\Enum($values);
        return $this;
    }

    /**
     * @param $rule
     * @return $this
     */
    public function customRule($rule){
        $this->rules[] = $rule;
        return $this;
    }

    public function byCallback(callable  $rule){
        $rule();
    }

}