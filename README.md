# X2Core

A library to develop your web applications through a lightweight, stable, extensible and quality system. This package is simply a set of well-organized classes that gives you a way to develop your projects with very good practices and very popular patterns.

Basically we have a class called QuickApplication from where we are going to obtain a very complete set of functionalities to develop an application in php, through an event-oriented architecture and with the possibility of implementing MVC among other well-known patterns and programming practices.

In addition to our aforementioned class, we will have a super varied set of utilities that allows us to work on a more semantic and consistent PHP.

## Starting 🚀

To install this code library just use composer to include it in your project and start using it.
```sh
composer require x2core/x2core
```
PHP 7.1 or higher is required.

### Usage


```php
<?php

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use X2Core\Foundation\Events\BootstrapEvent;
use X2Core\QuickApplication;
use X2Core\Types\RouteContext;

// define a basic constant to create a reference path
define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR);

// require vendor file to load basic environment
require BASEPATH . 'vendor/autoload.php';

// define a config var to create app
$config = [
    // name
    'name' => 'TestApp',

    // log system
    'log' => [
        'enable' => true,
        'name' => 'Test_log',
        'handles' => [
            [
                StreamHandler::class,
                BASEPATH . "elm.txt",
                Logger::NOTICE
            ],
            [
                FirePHPHandler::class
            ]
        ]
    ],

    'session' => [
        'enable' => true,
        'options' => ['name' => 'testApp']
    ]
];


$app = new QuickApplication($config);

$app->listen(BootstrapEvent::class, function(BootstrapEvent $event){
   $app = $event->getApplication();
    /** @var QuickApplication $app */
    $app->log('The app is bootstrap ', Logger::NOTICE);
});

$app->get('/', function(Request $request, Response $response, RouteContext $context){
    $response->setContent('Hello World!!')->send();
});

$app->get('/elm/$title', function(Request $request, Response $response, RouteContext $context){
    $response->setContent("Example " . $context['title'] )
        ->send();
});

$app->get('/redirect', function(Request $request, Response $response, RouteContext $context){
    $context->app->redirect('/');
});

$app->deploy();
```
