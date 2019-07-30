<?php

namespace X2Core\Util;

/**
 * Class Structure
 * @package X2Core\Util
 *
 * @desc A class to execute functions per structure and other utilities
 */
class Structure
{
    /**
     * @var callable[]|string[]
     */
    private $signatures;

    /**
     * @var mixed
     */
    private $schema;

    /**
     * Structure constructor.
     * @param array $signatures
     */
    public function __construct(array $signatures)
    {
        $this->signatures = $signatures;
    }

    /**
     * @param array $structure
     */
    public function exec(array $structure){
       foreach ($this->signatures as $signature => $handle){
           $current =& $structure;
           $keys = explode('.', $signature);
           $length = count($keys);
           $found = true;
           for($i = 0; $i < $length; $i++){
               if(isset($current[$keys[$i]])){
                   $current =& $current[$keys[$i]];
               }else{
                   $found = false;
                   break;
               }
           }
           if($found){
               $handle($current);
           }
       }
    }

//    /**
//     * @param array $structure
//     * @param bool $strict
//     *
//     * @return bool
//     */
//    public function validateSchema(array $structure, $strict){
//        do{
//            $current = current($structure);
//            $key = key($structure);
//        }while(next($structure));
//        return true;
//    }

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param mixed $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return callable[]|string[]
     */
    public function getSignatures()
    {
        return $this->signatures;
    }

    /**
     * @param $signature
     * @param $handle
     * @internal param callable[]|string[] $signatures
     */
    public function setSignature($signature, $handle)
    {
        $this->signatures[$signature] = $handle;
    }
}