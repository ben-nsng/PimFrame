<?php

class IoC_Service {

	private $instance;
	private $loading = array();
	private $loaded = array();
	private $model;
	private $service;

	// service - service
	//
	// controller - service, model
	//
	// model - service, model

	public function __construct() {
		$this->instance = Apps::$instance;
		$this->model = new IoC_Service_Model($this);
		$this->service = new IoC_Service_Service($this);
		$this->instance->model = $this->model;
	}

	public function load($class, $type = 'service') {
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

		$class->model = $this->model;
		$class->service = $this->service;
	}

}

class IoC_Service_Model {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function load($class) {
		$this->service->load($class, 'model');
	}
}

class IoC_Service_Service {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function load($class) {
		$this->service->load($class, 'service');
	}
}