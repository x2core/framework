<?php

namespace X2Core\Http;

use Generator;
use X2Core\Preset\Contracts\Dispatcher;
use X2Core\Preset\Contracts\HttpFlow;
use X2Core\Preset\Contracts\Request;
use X2Core\Preset\Contracts\Response;
use X2Core\Preset\Contracts\Router;
use X2Core\Preset\Exceptions\RuntimeException;

/**
 * Class Kernel
 * @package X2Core\Http\Kernel
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 */
class Flow implements HttpFlow
{
    /**
     * Associative array with key and correspond group of callable
     *
     * @var mixed[]
     */
    protected $middleware = [];

    /**
     * The group of middleware apply all route
     *
     * @var mixed[]
     */
    protected $globalMiddleware = [];

    /**
     *
     * @var mixed[]
     */
    protected $polities = [];

    /**
     * Group of callable that process an object or value to obtain a response
     *
     * @var mixed[]
     */
    protected $responsesMods;

    /**
     * Service collector of http routes
     *
     * @var Router
     */
    private $router;

    /**
     * Event manager to make http flow 
     *
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * Kernel constructor.
     *
     * @param Router $router
     * @param Dispatcher $dispatcher
     */
    public function __construct(Router $router, Dispatcher $dispatcher)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Insert a callable or class to queue of middleware flow
     *
     * @param $middleware
     */
    public function add($middleware)
    {
        array_push($this->globalMiddleware, $middleware);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($middleware)
    {
        array_unshift($this->globalMiddleware, $middleware);
    }

    /**
     * Register a polity to apply a request
     *
     * @param $polity
     */
    public function apply($name, $polity)
    {
        $this->polities[$name] = $polity;
    }

    /**
     * Register a function to parse a value or object to obtain a response
     *
     * @param $type
     * @param $process
     */
    public function addParser($type, $process)
    {
        $this->responsesMods[$type] = $process;
    }

    /**
     * Insert a callable or class to queue of middleware flow
     *
     * @param $mapper
     */
    public function map($mapper)
    {
        
    }

    /**
     * Execute a request to obtain a response
     *
     * @param Request $request
     * @return mixed
     */
    public function execute(Request $request){
        $this->dispatcher->dispatch(new RequestEvent($request));
        $uri = $request->getPath();
        $method = $request->getMethod();
        $matches = $this->router->fetch($method, $uri);
        if($matches->valid() === NULL){
            return $this->parse($this->handle($request, $matches));
        }else{
            return $this->diagnostic();
        }
    }

    /**
     * @param Response $response
     * @return void
     */
    public function terminate(Response $response)
    {
        $this->dispatcher->dispatch(new BeforeSendResponse($response));
        $response->send();
        $this->dispatcher->dispatch(new FinishedEvent($response));
    }

    /**
     * @return mixed
     */
    private function diagnostic()
    {
        return null;
    }

    /**
     * Handle a route
     *
     * @param array|Request $current
     * @param Generator $matches
     * @return mixed
     */
    private function handle(Request $current, Generator $matches)
    {
        $this->executeGlobals($current);
        $this->dispatcher->dispatch(new RouteEvent($matches) );
        return null;
    }

    /**
     * Execute all globals middleware
     *
     * @param Request $current
     * @param bool $middleware
     * @throws HttpException
     */
    private function executeGlobals(Request $current, $middleware = false)
    {
        $currentMiddleware = $middleware ? $middleware : current($this->globalMiddleware);
        $next = current($this->globalMiddleware);
        $nextHandle = function(Request $request = NULL) use($current, $next){
            $this->executeGlobals($request ?? $current, $next);
        };
        if( is_subclass_of($currentMiddleware, Middleware::class)){
                $object = new $currentMiddleware;
                $object->onRequest($current, $nextHandle);
        }elseif (is_callable($currentMiddleware)){
                $currentMiddleware($currentMiddleware, $nextHandle);
        }else{
            throw new HttpException('Invalid Middleware');
        }
    }

    /**
     * Create a response base on a type
     *
     * @param $value
     * @return mixed
     * @throws RuntimeException
     */
    private function parse($value){
        $type = gettype($value);
        if($type === "object")
            $type = get_class($value);
        if(isset($this->responsesMods[$type]))
            throw new RuntimeException('Not is possible accept this type as response: ' . $type);
        return $this->responsesMods[$type]($value);
    }
}