<?php

// ** Create Service ** //
$this->service = new PM_Service;

// ** Create Request ** //
$this->request = new PM_Request;

// ** Create Response ** //
$this->response = new PM_Response;

// ** Create Config ** //
$this->config = new PM_Config;

include BASE_DIR . '/application/config/config.php';
$this->config->add_config(array('default' => $config));

// ** Create Database ** //
include BASE_DIR . '/application/config/database.php';
$this->config->add_config(array('database' => $database));

#$this->database = new PM_Database($this->config->database[$this->config->database['choice']]);

// ** Create Session ** //
$this->session = new PM_Session;

// ** Create Debug ** //
$this->debug = new PM_Debug;

// ** Create Security ** //
include BASE_DIR . '/application/config/security.php';
$this->config->add_config(array('security' => $security));

$this->security = new PM_Security($this->config->security);

// ** Create Storage ** //
$this->storage = new PM_Storage;

// ** Create Upload ** //
$this->upload = new PM_Upload;

// ** Create I18n ** //
//$this->i18n = new PM_I18n;

// ** Create Form Control ** //
$this->form = new PM_Form;

// ** PRE-LOADING COMPONENT ** //

$this->service->_load('config');
#$this->service->_load('database');
$this->service->_load('session');
$this->service->_load('debug');
$this->service->_load('security');
$this->service->_load('request');
$this->service->_load('response');
$this->service->_load('storage');
$this->service->_load('upload');
//$this->service->_load('i18n');
$this->service->_load('form');
$this->session->load();
#$this->database->load();