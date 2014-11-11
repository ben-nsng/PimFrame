<?php

class PF_Config {

	private $config = array();

	public function __construct() {
	}

	public function set($key, $val) {
		$this->config[$key] = $val;
	}

	public function get($key) {
		if(isset($this->config[$key])) return $this->config[$key];
		return false;
	}

}