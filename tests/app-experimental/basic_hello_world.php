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

// require bootstrap file to load basic environment
require BASEPATH . 'bootstrap.php';

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

//$app->get('/elm', function(Request $request, Response $response, RouteContext $context){
//    $context->app->json(['elm' => 45]);
//});

$app->get('/elm/$title', function(Request $request, Response $response, RouteContext $context){
    $response->setContent("Elm -> " . $context['title'] . 'Session store: ' . $context->app->session('hello'))
        ->send();
});

$app->get('/redirect', function(Request $request, Response $response, RouteContext $context){
    $context->app->session('hello', 5**3);
    $context->app->redirect('/');
});

$app->deploy();

