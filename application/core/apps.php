<?php

require BASE_DIR . '/application/core/autoload.php';

class Apps {

	public static $instance;
	public $controller;
	private $stime;

	public function __construct() {

		self::$instance = $this;
		$this->stime = microtime(true);

		//require IoC
		require BASE_DIR . '/application/core/IoC.php';

		$self = $this;
		$this->response->add_parser(function($body) use($self) {
			if(ENVIRONMENT == 'development') {
				$body .= $self->debug->get_message();
				return $body;
			}
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
		$success = true;
		$result = null;
		if(!($success = $this->controller->pre_routing())) $this->response->error_404();

		if($success !== false) {
			if(method_exists($this->controller, $action_name)) $result = call_user_func_array(array($this->controller, $action_name), array_slice($this->request->url_elements, 3));
			else $result = call_user_func_array(array($this->controller, $method_name), array_slice($this->request->url_elements, 3));
		}

		//post routing
		$this->controller->post_routing();

		return $result;
	}

	private function output($buffer) {
		//return $buffer;
		$buffer = $this->response->parse($buffer);
		if(ENVIRONMENT == 'development' && !IS_RESTFUL_CALL) {
			$buffer .= '<script>$(function() { $("body").prepend("<div class=\"align-right\">Backend Running Time : ' . (microtime(true) - $this->stime) . '<br />Total Running Time : " + (new Date().getTime() / 1000 - ' . $this->stime . ' + "</div><span class=\"clearfix\">&nbsp;</span>")); });</script>';
		}
		return $buffer;
	}

	public function __destruct() {
		ob_end_flush();
	}
}

//create global apps
$apps = new Apps;