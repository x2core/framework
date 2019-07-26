<?php

namespace X2Core\Types;
use Monolog\Handler\HandlerInterface;
use X2Core\Application;
use X2Core\Contracts\EmitterInterface;
use X2Core\Contracts\HandleRoute;
use X2Core\Exceptions\NotImplementException;
use X2Core\Exceptions\RuntimeException;
use X2Core\Foundation\Events\AppFinished;
use X2Core\Foundation\Events\RouteMatchEvent;
use X2Core\Foundation\Services\Router;
use X2Core\QuickApplication;
use X2Core\Util\Arr;


/**
 * Class RouteContext
 * @package X2Core\Types
 *
 * @property QuickApplication $app
 *
 */
class RouteContext implements HandleRoute, EmitterInterface, \ArrayAccess
{
    use ReadonlyArray, ReadOnlyProperties;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $url;

    /**
     * @var callable[]
     */
    private $events;

    /**
     * @var callable[]
     */
    private $errors;

    /**
     * @var string[]
     */
    private $errorsIgnored;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var QuickApplication
     */
    private $_app;


    /**
     * @var RouteMatchEvent
     */
    private $masterEvent;

    /**
     * @var string
     */
    private $method;

    /**
     * @var callable
     */
    private $handler;

    /**
     * RouteContext constructor.
     * @param array $route
     * @param Application|QuickApplication $app
     * @param Router $router
     */
    public function __construct(array $route, QuickApplication $app, Router $router)
    {
        $this->arrName = "arguments";
        $this->initRoute($route);
        $this->router = $router;
        $this->_app = $app;
        $this->readOnly('app');
    }


    /**
     * @desc execute the next route handle
     */
    public function next()
    {
        $result = $this->router->fetch($this->method, $this->url);
        $this->arguments = $result['parameter'] ?? [];
        $this->_app->dispatch($this->masterEvent, $this);
    }

    /**
     * @param HandlerInterface $handler
     * @return mixed
     * @throws NotImplementException
     */
    public function pipe(HandlerInterface $handler)
    {
        throw new NotImplementException(__METHOD__, __CLASS__);
    }

    /**
     * @return void
     */
    public function finished()
    {
        $this->_app->dispatch(new AppFinished($this->_app));
    }

    /**
     * @param $route
     * @return void
     * @internal param $name
     */
    public function go($route)
    {
        $route = explode(':', $route);
        $result = $this->router->fetch($route[0], $route[1]);
        $this->arguments = $result['parameter'] ?? [];
        $this->_app->dispatch($this->masterEvent, $this);
    }

    /**
     * @param $wildcard
     * @param callable $handle
     *
     * @desc create a handle to name event with wildcard
     * @return mixed
     */
    public function on($wildcard, callable $handle)
    {
        if(!isset($this->events[$wildcard])){
            $this->events[$wildcard] = [];
        }

        $this->events[$wildcard][] = $handle;
        return $this;
    }

    /**
     * @param $wildcard
     *
     * @param null $payload
     * @return mixed
     * @desc emmit a event
     */
    public function emmit($wildcard, $payload = NULL)
    {
        if(!isset($this->events[$wildcard])){
           return NULL;
        }

        return $this->callEvents($this->events[$wildcard], $payload);
    }

    /**
     * @param $errors
     * @param callable $handle
     *
     * @desc create a handle to fix name error by name
     * @return $this
     */
    public function fix($errors, callable $handle)
    {
        foreach ((array) $errors as $error){
            if(!isset($this->errors[$error])){
                $this->errors[$error] = [];
            }
            $this->errors[$error][] = $handle;
        }

        return $this;
    }

    /**
     * @param $errors
     *
     * @desc throw a error by name that must handle for fix handler
     * @return mixed
     */
    public function report($errors)
    {
        if(!isset($this->errors[$errors])){
            return false;
        }

        foreach ((array) $errors as $error){
            $this->callEvents($this->errors[$error]);
        }

        return true;
    }

    /**
     * @param $wildcard
     *
     * @desc basically to delete event by wildcard
     * @return mixed
     */
    public function skip($wildcard)
    {
       if(isset($this->events[$wildcard])){
           unset($this->events[$wildcard]);
           return true;
       }

       return false;
    }

    /**
     * @param $errors
     *
     * @desc basically to delete handle or ignore if is emitted
     */
    public function ignore($errors)
    {
        $this->errorsIgnored[] = $errors;
    }

    /**
     * @param $events
     * @param $payload
     *
     * return array
     * @internal param $wildcard
     */
    private function callEvents($events, $payload = NULL)
    {
        foreach ($events as $event){
            $event($payload, $this);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    protected function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $route
     * @throws RuntimeException
     */
    protected function initRoute($route): void
    {
        if(!Arr::contains($route, ['method', 'url', 'parameter']))
            throw new RuntimeException('The arr that describe the route is not valid');
        $this->method = $route['method'];
        $this->url = $route['url'];
        $this->arguments = $route['parameter'];
        $this->handler = $route['handle'];
    }

    /**
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }
}