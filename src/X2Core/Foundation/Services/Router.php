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
        if(!isset($this->record[$method]))
            $this->record[$method] = [];
        $this->record[$method][] = [
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
     * @param string|array $method
     * @param string $url
     * @return \Generator
     * @desc this method is a generator to iterate for matches routes
     */
    public function fetch($method, $url){
        $result = [];
        foreach (is_array($method) ? $this->mergeMethods($method) : $this->record[$method] as $item){
            if($data = URL::match($item['type'], $item['route'], $url)){
                $result['handle'] = $item['handle'];
                $result['parameter'] = $item['type'] !== URL::MATCH_STATIC ? $data : NULL;
                $result['method'] = $item['type'];
                $result['url'] = $url;
                yield $result;
            }
        }
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

    /**
     * @param $method
     * @return array
     */
    private function mergeMethods($method)
    {
        $result = [];
        foreach ($method as $item){
            if(!isset($this->record[$item])){
                continue;
            }
           $result = array_merge($result,$this->record[$item]);
        }
        return $result;
    }
}