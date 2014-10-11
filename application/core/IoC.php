<?php
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

$this->service = $container['service'];

// ** Create Request ** //
$container['request'] = function($c) {
	return new IoC_Request;
};

$this->request = $container['request'];

// ** Create Config ** //
$container['config'] = function($c) {
	return new IoC_Config;
};

$this->config = $container['config'];

// ** Create Controller ** //
$container['controller'] = function($c) {
	return new IoC_Controller($c);
};

// ** Create Model ** //
$container['model'] = function($c) {
	return new IoC_Model($c);
};

$this->model = $container['model'];

// ** Create Database ** //
require BASE_DIR . '/application/config/database.php';
$this->config->add_config(array('database' => $database));

$container['database_statement'] = $container->factory(function($c) {
	return new IoC_Database_Statement($c['database']);
});

$container['database'] = function($c) {
	$database = new IoC_Database($c['config']->database[$c['config']->database['choice']]);
	return $database;
};

$this->database = $container['database'];

// ** Create Session ** //
$container['session'] = function() {
	return new IoC_Session;
};

$this->session = $container['session'];

// ** Create Debug ** //
$container['debug'] = function($c) {
	return new IoC_Debug;
};

$this->debug = $container['debug'];

// ** Create Security ** //
require BASE_DIR . '/application/config/security.php';
$this->config->add_config(array('security' => $security));

$container['security'] = function($c) {
	return new IoC_Security($c['config']->security);
};

$this->security = $container['security'];

// ** PRE-LOADING COMPONENT ** //
$this->service->add_service('config');
$this->service->add_service('controller');
$this->service->add_service('model');
$this->service->add_service('database');
$this->service->add_service('session');
$this->service->add_service('debug');
$this->service->add_service('security');
$this->service->add_service('request');
$this->session->load();
$this->database->load();
$this->model->load($this, 'UserModel');
$this->model->load($this, 'NetworkModel');
$this->model->load($this, 'LangModel');
$this->model->load($this, 'WorkModel');
