<?php

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use X2Core\Foundation\Events\BootstrapEvent;
use X2Core\QuickApplication;

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

$app->get('/', function(Request $request, Response $response){
    $response->setContent('Hello World!!')->send();
});

$app->get('/elm', function(QuickApplication $app, $query){
   $app->json($query);
});

$app->get('/elm/$title', function(Response $response, QuickApplication $app, $title){
    $response->setContent("Elm -> " . $title . ' Session store: ' . $app->session('hello'))
        ->send();
});

$app->get('/redirect', function(QuickApplication $application){
    $application->session('hello', 'sys');
    $application->redirect('/');
});

$app->deploy();

