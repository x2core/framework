<?php

namespace X2Core\Validator;

use Closure;
use X2Core\Util\Arr;
use X2Core\Exceptions\InvalidRuleTypeException;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $values;
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     * @param Descriptor[] $rules
     * @param array $requires
     * @return Validator
     * @throws InvalidRuleTypeException
     */
    public static function doValidate($data, array $rules, array $requires){
        $result = new self($data);

        $lefts = Arr::contains(array_keys($data), $requires, true);
        if(count($lefts) === 0){
            $result->evaluate($rules);
        }else{
            foreach ($lefts as $left){
                $result->appendError($left, ['required'] );
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @param $rules
     * @param Validator|null $validator
     * @return array
     * @internal param $getRules
     */
    public static function validateData($value, $rules, Validator $validator = null)
    {
        $result = [];

        foreach($rules as $rule){
            if($rule instanceof AbstractRule){
                $result[] = $rule->onValidate($value) ? true : get_class($rule);
            }

            if($rule instanceof Closure){
                $result[] = $rule($value, $validator);
            }
        }

        return $result;
    }

    /**
     * Validator constructor.
     * @param $data
     */
    public function __construct( $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $checks
     * @return array
     */
    private static function countError(array $checks)
    {
        $errors = [];

        foreach ($checks as $check){
            if($check !== true){
               $errors[] = $check;
            }
        }

        return $errors;
    }

    /**
     * @return bool
     */
    public function isValid(){
        return count($this->errors) === 0;
    }

    /**
     * @return bool
     */
    public function notValid(){
        return count($this->errors) > 0;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasErrorIn($key){
        return isset($this->errors[$key]);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function fetchErrors($key){
        return $this->errors[$key] ?? NULL;
    }

    /**
     * @param $key
     * @param array $errors
     */
    public function appendError($key, array $errors){
        $this->errors[$key] = $errors;
    }

    /**
     * @param $name
     */
    public function getValue($name)
    {
        $this->values[$name];
    }

    /**
     * @param $rules
     * @throws InvalidRuleTypeException
     */
    public function evaluate($rules)
    {
        foreach ($this->data as $key => $value){
            if(isset($rules[$key])){
                $ruleGroup = $rules[$key];
                if( $ruleGroup instanceof Descriptor ){
                    $checks = self::validateData($value, $ruleGroup->getRules(), $this);
                    if( count($errors = self::countError($checks) ) > 0){
                        $this->appendError($key, $errors);
                    }
                }else{
                    throw new InvalidRuleTypeException($key);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}