<?php

define('ENVIRONMENT', 'development');
define('BASE_DIR', __DIR__);
define('GET_INCLUDED', count(get_included_files()) >= 0);
define('HOSTNAME', isset($host_name) ? $host_name : '/');

switch(ENVIRONMENT) {
	case 'development':
		error_reporting(E_ALL);
	break;
	
	case 'production':
		error_reporting(0);
	break;
}

require 'application/core/apps.php';

if(!GET_INCLUDED) echo $apps->run();
