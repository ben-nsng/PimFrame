<?php

abstract class PF_Controller {

	private $instance;

	public function __construct() {
		$this->instance = Apps::getInstance();

		$this->load();
	}

	private function load() {
		$this->instance->module->controller($this);
	}

	public function __call($method_name, $args) {
		if(method_exists($this, $method_name) || (isset($this->$method_name) && $this->$method_name instanceof Closure))
			return call_user_func_array($this->$method_name, $args);
	}
}
