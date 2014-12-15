<?php

class PF_Hook {

	private $apps = null;

	public function __construct($apps) {
		$this->apps = $apps;
	}

	public function load($apps) {
		$hooks = $this->apps->config->get('hook');

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
