<?php

class PF_Service {

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
		$this->instance = Apps::getInstance();
		$this->load = new PF_Service_Loader($this);
		$this->instance->load = $this->load;
		//$this->model = new PF_Service_Model($this);
		//$this->service = new PF_Service_Service($this);
		//$this->instance->load = $this->load;
	}

	public function _load($class, $type = 'service', $config = array()) {
		$instance = $this->instance;

		if(in_array($class, $this->loading)) return;

		if(in_array($class, $this->loaded)) {
			//set the current controller
			if($type == 'controller') $instance->controller = $instance->$class;
			return;
		}

		//load library class
		if($type == 'library') {
			$instance->$class = new $class($config);
			foreach($this->loaded as $service)
				$instance->$service->$class = $instance->$class;
			$this->loaded[] = $class;
		}

		$this->loading[] = $class;

		//create a new controller or a new model
		if(!isset($instance->$class) && ($type == 'controller' || $type == 'model')) {
			//init class
			if($type == 'controller') $this->instance->hooks->pre_controller_construct(Apps::getInstance());
			$instance->$class = new $class;
			if($type == 'controller') $this->instance->hooks->post_controller_construct(Apps::getInstance());

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

	public function load_adapters($adapters) {
		foreach($adapters as $adapter_name => $adapter) {
			foreach($adapter as $class) {
				$path = BASE_DIR . '/application/adapters/' . $adapter_name . '/' . $class . '.php';
				if(file_exists($path)) {
					include(BASE_DIR . '/application/adapters/' . $adapter_name . '/' . $class . '.php');

					$class = ucfirst($class) . '_' . ucfirst($adapter_name);
					$lclass = strtolower($class);
					if(class_exists($class)) {
						$this->instance->$lclass = new $class;
						$this->instance->service->_load($lclass);
					}
				}
			}
		}
	}

	public function load_modules($modules) {
		foreach($modules as $module) {
			$module_name = 'PF_' . $module;
			$this->instance->$module = new $module_name;
			$this->instance->service->_load($module);
			if(method_exists($this->instance->$module, 'load'))
				$this->instance->$module->load();
		}
	}

	public function load_helpers($helpers) {
		foreach($helpers as $helper) {
			$path = BASE_DIR . '/application/helpers/' . $helper . '_helper.php';
			if(file_exists($path)) include($path);
		}
	}

	public function load_libraries($libraries) {
		foreach($libraries as $library) {
			$library_name = 'PF_' . $library;
			$this->instance->$library = new $library_name;
			$this->instance->service->_load($library, 'library');
		}
	}

}

class PF_Service_Loader {

	private $service;

	public function __construct($service) {
		$this->service = $service;
	}

	public function model($class) {
		$this->service->_load($class, 'model');
	}

	public function service($class) {
		$this->service->_load($class, 'service');
	}

	public function view($view, $data = array(), $exports = false) {
		if($exports) ob_start();

		GLOBAL $apps;
		extract($data, EXTR_OVERWRITE);

		chdir(BASE_DIR . '/application/views');
		include($view . '.php');
		chdir(BASE_DIR);

		if($exports) {
			$view = ob_get_contents();
			ob_end_clean();
			return $view;
		}
	}

	public function library($class, $config = array()) {
		$this->service->_load($class, 'library', $config);
	}

}
