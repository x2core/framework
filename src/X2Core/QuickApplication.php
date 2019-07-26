<?php

namespace X2Core;

use Closure;
use Monolog\Logger;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Cache\Cache;
use Monolog\Handler\HandlerInterface;
use Foundation\Database\ActiveRecord;
use X2Core\Contracts\ActiveRecordInterface;
use X2Core\Exceptions\IntegrityException;
use X2Core\Foundation\Database\Connector\DBAL;
use X2Core\Foundation\Events\AppDeploy;
use X2Core\Exceptions\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use X2Core\Foundation\Events\AppError;
use X2Core\Foundation\Events\AppFinished;
use X2Core\Foundation\Events\AppForceExit;
use X2Core\Foundation\Events\AppRequestEvent;
use X2Core\Foundation\Events\BeforeSendHeaders;
use X2Core\Foundation\Events\BootstrapEvent;
use X2Core\Foundation\Events\HttpError;
use X2Core\Foundation\Events\HttpNotFound;
use X2Core\Foundation\Events\NotMatchEvent;
use X2Core\Foundation\Events\RouteMatchEvent;
use X2Core\Foundation\Events\UnloadEvent;
use X2Core\Foundation\Services\Router;
use X2Core\Foundation\Services\View;
use X2Core\Types\RouteContext;
use X2Core\Util\Runtime;
use X2Core\Util\URL;

/**
 * Class QuickApplication
 * @package X2Core
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * @desc This class is shortcut to use the all ability to need
 * to create a web app of simple way and quickly
 *
 * QuickApplication is a implementation centralized but base on events and routes system
 * that allow make flexible web app
 */
class QuickApplication extends Application
{
    /**
     * @var Cache
     *
     * @desc this is cache container and manager drive to work in in/out cache data
     */
    private $cache;

    /**
     * @var Session
     *
     * @desc this is a manager Session of Request System
     */
    private $session;

    /**
     * @var Router
     *
     * Router manager service to match request with avaliable routes
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Connection
     *
     * @desc Connection manager support to use your database
     *
     */
    private $database;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var callable[]
     *
     * @desc This var content is collection of func to execute before route handle
     */
    private $middlewareCollect = [];

    /**
     * @var Closure[] services
     *
     * @desc this var is a container of services that can extend
     * your app with other libraries
     */
    private $services;

    /**
     * QuickApplication constructor.
     * @param array|null $config
     */
    public function __construct(array $config = NULL)
    {
        // check and load configures
        if($config){
            $this->config('app', $config);
        }

        // define few system vars
        $this->request =  Request::createFromGlobals();
        $this->session = $this->request->getSession();
        $this->response = new Response();
        $this->router = new Router;

        // initialize log support
        // the config avaliable should has value to configure the handler logger
        if($this->config('app.log.enable')){
            $this->logger = new Logger('app.log.name',
                $this->getHandlesLog());
        }

        // create cache support
        $this->cache = new \SplFileInfo($this->config('app.cache-file') ?? 'app.cache');

        // define action binder for default to important events
        // that this app emmited to execute 
        $this->bind(AppRequestEvent::class, $this);
        $this->bind(RouteMatchEvent::class, $this);
        $this->bind(NotMatchEvent::class, $this);
        $this->bind(HttpNotFound::class, $this);
        $this->bind(HttpError::class, $this);

        // concatenate events to AppDeploy Event to define app flow
        // with to call deply method the AppDeploy is dispatched
        $this->concat(AppDeploy::class, [
            BootstrapEvent::class,
            AppRequestEvent::class,
            UnloadEvent::class,
            AppFinished::class
        ], $this);
    }

    /**
     * @param object $event
     * @param mixed $context
     *
     * @return void
     */
    public function onInteract($event, $context){
        switch (get_class($event)){
            case BootstrapEvent::class:
                break;

            case AppRequestEvent::class:
                    $this->processRoutes();
                break;

            case RouteMatchEvent::class:
                    $this->dispatchRoute($event, $context);
                break;

            case NotMatchEvent::class:
                    $this->diagnosticRoutes();
                break;

            case UnloadEvent::class:
                break;

            case AppFinished::class:
                break;

            case HttpNotFound::class:
                $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->send();
                break;

            case HttpError::class:
                $this->response->setStatusCode($event->getCode())->send();
                break;
        }
    }

    public static function fromCache(){
    }

    /**
     * @param $name
     * @return bool
     */
    public function isCached($name){
        return $this->cache->contains($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSession($name){
        return $this->session->has($name);
    }

    /**
     * @param $name
     * @param null $key
     * @param string $prefix
     * @return mixed
     */
    public function cache($name, $key = NULL, $prefix = "std")
    {
        $name = $prefix . '-' . $name;
        if($key === NULL){
            return $this->cache->fetch($name);
        }else{
            return $this->cache->save($name, $key);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function deleteCache($name){
        return $this->cache->delete($name);
    }

    /**
     * @param $name
     * @param null $key
     * @param string $prefix
     * @return mixed
     */
    public function session($name, $key = NULL, $prefix = "std")
    {
        $name = $prefix . '-' . $name;
        if($key === NULL){
            return $this->session->get($name, NULL);
        }else{
             $this->session->set($name, $key);
        }
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function deleteSession($name){
        $this->session->remove($name);
        return $this;
    }

    /**
     * @param $method
     * @param $url
     * @param $handle
     * @return void
     *
     * @desc This method to register http router that will run
     * if request is matched with preset routes
     * The shortcut method is recommend to configure the router model
     *
     */
    public function route($method, $url, $handle)
    {
        $this->router->pushRoute($method,(strstr($url,'$') ?
            URL::MATCH_ARRAY_PARAM : URL::MATCH_STATIC)
            , $url, $handle);
    }

    /**
     * @return void
     */
    private function processRoutes(){
        $route = $this->router->fetch(['*',$this->request->getMethod()], $this->request->getPathInfo());
        if($route->valid()){
            $this->dispatch(new RouteMatchEvent, new RouteContext($route->current(), $this, $this->router));
        }else{
            $this->dispatch(new NotMatchEvent, [$this, $this->router]);
        }
    }

    /**
     * @param $url
     * @param $handle
     */
    public function all($url, $handle){
        $this->route("*", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function get($url, $handle){
        $this->route("GET", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function post($url, $handle){
        $this->route("POST", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function put($url, $handle){
        $this->route("PUT", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function patch($url, $handle){
        $this->route("PATCH", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function delete($url, $handle){
        $this->route("DELETE", $url, $handle);
    }

    /**
     * @param $url
     * @param $handle
     */
    public function head($url, $handle){
        $this->route("HEAD", $url, $handle);
    }

    /**
     * @param $url
     * @param $class
     */
//    public function controller($url, $class){
//
//    }

    /**
     * @param $url
     * @param $class
     *
     * @desc With url make rest routes base on method
     */
    public function rest($url, $class){
        $app = $this;
        $this->all($url, function(Request $request, Response $response, RouteContext $context) use ($class, $app){
            (new $class($app))->{strtolower($request->getMethod())}($request, $response, $context);
        });
    }

    /**
     * @param $handle
     *
     * @desc this function to add action all request
     */
    public function middleware($handle){
        $this->middlewareCollect[] = $handle;
    }

    /**
     * @param $record
     * @param null|mixed[] $hydrate
     *
     * @desc resolve a model of table record in the preset database connection
     * @return ActiveRecordInterface
     */
    public function model($record, $hydrate = NULL){
        if($this->database && $this->config('app.database.use')){
            $this->databaseInit();
        }
        $key = $this->config('app.database.std-key');
        if(class_exists($record)){
            return new $record($this->database, $key, $hydrate);
        }else{
            return new ActiveRecord($this->database, $record, $key);
        }
    }

    /**
     * @param $name
     *
     * @desc change db connection params
     */
    public function changeDBConnection($name){
        if($this->config('app.database.' . $name))
            $this->databaseInit($name);
    }

    /**
     * @param $path
     * @param $data
     *
     * To render template base on twig view engine
     */
    public function view($path, $data){
        static $viewEngine;
        if($viewEngine === NULL && $this->config('app.view')){
            $viewEngine = new View($this->config('app.view.path'), $this->config('app.view.path'));
            if($exts = $this->config('app.view.extension')){
                if(is_array($exts)){
                    foreach ($exts as $extension){
                        $viewEngine->extend(new $extension);
                    }
                }
            }
        }
        $this->emmitHeaders();
        $this->response->setContent($viewEngine->render($path, $data))->send();
    }

    /**
     * @param $data
     *
     * @desc to send data in format json encoding
     */
    public function json($data){
        $this->emmitHeaders();
        $this->response->headers->set('Content-Type', 'application/json');
        $this->response->setContent(json_encode($data))->send();
    }

    /**
     * @param $url
     */
    public function redirect($url){
        $this->emmitHeaders();
        $redirect = new RedirectResponse($url);
        $redirect->headers = $this->response->headers;
        $redirect->send();
    }

    /**
     *
     * @desc to send no content code status
     * @return void
     */
    public function noContent(){
        $this->emmitHeaders();
        $this->response->setStatusCode(Response::HTTP_NO_CONTENT)->send();
    }

    /**
     *
     * @desc to send 404 not found code status
     * @param string $message
     * @return void
     */
    public function notFound($message = ''){
        $this->emmitHeaders();
        $this->response->setContent($message);
        $this->dispatch(new HttpNotFound());
    }

    /**
     *
     * @desc to send 404 not found code status
     * @return void
     */
    public function badRequest(){
        $this->emmitHeaders();
        $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->send();
        $this->dispatch(new HttpError(Response::HTTP_BAD_REQUEST));
    }

    /**
     * @param $msg
     * @param $level
     * @throws RuntimeException
     */
    public function log($msg, $level = Logger::INFO){
        switch ($level){
            case Logger::INFO:
                $this->logger->info($msg);
                break;

            case Logger::NOTICE:
                $this->logger->notice($msg);
                break;


            case Logger::WARNING:
                $this->logger->warn($msg);
                break;

            case Logger::ERROR:
                $this->logger->err($msg);
                break;

            case Logger::CRITICAL:
                $this->logger->critical($msg);
                break;

            case Logger::EMERGENCY:
                $this->logger->emergency($msg);
                break;

            default:
                 throw new RuntimeException("The log level is not supported");
                break;
        }
    }

    /**
     * @param $msg
     * @param $code
     * @param bool $critical
     */
    public function error($msg, $code, $critical = false){
        $this->dispatch(new AppError($this, $code));
        if($critical){
            $this->log($msg, Logger::CRITICAL);
            $this->dispatch(new AppForceExit($this));
            $this->exit();
        }else{
            $this->log($msg, Logger::ERROR);
        }
    }

    /**
     * @param $service
     * @param Closure $fn
     * @return $this
     * @throws RuntimeException
     */
    public function service($service, Closure $fn){
        if(!($fn instanceof Closure)){
            throw new RuntimeException('Invalided Service Source');
        }
        $this->services[$service] = $fn;
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @desc this magic method is a helper to execute a service that has been created
     */
    public function __call($name, $arguments)
    {
        if(!isset($this->services[$name])){
           throw new \BadMethodCallException;
        }
        $this->services[$name]->call($this, ...$arguments);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param mixed Connection
     */
    public function setDatabase(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @desc this to prepare app configures and dispatch an event of deployment
     * @return void
     */
    public function deploy()
    {
        $this->dispatch(new AppDeploy($this));
    }

    /**
     * @param callable $fn
     *
     * @desc this method is a shortcut to set handle error base on ErrorEvent
     * @return void
     */
    public function handleErrors(callable  $fn){
        Runtime::handleError($fn);
    }

    /**
     * @param $event
     * @param $context
     *
     * @desc handler to process a route
     * @return void
     */
    public function dispatchRoute($event, RouteContext $context){
        Runtime::action(
            $context->getHandler(), [
            $this->request,
            $this->response,
            $context
        ]
        )();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ((string) $this->config('app.name')) ?? "App object";
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array
     * @throws IntegrityException
     */
    private function getHandlesLog()
    {
        $handles = [];
        foreach ( $this->config('app.log.handles') as  $params){
            if(!is_subclass_of($params[0], HandlerInterface::class)){
                throw new IntegrityException("the handler class is not implements Handle Interface");
            }
            $handles[] = new $params[0](...array_slice($params,1));
        }
        return $handles;
    }

    /**
     * @desc dispatch an event before send headers of response
     * @return void
     */
    private function emmitHeaders()
    {
        $this->dispatch(new BeforeSendHeaders($this->response->headers));
    }

    /**
     * @param null $usage
     * @return void
     */
    private function databaseInit($usage = NULL)
    {
        $current =  $usage ?? $this->config('app.database.use');
        $config = $this->config('app.database.' . $current);
        $this->database = DBAL::makeConnection($config['adapter'],
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['name']);
    }

    /**
     * @desc this method make a diagnostic about the problem of routes
     * @return void
     */
    private function diagnosticRoutes()
    {
        $routes = $this->router->fetch(
            [
                '*','GET','POST','PUT','PATCH','DELETE','HEAD'
            ],
            $this->request->getPathInfo()
        );
        if($routes->valid()){
            $this->dispatch(new HttpError(Response::HTTP_METHOD_NOT_ALLOWED));
        }else{
            $this->dispatch(new HttpNotFound());
        }
    }
}