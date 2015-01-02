<?php

class Cookie_Session extends PF_Session {

	private $flash_session = array();

	public function __construct() {
		parent::__construct();
	}

	public function load() {
		$is_init = false;
		if(function_exists('session_status')) $is_init = !(session_status() == PHP_SESSION_NONE);
		else $is_init = !(session_id() == '');

		if(!$is_init) session_start();

		if(isset($_SESSION[$this->flash_key])) {
			$this->flash_session = $_SESSION[$this->flash_key];
			unset($_SESSION[$this->flash_key]);
		}
	}

	public function set($key, $val) {
		$_SESSION[$key] = $val;
	}

	public function get($key) {
		if(isset($this->flash_session[$key])) return $this->flash_session[$key];
		if(isset($_SESSION[$key])) return $_SESSION[$key];
		return false;
	}

	public function remove($key) {
		unset($_SESSION[$key]);
	}

	public function flash($key, $val) {
		if(isset($_SESSION[$this->flash_key]))
			$_SESSION[$this->flash_key][$key] = $val;
		else
			$_SESSION[$this->flash_key] = array(
				$key => $val
				);
	}
}

?>
