<?php

namespace X2Core\Foundation\Http;


use Symfony\Component\HttpFoundation\Request;

class RequestRule
{
    const GET_METHOD = 'GET';
    const POST_METHOD = 'POST';
    const PUT_METHOD = 'PUT';
    const PATCH_METHOD = 'PATCH';
    const DELETE_METHOD = 'DELETE';
    const HEAD_METHOD = 'HEAD';

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string[]
     */
    private $parameter;

    /**
     * @var callable[]
     */
    private $gates;

    /**
     * @var string[]
     */
    private $metadata;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string[]
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param string $parameter
     */
    public function setParameter($parameter)
    {
        $this->parameter[] = $parameter;
    }

    /**
     * @return callable[]
     */
    public function getGates()
    {
        return $this->gates;
    }

    /**
     * @param callable $gates
     */
    public function setGates(callable $gates)
    {
        $this->gates[] = $gates;
    }

    /**
     * @param $string
     */
    public function parsePath($string){
       $this->path = preg_replace('/{[A-z]}/', "(.*)", $string);

    }

    /**
     * @param Request $request
     * @return int
     */
    public function match(Request $request){
        return preg_match($this->path, $request->getPathInfo(), $this->parameter);
    }

    /**
     * @param $name
     * @return string
     */
    public function getMetadata($name)
    {
        return $this->metadata[$name];
    }

    /**
     * @param string $name
     * @param string $metadata
     */
    public function setMetadata($name, $metadata)
    {
        $this->metadata[$name] = $metadata;
    }

}