<?php

class IoC_Model {

	private $c;
	private $models = array();

	public function __construct($container) {
		$this->c = $container;
	}
	
	public function load($new_model) {
		$this->c['service']->add_service($new_model);
	}

}
