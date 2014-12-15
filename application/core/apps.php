<?php

include BASE_DIR . '/application/core/autoload.php';

class Apps {

	private static $instance = null;

	public static function getInstance() {
		if(Apps::$instance == null) {
			Apps::$instance = new Apps;
		}
		return Apps::$instance;
	}

	public $controller;

	private function __construct() {
		self::$instance = $this;

		$this->module = new PF_Module($this);

		//register all the modules into our apps
		$this->module->registers();

		//hook
		$this->hook->apps_construct($this);
	}

	public function run($route = '') {
		$this->request->set_pathinfo($route);

		$this->route->set_route($this->request->get_url_elements());

		return $this->route->invoke();
	}

	public function __destruct() {
		//hook
		$this->hook->apps_destruct($this);

		$this->module->unregisters();
	}
}

//create global apps
$apps = Apps::getInstance();
