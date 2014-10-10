<?php

class IoC_Config {

	private $c;

	public function __construct($container) {
		$this->c = $container;
	}

	public function add_config($config) {
		foreach($config as $key => $val)
			$this->$key = $val;
	}

}