<?php

namespace X2Core\RouterSystem;


/**
 * Class Route
 * @package App\Types
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class Route
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $offset;

    /**
     * Route constructor.
     * @param Router $router
     * @param $method
     * @param $offset
     */
    public function __construct(Router $router, $method, $offset)
    {
        $this->router = $router;
        $this->method = $method;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Shortcut to meta of instance route
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function addMeta($name, $value){
        $this->router->meta($this->method, $this->offset, $name, $value);
    }

    /**
     * @param $value
     */
    public function setName($value){
        $this->router->setName($this->method, $this->offset, $value);
    }
}