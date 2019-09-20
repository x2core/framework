<?php

namespace X2Core\Preset;


use X2Core\Preset\Exceptions\RuntimeException;


/**
 * Trait ReadOnlyProperties
 * @package X2Core\Preset\Concepts
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
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
        if(!property_exists($this, '_' . $name)){
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