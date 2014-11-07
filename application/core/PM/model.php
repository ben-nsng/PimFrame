<?php

class PM_Model {

	private $instance;

	public function __construct() {
		$this->instance = Apps::$instance;

		$this->load();
	}
	
	private function load() {
		$this->instance->service->loading($this);
	}

}
