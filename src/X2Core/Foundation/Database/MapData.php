<?php

namespace X2Core\Foundation\Database;

/**
 * Class MapData
 * @package X2Core\Foundation\Database
 */
class MapData
{
    /**
     * @var string[]
     */
    private $buildable;

    /**
     * @var string[]
     */
    private $links;

    /**
     * @var mixed[]
     */
    private $policies;

    /**
     * @param $key
     * @return boolean|string
     */
    public function isBuildable($key){
        return isset($this->buildable[$key]) ? $this->buildable[$key] : false;
    }

    /**
     * @param $key
     * @return boolean|string
     */
    public function hasPolicy($key){
        return isset($this->policies[$key]) ? $this->policies[$key] : false;
    }

    /**
     * @param $key
     * @return string
     */
    public function getLink($key){
        return $this->links[$key];
    }

    /**
     * @param $key
     * @param $dest
     */
    public function setLink($key, $dest){
        $this->links[$key] = $dest;
    }

    /**
     * @param $key
     * @param $dest
     */
    public function setBuildable($key, $dest){
        $this->buildable[$key] = $dest;
    }

    /**
     * @param $key
     * @param $dest
     */
    public function setPolicy($key, $dest){
        $this->policies[$key] = $dest;
    }

    /**
     * @param $name
     * @return object
     */
    public function build($name)
    {
        //TODO make build to construct object to data
    }
}