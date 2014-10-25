<?php

define('ENVIRONMENT', 'development');
define('BASE_DIR', __DIR__);
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
$included_file_count = count(get_included_files());
define('IS_RESTFUL_CALL', $included_file_count == 1);

require 'application/core/apps.php';

if(IS_RESTFUL_CALL) echo $apps->run();

