<?php

namespace X2Core\Types;


use X2Core\Exceptions\RuntimeException;


/**
 * Trait ReadOnlyProperties
 * @package X2Core\Types
 */
trait ReadOnlyProperties
{
    /**
     * @var array
     */
    private $propertiesFinals = [];

    /**
     * @param $name
     * @return mixed
     * @throws RuntimeException
     */
    public function __get($name)
    {
        if(!property_exists($this, $name)){
            throw new RuntimeException('A class that use ReadOnlyProperties trait not accepts dynamic fields');
        }
        return $this->{'_' . $name};
    }

    /**
     * @param $name
     * @param $value
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        if(in_array($name, $this->propertiesFinals))
            throw new RuntimeException("The property {$name} is readonly");
        throw new RuntimeException('A class that use ReadOnlyProperties trait not accepts dynamic fields');
    }

    /**
     * @param $name
     *
     * @return void
     */
    public function readOnly($name){
        $this->propertiesFinals[] = $name;
    }

    /**
     * @param $name
     *
     * @return true;
     */
    public function isReadOnly($name){
        return in_array($name, $this->propertiesFinals);
    }


}