<?php

//start up mock system environment

require_once BASEPATH . '../../vendor/autoload.php';

// manual autoload src
$autoload = new \Composer\Autoload\ClassLoader();
$autoload->addPsr4('X2Core\\', array(BASEPATH . '../../src/X2Core'));
$autoload->register();
