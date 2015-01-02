<?php

abstract class PF_Session {

	protected $flash_key = '__PF_FLASH__';

	public function __construct() {
	}

	abstract protected function load();
	abstract protected function set($key, $val);
	abstract protected function get($key);
	abstract protected function remove($key);
	abstract protected function flash($key, $val);

}

?>
