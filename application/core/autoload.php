<?php

spl_autoload_register('apiAutoload');
function apiAutoload($classname)
{
	if (preg_match('/PF\_([a-zA-Z0-9]+)$/', $classname, $match)) {
		if(file_exists(BASE_DIR . '/application/core/PF/' . strtolower($match[1]) . '.php')) {
			include BASE_DIR . '/application/core/PF/' . strtolower($match[1]) . '.php';
			return true;
		}
	}
	else if (preg_match('/[a-zA-Z0-9\_]+Controller$/', $classname)) {
		if(file_exists(BASE_DIR . '/application/controllers/' . $classname . '.php')) {
			include BASE_DIR . '/application/controllers/' . $classname . '.php';
			return true;
		}
	}
	else if (preg_match('/[a-zA-Z0-9\_]+Model$/', $classname)) {
		if(file_exists(BASE_DIR . '/application/models/' . $classname . '.php')) {
			include BASE_DIR . '/application/models/' . $classname . '.php';
			return true;
		}
	}
	else if(file_exists(BASE_DIR . '/application/libraries/' . $classname . '.php')) {
		include BASE_DIR . '/application/libraries/' . $classname . '.php';
		return true;
	}

	return false;
}
