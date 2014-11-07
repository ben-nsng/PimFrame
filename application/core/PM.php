<?php

// --** LOAD CORE MODULES **-- //

// ** Create Service ** //
$this->service = new PM_Service;

// ** Create Request ** //
$this->request = new PM_Request;

// ** Create Response ** //
$this->response = new PM_Response;

// ** Create Config ** //
$this->config = new PM_Config;

include BASE_DIR . '/application/config/config.php';
$this->config->set('config', $config);

// ** Create Database ** //
include BASE_DIR . '/application/config/database.php';
$this->config->set('database', $database);

#$this->database = new PM_Database($this->config->database[$this->config->database['choice']]);

// ** Create Session ** //
//$this->session = new PM_Session;

// ** Create Debug ** //
$this->debug = new PM_Debug;

// ** Create Security ** //
include BASE_DIR . '/application/config/security.php';
$this->config->set('security', $security);

$this->security = new PM_Security($this->config->get('security'));

// ** Create Storage ** //
$this->storage = new PM_Storage;

// ** Create Upload ** //
$this->upload = new PM_Upload;

// ** Create I18n ** //
//$this->i18n = new PM_I18n;

// ** Create Form Control ** //
$this->form = new PM_Form;

// ** LOADING COMPONENT ** //

$this->service->_load('config');
$this->service->_load('debug');
$this->service->_load('security');
$this->service->_load('request');
$this->service->_load('response');
$this->service->_load('storage');
$this->service->_load('upload');
//$this->service->_load('i18n');
$this->service->_load('form');

// --** USER DEFINED MODULES **-- //
// includes 'session', 'database'
$modules = $this->config->get('config')['modules'];

foreach($modules as $module) {
	$module_name = 'PM_' . $module;
	$this->$module = new $module_name;
	$this->service->_load($module);
	if(method_exists($this->$module, 'load'))
		$this->$module->load();
}
