<?php

class PF_Loader {

	private $app = null;

	public function __construct($apps) {
		$this->apps = $apps;
	}

	public function model($name) {
		$this->apps->module->model_preload($name);
	}

	public function library($name, $config = array()) {
		$this->apps->module->library($name, $config);
	}

	public function helper($name) {
		$this->apps->module->helper($name);
	}

	public function adapter($name) {
		$this->apps->module->adapter($name);
	}

	public function view($view, $data = array(), $exports = false) {
		if($exports) ob_start();

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

}

?>
