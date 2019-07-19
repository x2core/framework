<?php

namespace X2Core;

use Monolog\Logger;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Cache\Cache;
use Monolog\Handler\HandlerInterface;
use Foundation\Database\ActiveRecord;
use X2Core\Foundation\Events\AppDeploy;
use X2Core\Exceptions\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use X2Core\Foundation\Events\AppError;
use X2Core\Foundation\Events\AppForceExit;
use X2Core\Foundation\Events\BeforeSendHeaders;
use X2Core\Foundation\Events\HttpError;
use X2Core\Foundation\Events\HttpNotFound;
use X2Core\Foundation\Services\Router;
use X2Core\Foundation\Services\View;
use X2Core\Util\URL;

/**
 * Class QuickApplication
 * @package X2Core
 *
 * @desc This class is shortcut to use the all ability to need
 * to create a web app of simple way and quickly
 */
class QuickApplication extends Application
{

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
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
     */
    private $database;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var callable[][]
     */
    private $middlewareCollect = [];

    /**
     * QuickApplication constructor.
     * @param array|null $config
     */
    public function __construct(array $config = NULL)
    {
        if($config){
            $this->config('app', $config);
        }
        $this->request =  Request::createFromGlobals();
        $this->session = $this->request->getSession();
        $this->response = new Response();
        $this->router = new Router;
        if($this->config('app.log.enable')){
            $this->logger = new Logger('app.log.name',
                $this->getHandlesLog());
        }
        $this->cache = new \SplFileInfo($this->config('app.cache-file') ?? 'app.cache');
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
            return $this->session->set($name, $key);
        }
    }

    public function deleteSession($name){
        $this->session->remove($name);
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
    public function controller($url, $class){
    }

    /**
     * @param $url
     * @param $class
     */
    public function rest($url, $class){
    }

    /**
     * @param $handle
     *
     * @desc this function to add action all request
     */
    public function pushMiddleware($handle){
        $this->middlewareCollect[] = $handle;
    }

    /**
     * @param $record
     * @param null|mixed[] $hydrate
     *
     * @desc resolve a model of table record in the preset database connection
     * @return ActiveRecord
     */
    public function model($record, $hydrate = NULL){
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
     * @return void
     */
    public function notFound(){
        $this->emmitHeaders();
        $this->response->setStatusCode(Response::HTTP_NOT_FOUND)->send();
        $this->dispatch(new HttpNotFound($this));
    }

    /**
     *
     * @desc to send 404 not found code status
     * @return void
     */
    public function badRequest(){
        $this->emmitHeaders();
        $this->response->setStatusCode(Response::HTTP_BAD_REQUEST)->send();
        $this->dispatch(new HttpError($this, Response::HTTP_BAD_REQUEST));
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
     * @return void
     */
    public function deploy()
    {
        $this->prepare();
        $this->dispatch(new AppDeploy($this));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ((string) $this->config('app.name')) ?? "App object";
    }

    /**
     * @desc to prepare app system
     * @return void
     */
    private function prepare()
    {
    }

    /**
     * @return array
     * @throws IntegrityException
     */
    private function getHandlesLog()
    {
        $handles = [];
        foreach ( $this->config('app.log.handles') as $key => $param){
            if(is_subclass_of($key, HandlerInterface::class)){
                throw new IntegrityException("the handler class is not implements Handle Interface");
            }
            $handles[] = new $key(...$param);
        }
        return $handles;
    }

    /**
     * @desc before send headers of response
     * @return void
     */
    private function emmitHeaders()
    {
        $this->dispatch(new BeforeSendHeaders($this->response->headers));
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
}