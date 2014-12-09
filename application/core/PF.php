<?php

// --** LOAD CORE MODULES **-- //

// ** Create Service ** //
$this->service = new PF_Service;

// ** Create Request ** //
$this->request = new PF_Request;

// ** Create Response ** //
$this->response = new PF_Response;

// ** Create Config ** //
$this->config = new PF_Config;

include BASE_DIR . '/application/config/config.php';
$this->config->set('config', $config);

// ** Create Database ** //
include BASE_DIR . '/application/config/database.php';
$this->config->set('database', $database);

#$this->database = new PF_Database($this->config->database[$this->config->database['choice']]);

// ** Create Session ** //
//$this->session = new PF_Session;

// ** Create Debug ** //
$this->debug = new PF_Debug;

// ** Create Security ** //
include BASE_DIR . '/application/config/security.php';
$this->config->set('security', $security);
$this->security = new PF_Security($this->config->get('security'));

// ** Create Storage ** //
$this->storage = new PF_Storage;

// ** Create Upload ** //
$this->upload = new PF_Upload;

// ** Create I18n ** //
//$this->i18n = new PF_I18n;

// ** Create Form Control ** //
$this->form = new PF_Form;

// ** Create Hooks ** //
include BASE_DIR . '/application/config/hooks.php';
$this->config->set('hooks', $hooks);
$this->hooks = new PF_Hooks($this->config->get('hooks'));

// ** Create Paginator ** //
//$this->paginator = new PF_Paginator;

// ** Create Url ** //
$this->url = new PF_Url;

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
$this->service->_load('hooks');
//$this->service->_load('paginator');
$this->service->_load('url');

// --** USER DEFINED MODULES **-- //
// includes 'session', 'database'
$modules = $this->config->get('config')['modules'];
$this->service->load_modules($modules);

// --** USER DEFINED HELPERS **-- //
$helpers = $this->config->get('config')['helpers'];
$this->service->load_helpers($helpers);

// --** USER DEFINED LIBRARIES **-- //
$libraries = $this->config->get('config')['libraries'];
$this->service->load_libraries($libraries);

// --** USER DEFINED ADAPTERS **-- //
$adapters = $this->config->get('config')['adapters'];
$this->service->load_adapters($adapters);
