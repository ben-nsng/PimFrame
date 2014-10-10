<?php

class IoC_Controller {

	private $c;
	private $controller;

	public function __construct($container) {
		$this->c = $container;
	}

	public function set_controller($controller) {
		$this->controller = $this->c[$controller];
		$this->c['service']->add_service($controller);
	}

	public function __call($method_name, $args) {
		return $this->controller->$method_name();
	}
}
