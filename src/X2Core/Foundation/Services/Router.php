<?php

namespace X2Core\Foundation\Services;


use X2Core\Util\URL;

/**
 * Class Router
 * @package X2Core\Foundation\Services
 */
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
     * @param string $method
     * @param string $url
     * @param callable $process
     *
     * @desc exec callable process for every route that match with request params
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
     * @param string $method
     * @param string $url
     *
     * @desc retrieve all matches routes
     * @return bool|array
     */
    public function getMatches($method, $url){
        $result = [];
        $i = 0;
        foreach ($this->record[$method] as $item){
            if($data = URL::match($item[0], $item[1], $url)){
                $result[$i] = [];
                $result[$i]['handle'] = $item[2];
                $result[$i]['parameter'] = $item[0] !== URL::MATCH_STATIC ? $data : NULL;
                $i++;
            }
        }
        return $i > 0 ? $result : false;
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @desc this method is a generator to iterate for matches routes
     * @return bool|array
     */
    public function fetch($method, $url){
        $result = [];
        foreach ($this->record[$method] as $item){
            if($data = URL::match($item[0], $item[1], $url)){
                $result['handle'] = $item[2];
                $result['parameter'] = $item[0] !== URL::MATCH_STATIC ? $data : NULL;
                yield $result;
            }
        }
        yield NULL;
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