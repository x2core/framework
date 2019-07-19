<?php

namespace X2Core\Foundation\Services;


use X2Core\Util\URL;

class Router
{
    /**
     * @var mixed
     */
    private $record;

    /**
     * @var int
     */
    private $limitHandles;

    /**
     * @param $method
     * @param $matchType
     * @param $matchRoute
     * @param $handle
     */
    public function pushRoute($method, $matchType, $matchRoute, $handle){
        $this->record[$method] = [
            'type' => $matchType,
            'route' => $matchRoute,
            'handle' => $handle,
        ];
    }

    /**
     * @param $method
     * @param $url
     * @param $process
     * @return bool
     */
    public function match($method, $url, $process){
        $result = [];
        foreach ($this->record[$method] as $item){
            if($data = URL::match($item[0], $item[1], $url)){
                $result['handle'] = $item[2];
                $result['parameter'] = $item[0] !== URL::MATCH_STATIC ? $data : NULL;
                $process(...$result);
            }
        }
        return count($result) > 0;
    }

    /**
     * @return mixed
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param mixed $record
     */
    public function setRecord($record)
    {
        $this->record = $record;
    }

    /**
     * @return mixed
     */
    public function getLimitHandles()
    {
        return $this->limitHandles;
    }

    /**
     * @param mixed $limitHandles
     */
    public function setLimitHandles($limitHandles)
    {
        $this->limitHandles = $limitHandles;
    }
}