<?php

spl_autoload_register('apiAutoload');
function apiAutoload($classname)
{
	//include pimframe modules
	if (preg_match('/PF\_([a-zA-Z0-9]+)$/', $classname, $match)) {
		$file = BASE_DIR . '/application/core/PF/' . strtolower($match[1]) . '.php';
		if(file_exists($file)) {
			include $file;
			return true;
		}
	}

	//include pimframe model
	else if (preg_match('/[a-zA-Z0-9\_]+Model$/', $classname)) {
		$file = BASE_DIR . '/application/models/' . str_replace('_', '/', strtolower(substr($classname, 0, -5))) . '.php';
		if(file_exists($file)) {
			include $file;
			return true;
		}
	}

	//include pimframe library
	else if(file_exists($file = BASE_DIR . '/application/libraries/' . $classname . '.php')) {
		include $file;
		return true;
	}

	//include pimframe controller
	else if (preg_match('/[a-zA-Z0-9\_]+$/', $classname)) {
		$file = BASE_DIR . '/application/controllers/' . str_replace('_', '/', strtolower($classname)) . '.php';
		if(file_exists($file)) {
			include $file;
			return true;
		}
	}

	return false;
}
