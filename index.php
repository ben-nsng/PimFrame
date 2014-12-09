<?php

if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1')))
	define('ENVIRONMENT', 'development');
else
	define('ENVIRONMENT', 'production');

define('BASE_DIR', __DIR__);
define('BASEDIR', __DIR__);
define('HOSTNAME', isset($host_name) ? $host_name : '/');

switch(ENVIRONMENT) {
	case 'development':
		error_reporting(E_ALL);
	break;
	
	case 'production':
		error_reporting(0);
	break;
}

//check if the call is restful or in-app usage
define('IS_RESTFUL_CALL', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('IS_PATH_REWRITE', count(get_included_files()) == 1);
include 'application/core/apps.php';

if(IS_RESTFUL_CALL) echo json_encode($apps->run());
else if(IS_PATH_REWRITE) echo $apps->run();
