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

		//default controller
		$controller_name = ucfirst($this->config->default['controller']) . 'Controller';
		$elems = $this->request->url_elements;
		$args = $elems;

		//check if there is existing controller
		if(count($elems) > 1 && class_exists($controller = ucfirst($elems[1]) . 'Controller')) {
			$controller_name = $controller;
			$method_name = isset($elems[2]) ? $elems[2] : 'index';
			$args = array_slice($args, 2);
		}
		else {
			//no default controller
			if(!class_exists($controller_name)) return $this->response->error_404();
			//use default controller
			$method_name = isset($elems[1]) ? $elems[1] : 'index';
			$args = array_slice($args, 1);
		}

		//create controller instance
		$this->service->load($controller_name, 'controller');
			
		$action_name = $method_name . '_' . strtolower($this->request->verb);

		//pre routing
		$success = true;
		$result = null;
		$success = $this->controller->pre_routing();
		
		if($success !== false) {
			if(method_exists($this->controller, $action_name))
				//call RESTful first
				$result = call_user_func_array(
					array($this->controller, $action_name), $args);
			else if(method_exists($this->controller, $method_name))
				//if no RESTful, then class default method name
				$result = call_user_func_array(
					array($this->controller, $method_name), $args);

			//post routing
			$this->controller->post_routing();
		}

		return $result;
	}

	private function output($buffer) {
		//return $buffer;
		$buffer = $this->response->parse($buffer);
		if(ENVIRONMENT == 'development' && !IS_RESTFUL_CALL) {
		//	$buffer .= '<script>$(function() { $("body").prepend("<div class=\"align-right\">Backend Running Time : ' . (microtime(true) - $this->stime) . '<br />Total Running Time : " + (new Date().getTime() / 1000 - ' . $this->stime . ' + "</div><span class=\"clearfix\">&nbsp;</span>")); });</script>';
		}
		return $buffer;
	}

	public function __destruct() {
		ob_end_flush();
	}
}

//create global apps
$apps = new Apps;