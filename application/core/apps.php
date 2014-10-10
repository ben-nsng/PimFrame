<?php

require BASE_DIR . '/vendor/autoload.php';

require BASE_DIR . '/application/core/autoload.php';
require BASE_DIR . '/application/core/IoC.php';

class Apps {

	private $request;

	public function __construct() {
		$this->request = $GLOBALS['container']['request'];
	}

	public function run($route = '') {
		$this->request->set_pathinfo($route);

		//not enough path info
		if(count($this->request->url_elements) <= 2 || !isset($this->request->url_elements[2])) return $this->error_404();

		//get controller name
		$controller_name = ucfirst($this->request->url_elements[1]) . 'Controller';

		//check if controller exists
		if (!class_exists($controller_name)) return $this->error_404();

		//create controller instance
		$controller = $GLOBALS['container']['controller'];
		$controller->set_controller($controller_name);

		//get method and action name
		$method_name = ucfirst($this->request->url_elements[2]);
		$action_name = $method_name . '_' . strtolower($this->request->verb);

		//check if method exists
		if(!method_exists($controller_name, $action_name)) 
			if(!method_exists($controller_name, $method_name)) return $this->error_404();
		//call the method and output result
			else $result = $controller->$method_name();
		else $result = $controller->$action_name();

		if($result === NULL) return '{}';
		else if(!GET_INCLUDED) return json_encode($result);
		else return $result;

	}

	public function redirect($page) {
		header('location: ' . HOSTNAME . "$page");
		exit;
	}

	private function error_404() {
		$json = array('error' => 'The page does not exist!');
		return json_encode($json);
	}
}
