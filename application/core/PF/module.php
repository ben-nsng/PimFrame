<?php

class PF_Module {

	private $apps = null;
	private $controller = null;
	private $models = array();
	private $libraries = array();
	private $helpers = array();
	private $adapters = array();

	//this is the modules list of pimframe
	private $modules = array(
				'benchmark',
				'request',
				'route',
				'response',
				'config',
				);

	public function __construct($apps) {
		$this->apps = $apps;
	}

	public function registers() {

		$modules = $this->modules;

		foreach($modules as $module) {
			$this->module($module);
		}

		//load models
		$configs = $this->apps->config->get('config');
		foreach($configs['models'] as $model)
			$this->model_preload($model);

		//load helpers
		foreach($configs['helpers'] as $helper)
			$this->helper($helper);

		//load library
		foreach($configs['libraries'] as $library)
			$this->library($library);

		//load modules
		foreach($configs['modules'] as $module) {
			$this->module($module);
			$this->modules[] = $module;
		}

		//load adapters
		$this->adapters($configs['adapters']);
	}

	public function module($module) {
		$class = 'PF_' . ucfirst($module);	//it is the class name

		$this->apps->$module = new $class;
		if(method_exists($this->apps->$module, 'load'))
			$this->apps->$module->load();
	}

	public function controller($con) {
		$this->controller = $con;

		$con->load = new PF_Loader($this->apps);

		$modules = $this->modules;
		foreach($modules as $module)
			$con->$module = $this->apps->$module;

		$adapters = $this->adapters;
		foreach($adapters as $adapter_name)
			$con->$adapter_name = $this->apps->$adapter_name;
	}

	private $i = 0;

	public function model($model) {
		$new_model_name = get_class($model);

		$model->load = new PF_Loader($this->apps);

		$modules = $this->modules;
		foreach($modules as $module)
			$model->$module = $this->apps->$module;

		$models = $this->models;
		foreach($models as $model_name) {
			//assign new model to existing model
			$this->apps->$model_name->$new_model_name = $model;

			//assign existing model to new model
			$model->$model_name = $this->apps->$model_name;
		}

		$adapters = $this->adapters;
		foreach($adapters as $adapter_name)
			$model->$adapter_name = $this->apps->$adapter_name;

		$this->models[] = $new_model_name;
		$this->apps->$new_model_name = $model;
		$this->controller->$new_model_name = $model;
	}

	public function model_preload($model) {
		$model = str_replace('/', '_', $model);
		if(!in_array($model, $this->models)) {
			if(class_exists($model)) {
				//it will go to model and add dependency
				new $model;
			}
		}
	}

	public function library($library, $config = array()) {
		if(!in_array($library, $this->libraries)) {
			if(class_exists($library)) {
				$this->apps->$library = new $library($config);
				$this->libraries[] = $library;
			}
		}
	}

	public function helper($helper) {
		if(!in_array($helper, $this->helpers)) {
			$path = BASE_DIR . '/application/helpers/' . $helper . '.php';
			if(file_exists($path)) {
				include($path);
				$this->helpers[] = $helper;
			}
		}
	}

	public function adapters($adapters) {

		foreach($adapters as $adapter_name => $adapter) {
			foreach($adapter as $class) {
				$path = BASE_DIR . '/application/adapters/' . $adapter_name . '/' . $class . '.php';
				if(file_exists($path)) {
					include(BASE_DIR . '/application/adapters/' . $adapter_name . '/' . $class . '.php');

					$class = ucfirst($class) . '_' . ucfirst($adapter_name);
					$lclass = strtolower($class);
					if(class_exists($class)) {
						$this->apps->$lclass = new $class($this->apps);
						if(method_exists($this->apps->$lclass, 'load'))
							$this->apps->$lclass->load();

						//$this->controller->$lclass = $this->apps->$lclass;

						foreach($this->models as $model)
							$model->$lclass = $this->apps->$lclass;

						$this->adapters[] = $lclass;
					}
				}
			}
		}
	}

	//unload all the module
	//it will only be called when the apps is destroyed
	public function unregisters() {
		$modules = $this->modules;

		foreach($modules as $module) {
			if(method_exists($this->apps->$module, 'unload'))
				$this->apps->$module->unload($this->apps);
		}
	}

}
