<?php

class PM_Config {

	public function __construct() {
	}

	public function add_config($config) {
		foreach($config as $key => $val)
			$this->$key = $val;
	}

}