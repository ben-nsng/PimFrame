<?php

class PM_Service {

	private $instance;
	private $loading = array();
	private $loaded = array();
	private $model;
	private $service;
	private $load;

	// service - service
	//
	// controller - service, model
	//
	// model - service, model

	public function __construct() {
		$this->instance = Apps::$instance;
		$this->load = new PM_Service_Loader($this);
		$this->instance->load = $this->load;
		//$this->model = new PM_Service_Model($this);
		//$this->service = new PM_Service_Service($this);
		$this->instance->load = $this->load;
	}

	public function _load($class, $type = 'service') {
		$instance = $this->instance;

		if(in_array($class, $this->loading)) return;

		if(in_array($class, $this->loaded)) {
			//set the current controller
			if($type == 'controller') $instance->controller = $instance->$class;
			return;
		}

		$this->loading[] = $class;

		//create a new controller or a new model
		if(!isset($instance->$class) && ($type == 'controller' || $type == 'model')) {
			//init class
			$instance->$class = new $class;
			//set the current controller
			if($type == 'controller') $instance->controller = $instance->$class;
		}

		foreach($this->loaded as $service) {
			$instance->$class->$service = $instance->$service;
			//if(stripos($service, 'controller') !== false || stripos($service, 'model') !== false)
				$instance->$service->$class = $instance->$class;
		}

		//add to the loaded class
		if(($key = array_search($class, $this->loading)) !== false) unset($this->loading[$key]);
		$this->loaded[] = $class;
	}

	public function loading($class) {
		$instance = $this->instance;

		$class->load = $this->load;
		$class->service = $this->service;
	}

}

class PM_Service_Loader {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function model($class) {
		$this->service->load($class, 'model');
	}

	public function service($class) {
		$this->service->load($class, 'service');
	}

	public function view($view, $data = array()) {
		GLOBAL $apps;
		extract($data, EXTR_OVERWRITE);

		chdir(BASE_DIR . '/application/views');
		include($view . '.php');
		chdir(BASE_DIR);
	}

	public function helper($class) {
		include(BASE_DIR . '/application/helpers/' . $class . '.php');
	}

}

/*
class PM_Service_Model {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function load($class) {
		$this->service->load($class, 'model');
	}
}

class PM_Service_Service {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function load($class) {
		$this->service->load($class, 'service');
	}
}
*/