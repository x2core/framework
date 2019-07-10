<?php

namespace X2Core;

use function explode;

/**
 * Trait ConfigSupport
 * @package X2Core\Container
 */
trait ConfigSupport
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
    public function &config($source, $payload = null){
        $ref =& $this->resolveConfigSource($source);
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
    public function &getConfigSafe($source, $fallback = null){
        $ref =& $this->config($source);
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
    private function &resolveConfigSource($source){
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