<?php
define('BASEDIR_RUNTEST', __DIR__ . DIRECTORY_SEPARATOR);

require_once BASEDIR_RUNTEST . '../vendor/autoload.php';

// manual autoload src
$autoload = new \Composer\Autoload\ClassLoader();
$autoload->addPsr4('X2Core\\', array(BASEDIR_RUNTEST . '../src'));
$autoload->addPsr4('Test\\', array(BASEDIR_RUNTEST . 'Fixtures', BASEDIR_RUNTEST . 'Test' ));
$autoload->register();

require_once BASEDIR_RUNTEST . "TestsBasicFramework.php";
require_once BASEDIR_RUNTEST . "EventTest.php";
require_once BASEDIR_RUNTEST . "HubTest.php";
require_once BASEDIR_RUNTEST . "ValidatorTest.php";
require_once BASEDIR_RUNTEST . "FileTest.php";

EventTest::toTest();
HubTest::toTest();
ValidatorTest::toTest();
FileTest::toTest();