<?php

namespace X2Core\Foundation\Http;


use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestRule
 * @package X2Core\Foundation\Http
 */
class RequestRule
{
    const GET_METHOD = 'GET';
    const POST_METHOD = 'POST';
    const PUT_METHOD = 'PUT';
    const PATCH_METHOD = 'PATCH';
    const DELETE_METHOD = 'DELETE';
    const HEAD_METHOD = 'HEAD';
    const ROOT_PATH = '/';

    /**
     * @var string
     */
    private $path;

    /**
     * @var string[]
     */
    private $method = [];

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
     * @return $this
     */
    public function setPath($path)
    {
        $this->parsePath($path);
        return $this;
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
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
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
     * @return $this
     */
    public function setParameter($parameter)
    {
        $this->parameter[] = $parameter;
        return $this;
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
        $this->path = $string;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request){
        $result = $request->getPathInfo() === $this->path;
        return $result;
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

    /**
     * @param array $parameters
     */
    private function resolveParameters(array &$parameters)
    {
        $length = count($parameters);
        if($length > 1){
            $parameters = array_slice($parameters, 1);
        }else{
            $parameters = [];
        }
    }
}