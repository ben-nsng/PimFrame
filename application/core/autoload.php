<?php

spl_autoload_register('apiAutoload');
function apiAutoload($classname)
{
	if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
		if(file_exists(BASE_DIR . '/application/controllers/' . $classname . '.php')) {
			include BASE_DIR . '/application/controllers/' . $classname . '.php';
			return true;
		}
		else
			return false;
	} elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
		if(file_exists(BASE_DIR . '/application/models/' . $classname . '.php')) {
			include BASE_DIR . '/application/models/' . $classname . '.php';
			return true;
		}
		else
			return false;
	} elseif (preg_match('/IoC\_([a-zA-Z]+)$/', $classname, $match)) {
		if(file_exists(BASE_DIR . '/application/core/IoC/' . strtolower($match[1]) . '.php')) {
			include BASE_DIR . '/application/core/IoC/' . strtolower($match[1]) . '.php';
			return true;
		}
		else
			return false;
	}

	return false;
}