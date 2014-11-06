<?php
/*// ** Create Controller ** //
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
}*/

// ** Create Service ** //
$this->service = new IoC_Service;

// ** Create Request ** //
$this->request = new IoC_Request;

// ** Create Response ** //
$this->response = new IoC_Response;

// ** Create Config ** //
$this->config = new IoC_Config;

include BASE_DIR . '/application/config/config.php';
$this->config->add_config(array('default' => $config));

// ** Create Database ** //
include BASE_DIR . '/application/config/database.php';
$this->config->add_config(array('database' => $database));

$this->database = new IoC_Database($this->config->database[$this->config->database['choice']]);

// ** Create Session ** //
$this->session = new IoC_Session;

// ** Create Debug ** //
$this->debug = new IoC_Debug;

// ** Create Security ** //
include BASE_DIR . '/application/config/security.php';
$this->config->add_config(array('security' => $security));

$this->security = new IoC_Security($this->config->security);

// ** Create Storage ** //
$this->storage = new IoC_Storage;

// ** Create Upload ** //
$this->upload = new IoC_Upload;

// ** Create I18n ** //
$this->i18n = new IoC_I18n;

// ** Create Form Control ** //
$this->form = new IoC_Form;

// ** PRE-LOADING COMPONENT ** //

$this->service->load('config');
$this->service->load('database');
$this->service->load('session');
$this->service->load('debug');
$this->service->load('security');
$this->service->load('request');
$this->service->load('response');
$this->service->load('storage');
$this->service->load('upload');
$this->service->load('i18n');
$this->service->load('form');
$this->session->load();
$this->database->load();
