<?php

namespace X2Core;

use function explode;

/**
 * Trait ConfigSupport
 * @package X2Core\Preset\Concepts
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * Provide support to quick manager config
 */
trait DotSupport
{
    /**
     * @var array
     */
    private $configStorage = [];

    /**
     * @param $source
     * @param null $payload
     * @return $this|mixed
     */
    public function &dot($source, $payload = null){
        $ref =& $this->dotResolvePath($source);
        if($payload !== null){
            $ref = $payload;
            return $this;
        }else{
            return $ref;
        }
    }

    /**
     * @param $source
     * @param null $fallback
     * @return $this|mixed|null
     */
    public function &dotSafeValue($source, $fallback = null){
        $ref =& $this->dot($source);
        if($ref === null){
            return $fallback;
        }else{
            return $ref;
        }
    }

    /**
     * @param $source
     * @return array
     */
    private function &dotResolvePath($source){
        $chunks = explode('.',$source);
        $handle =& $this->configStorage;
        foreach ($chunks as $section){
            if(!isset($handle[$section])){
                $this->configStorage[$section] = [];
            }
            $handle =& $handle[$section];
        }
        return $handle;
    }
}