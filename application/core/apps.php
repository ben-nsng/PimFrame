<?php

include BASE_DIR . '/application/core/autoload.php';

class Apps {

	private static $instance = null;

	public static function getInstance() {
		return Apps::$instance;
	}

	public $controller = null;

	public function __construct() {
		$GLOBALS['apps'] = self::$instance = &$this;

		//this component help create other component, eg: helper, library, model
		$this->module = new PF_Module($this);

		//register all the modules into our apps
		$this->module->registers();

		//hook
		if(isset($this->hook))
			$this->hook->apps_construct($this);
	}

	public function run($route = '') {
		$this->request->set_pathinfo($route);

		$this->route->set_route($this->request->get_url_elements());

		return $this->route->invoke();
	}

	public function __destruct() {
		//hook
		if(isset($this->hook))
			$this->hook->apps_destruct($this);

		$this->module->unregisters();
	}
}

//create global apps
new Apps;
