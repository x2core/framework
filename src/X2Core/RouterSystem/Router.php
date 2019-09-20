<?php

namespace X2Core\RouterSystem;


use X2Core\Preset\Contracts\Router as RouterInterface;
use X2Core\Util\Str;
use X2Core\Util\URL;

/**
 * Class Router
 * @package App\Services
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class Router implements \Serializable, RouterInterface
{
    /**
     * @var mixed
     */
    private $record;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $namespace = "";

    /**
     * @var string[]
     */
    private $recordName;

    /**
     * @var string[][]
     */
    private $tags;
    /**
     * @var bool
     */
    private $cache;

    /**
     * Router constructor.
     * @param bool $cache
     */
    public function __construct($cache = false)
    {
        $this->cache = $cache;
    }

    public function __destruct()
    {

    }

    /**
     * @param $method
     * @param $matchRoute
     * @param $handle
     * @param array $options
     * @return Route
     */
    public function addRoute($method, $matchRoute, $handle, array $options = []){
        if(!isset($this->record[$method]))
            $this->record[$method] = [];
        if($handle instanceof \Closure)
            $handle = (new SerializableClosure($handle));
        if(is_string($handle) && strstr($handle, '@'))
            $handle = $this->namespace . $handle;
        $offset = array_push($this->record[$method],[
            'type' => (strstr($matchRoute,'$') ?
                URL::MATCH_ARRAY_PARAM : URL::MATCH_STATIC),
            'route' => $matchRoute,
            'handle' => $handle,
            'options' => $options
        ]);
        return new Route($this,$method, $offset);
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
    public function fetch($method, $url): \Generator{
        $result = [];
        foreach (is_array($method) ? $this->mergeMethods($method) : $this->record[$method] ?? [] as $item){
            if($data = URL::match($item['type'], $item['route'], $url)){
                $result['handle'] = $item['handle'];
                $result['parameter'] = (!is_bool($data)) ? $data : [];
                $result['url'] = $url;
                yield $result;
            }
        }
    }

    /**
     * Set a handler to route for all http methods and url
     *
     * @param $url
     * @param $handle
     * @param $options
     * @return Route
     */
    public function all($url, $handle, $options = []){
        return $this->addRoute("*", $url, $handle, $options);
    }

    /**
     * Set a handler to route with name of this method and url rule
     *
     * @param $url
     * @param $handle
     * @param $options
     * @return Route
     */
    public function get($url, $handle, $options = []){
        return $this->addRoute("GET", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $handle, $options = []){
        return $this->addRoute("POST", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $handle, $options = []){
        return $this->addRoute("PUT", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, $handle, $options = []){
        return $this->addRoute("PATCH", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $handle, $options = []){
       return  $this->addRoute("DELETE", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function head($url, $handle, $options = []){
        return $this->addRoute("HEAD", $url, $handle, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function group($prefix, callable $fn){
        $this->prefix = $prefix;
            $fn($this);
        $this->prefix = "";
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

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->record);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->record = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace . "\\";
    }

    /**
     * Generate a url
     *
     * @param $name
     * @param null $values
     * @return string
     */
    public function generate($name, $values = NULL){
        if(isset($this->recordName[$name])){
            return "";
        }
        $val = $this->recordName[$name];
        $route = $this->record[$val[0]][$val[1]]['route'];
        if (strstr($route,'$'))
            return $this->resolveDynamicRoute($route, $values);
        else
            return $route;
    }

    /**
     * Add extra meta-data to a record of route
     *
     * @param $tag
     * @param $route
     * @return $this
     */
    public function tag($tag, $route)
    {
        if(isset($this->tags[$tag])){
            $this->tags[$tag] = [];
        }

        if(!in_array($route, $this->tags[$tag]))
            $this->tags[$tag][] =  $route;

        return $this;
    }

    /**
     * Add extra meta-data to a record of route
     *
     * @param $method
     * @param $offset
     * @param $name
     * @param $value
     */
    public function meta($method, $offset, $name, $value)
    {
        $this->record[$method][$offset][$name] = $value;
    }

    /**
     * Add extra meta-data to a record of route
     *
     * @param $method
     * @param $offset
     * @param $name
     * @return bool
     */
    public function hasMeta($method, $offset, $name)
    {
        return isset($this->record[$method][$offset][$name]);
    }

    /**
     * Set a name to a route
     *
     * @param $method
     * @param $offset
     * @param $value
     */
    public function setName($method, $offset, $value)
    {
        $this->recordName[$value] = [$method, $offset];
    }

    /**
     * @param $route
     * @param $values
     * @return string
     */
    private function resolveDynamicRoute($route, $values)
    {
        $result = [];
        $route = explode('/', $route);
        foreach($route as $chunk){
            if(($flag =$chunk !== "") && $chunk[0] === '$'){
                $name = Str::slice($chunk, 1);
                $value = $values[$name];
                $result[] = $value;
            }elseif($flag){
                $result[] = $chunk;
            }
        }
        return implode('/', $result);
    }
}