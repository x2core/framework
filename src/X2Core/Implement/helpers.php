<?php


use X2Core\Implement\Application;

/**
 * This function should call once
 * because execute a task fundamental in the application process
 * This function set global application to use by helpers and proxies
 *
 * @param Application $application
 * @return void
 */
function implement_app(Application $application){

}


/**
 * Basic shortcut of \X2Core\Container\Container::register
 * that is bind with current application implementation instance
 *
 * @param $service
 * @param null $alt
 * @return mixed
 */
function bind($contract, $element){
    return ;
}

/**
 * Basic shortcut of \X2Core\Container\Container::resolver
 * that is bind with current application implementation instance
 *
 * @param $service
 * @param null $alt
 * @return mixed
 */
function resolve($service, $alt = NULL){
    return ;
}

/**
 * Basic shortcut of \X2Core\Container\Container::inject
 * that is bind with current application implementation instance
 *
 * @param $class
 * @return mixed
 */
function inject($class){
    return ;
}

/**
 * Basic shortcut of boot method of Application
 * that is bind with current application implementation instance
 *
 * @param $module
 * @return mixed
 */
function boot($module){
    return ;
}

/**
 * Basic shortcut of \X2Core\Container\Container::call
 * that is bind with current application implementation instance
 *
 * @param $service
 * @param null $alt
 * @return mixed
 */
function call($service, $alt = NULL){
    return ;
}

/**
 * Basic helper to configures
 * that is bind with current application implementation instance
 *
 * @param $name
 * @param null $data
 * @return mixed
 */
function config($name, $data = NULL){
    return ;
}

/**
 * Basic helper to configures
 * that is bind with current application implementation instance
 *
 * @param $name
 * @param null $path
 * @return mixed
 */
function path($name, $path = NULL){
    return ;
}

/**
 * Basic helpers to create a url
 *
 * @param $name
 * @param null $path
 * @return mixed
 */
function url($name, $path = NULL){
    return ;
}

/**
 * Basic shortcut of \X2Core\EventSystem\Dispatcher::listen
 * that is bind with current application implementation instance
 *
 * @param $event
 * @param $listener
 * @return mixed
 */
function listen($event, $listener){
    return ;
}

/**
 * Basic shortcut of \X2Core\EventSystem\Dispatcher::dispatch
 * that is bind with current application implementation instance
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function dispatch($event, $context = NULL){
    return ;
}

/**
 * A helper to make a report to an activity, var, class or error
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function report($target){
    return ;
}

/**
 * A helper to acces a request object
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function request(){
    return ;
}

/**
 * A helper to fetch input data
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function input($key = null){
    return ;
}

/**
 * A helper to manager storage
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function storage($file = null){
    return ;
}

/**
 * A helper to make a response
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function router($method, $url, $handler){
    return ;
}

/**
 * A helper to create an endpoint to the API System
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function endpoint($url, $handler){
    return ;
}

/**
 * A helper to make a response
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function response($content = '', $code = 200){
    return ;
}

/**
 * A helper to session
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function session($key = null){
    return ;
}

/**
 * A helper to cache
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function cache($key = null){
    return ;
}

/**
 * A helper to log a message
 *
 * @param $event
 * @param null $context
 * @return mixed
 */
function logging($message, $channel = null){
    return ;
}