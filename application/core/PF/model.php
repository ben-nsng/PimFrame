<?php

abstract class PF_Model {

	private $instance;

	public function __construct() {
		$this->instance = Apps::getInstance();

		$this->load();
	}

	private function load() {
		$this->instance->module->model($this);
	}

}
