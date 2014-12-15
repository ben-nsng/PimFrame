<?php

class PF_Config {

	private $config = array();

	public function __construct() {
	}

	public function load() {
		$files = glob(BASE_DIR . '/application/config/*');

		foreach($files as $file) {
			include($file);							//include the config file
			$var = substr(basename($file), 0, -4);	//find the config variable name
			$this->set($var, $$var);				//store it
		}
	}

	public function set($key, $val) {
		$this->config[$key] = $val;
	}

	public function get($key) {
		if(isset($this->config[$key])) return $this->config[$key];
		return false;
	}

}
