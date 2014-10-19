<?php

require BASE_DIR . '/application/core/autoload.php';

class Apps {

	public static $instance;
	public $controller;

	public function __construct() {

		self::$instance = $this;

		//require IoC
		require BASE_DIR . '/application/core/IoC.php';

		$self = $this;
		$this->response->add_parser(function($body) use($self) {
			$body .= $self->debug->get_message();
			return $body;
		});
		//buffering output
		ob_start(array($this, "output"));
	}

	public function run($route = '') {
		$this->request->set_pathinfo($route);

		//not enough path info
		if(count($this->request->url_elements) <= 2 || !isset($this->request->url_elements[2])) return $this->response->error_404();

		//get controller name
		$controller_name = ucfirst($this->request->url_elements[1]) . 'Controller';

		//check if controller exists
		if (!class_exists($controller_name)) return $this->response->error_404();

		//create controller instance
		$this->service->load($controller_name, 'controller');

		//get method and action name
		$method_name = ucfirst($this->request->url_elements[2]);
		$action_name = $method_name . '_' . strtolower($this->request->verb);

		//pre routing
		$this->controller->pre_routing();

		$result = null;
		if(method_exists($this->controller, $action_name)) $result = $this->controller->$action_name();
		else $result = $this->controller->$method_name();
		
		//post routing
		$this->controller->post_routing();

		if(GET_INCLUDED) return $result;
		else if($result === NULL) return '{}';
		else return json_encode($result);

	}

	private function output($buffer) {
		return $this->response->parse($buffer);
	}

	public function __destruct() {
		ob_end_flush();
	}
}

//create global apps
$apps = new Apps;