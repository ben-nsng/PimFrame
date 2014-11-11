<?php

class PF_Model {

	private $instance;

	public function __construct() {
		$this->instance = Apps::getInstance();

		$this->load();
	}
	
	private function load() {
		$this->instance->service->loading($this);
	}

}
