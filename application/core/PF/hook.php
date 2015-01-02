<?php

class PF_Hook {

	public function __construct() {
	}

	public function load() {
		global $apps;
		$config = $apps->config;
		$hooks = $config->get('hook');

		foreach($hooks as $name => $callback) {
			$this->$name = $callback;
		}
	}

	public function __call($method_name, $args) {
		if(method_exists($this, $method_name) || (isset($this->$method_name) && $this->$method_name instanceof Closure)) {
			return call_user_func_array($this->$method_name, $args);
		}
	}

}
