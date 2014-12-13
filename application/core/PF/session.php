<?php

abstract class PF_Session {

	protected $flash_key = '__PF_FLASH__';

	public function __construct() {
	}

	abstract function load();
	abstract function set($key, $val);
	abstract function get($key);
	abstract function remove($key);
	abstract function flash($key, $val);

}

?>
