<?php

// Define path to application directory
define('APPLICATION_PATH', realpath(__DIR__ . '/../../application'));

// Define application environment
const APPLICATION_ENV = 'testing';

// Define test root
define('TEST_ROOT', realpath(__DIR__ . '/..'));

// Define test constants
require_once 'constants.php';

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(TEST_ROOT . '/library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();