<?php
use Pimple\Container;
$container = new Container();

// ** Create Controller ** //
foreach(glob(BASE_DIR . '/application/controllers/*.php') as $filename) {
	$controller = substr(basename($filename), 0, -4);
	$container[$controller] = function($c) use($controller) {
		return new $controller;
	};
}

// ** Create Model ** //
foreach(glob(BASE_DIR . '/application/models/*.php') as $filename) {
	$model = substr(basename($filename), 0, -4);
	$container[$model] = function($c) use($model) {
		return new $model;
	};
}

// ** Create Service ** //
$container['service'] = function($c) {
	return new IoC_Service($c);
};

$service = $container['service'];

// ** Create Request ** //
$container['request'] = function($c) {
	return new IoC_Request;
};

$request = $container['request'];

// ** Create Config ** //
$container['config'] = function($c) {
	return new IoC_Config($c);
};

$config = $container['config'];

// ** Create Controller ** //
$container['controller'] = function($c) {
	return new IoC_Controller($c);
};

// ** Create Model ** //
$container['model'] = function($c) {
	return new IoC_Model($c);
};

$model = $container['model'];

// ** Create Database ** //
require BASE_DIR . '/application/config/database.php';
$config->add_config(array('database' => $database));

$container['database_statement'] = $container->factory(function($c) {
	return new IoC_Database_Statement($c['database']);
});

$container['database'] = function($c) {
	$database = new IoC_Database($c['config']->database[$c['config']->database['choice']]);
	$database->execute = function($sql, $placeholders = array()) use($c) {
		return $c['database_statement']->query($sql, $placeholders);
	};
	return $database;
};

$database = $container['database'];

// ** Create Session ** //
$container['session'] = function($c) {
	return new IoC_Session($c);
};

$session = $container['session'];

// ** Create Debug ** //
$container['debug'] = function($c) {
	return new IoC_Debug($c);
};

$debug = $container['debug'];

// ** Create Security ** //
require BASE_DIR . '/application/config/security.php';
$config->add_config(array('security' => $security));

$container['security'] = function($c) {
	return new IoC_Security($c['config']->security);
};

$security = $container['security'];

// ** PRE-LOADING COMPONENT ** //
$service->add_service('config');
$service->add_service('controller');
$service->add_service('model');
$service->add_service('database');
$service->add_service('session');
$service->add_service('debug');
$service->add_service('security');
$service->add_service('request');
$database->load();
$session->load();
$model->load('UserModel');
$model->load('NetworkModel');