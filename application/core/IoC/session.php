<?php

class IoC_Session {

	public function __construct() {
	}

	public function load() {
		$is_init = false;
		if(function_exists('session_status')) $is_init = !(session_status() == PHP_SESSION_NONE);
		else $is_init = !(session_id() == '');

		if(!$is_init) session_start();
	}

	public function set($key, $val) {
		$_SESSION[$key] = $val;
	}

	public function get($key) {
		if(isset($_SESSION[$key])) return $_SESSION[$key];
		return false;
	}

	public function remove($key) {
		unset($_SESSION[$key]);
	}

}