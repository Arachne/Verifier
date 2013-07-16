<?php

use Nette\Config\Configurator;

// Composer autoloading
require_once __DIR__ . '/../../vendor/autoload.php';

//register_shutdown_function(function () {
	//Mockery::close();
//});

// Configure application
$configurator = new Configurator();

// Enable RobotLoader
$configurator->setTempDirectory(__DIR__ . '/../_temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
