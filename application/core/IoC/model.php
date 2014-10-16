<?php

class IoC_Model {

	private $c;
	private $models = array();

	public function __construct($container) {
		$this->c = $container;
	}
	
	public function load($apps, $new_model) {
		$this->c['service']->add_model($new_model);
		$apps->$new_model = $this->c[$new_model];
	}

}
