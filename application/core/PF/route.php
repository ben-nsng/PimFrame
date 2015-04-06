<?php

class PF_Route {

	private $controller = null;
	private $method = null;
	private $args = null;

	public function __construct() {
	}

	public function set_route($elems) {
		$path = BASE_DIR . '/application/controllers';
		$controller = '';

		//check if controller inside folders
		$i = 0;
		for(; $i < count($elems); $i++) {
			if(is_dir($path . '/' . $elems[$i])) {
				$path .= '/' . $elems[$i];
				$controller .= $elems[$i] . '_';
			}
			else
				break;
		}

		if($controller != '') {
			//matched controller inside folder
			//var_dump($controller . $elems[$i]);
			if(isset($elems[$i]) && class_exists($controller . $elems[$i])) {

				$this->controller = $controller . ucfirst($elems[$i++]);

				if($this->check_route($this->controller, isset($elems[$i]) ? $elems[$i] : 'index', $elems, $i)) return;

				/*
				if(isset($elems[$i]) && $method_name = ($this->check_method_exists($this->controller, $elems[$i]))) {
					$this->method = $method_name;
					$this->args = array_slice($elems, $i++);
					return;
				}
				else if($method_name = ($this->check_method_exists($this->controller, 'index'))) {
					$this->method = $method_name;
					$this->args = array_slice($elems, $i);
					return;
				}
				*/

			}
		}

		//no matched controller inside folder or no matched method inside folder
		//check if request controller exists
		if(count($elems) > 0 && class_exists($elems[0])) {

			//get the controller name first
			$this->controller = ucfirst($elems[0]);

			//check if the method exists in the controller
			if($this->check_route($elems[0], isset($elems[1]) ? $elems[1] : 'index', $elems, 1)) return;
			/*
			if(isset($elems[1]) && $method_name = ($this->check_method_exists($elems[0], $elems[1]))) {
				$this->method = $method_name;
				$this->args = array_slice($elems, 2);
				return;
			}
			//check if 'index' exists in the controller
			else if($method_name = ($this->check_method_exists($elems[0], 'index'))) {
				$this->method = $method_name;
				$this->args = array_slice($elems, 1);
				return;
			}
			*/
		}
		else {
			global $apps;
			$config = $apps->config;
			//otherwise, use default controller
			$config = $config->get('config');
			$controller = $config['controller']; //default controller

			if(class_exists($controller)) {
				$this->controller = ucfirst($controller);

				if($this->check_route($controller, isset($elems[0]) ? $elems[0]: 'index', $elems, 0)) return;

				/*
				if(isset($elems[0]) && $method_name = ($this->check_method_exists($controller, $elems[0]))) {
					$this->method = $method_name;
					$this->args = array_slice($elems, 1);
					return;
				}
				else if($method_name = ($this->check_method_exists($controller, 'index'))) {
					$this->method = $method_name;
					$this->args = array_slice($elems, 0);
					return;
				}
				*/
			}
		}

		$this->controller = false;
		$this->method = false;
		$this->args = array();
	}

	public function invoke() {
		if(($controller = $this->controller) !== false) {

			$controller = new $controller;
			$method = $this->get_method();
			$args = $this->get_args();
			$success = true;
			$result = null;

			if(method_exists($controller, 'pre_routing')) $success = $controller->pre_routing();

			if($success !== false) $result = call_user_func_array(array($controller, $method), $args);

			if(method_exists($controller, 'post_routing')) $controller->post_routing();

			return $result;
		}
		else {
			global $apps;
			$response = $apps->response;
			return $response->error_404();
		}
	}

	public function get_controller() {
		return $this->controller;
	}

	public function get_method() {
		return $this->method;
	}

	public function get_args() {
		return $this->args;
	}

	private function check_route($controller, $method, $elems, $i) {
		if($method_name = ($this->check_method_exists($controller, $method))) {
			$this->method = $method_name;
			$this->args = array_slice($elems, ++$i);
			return true;
		}
		else if($method_name = ($this->check_method_exists($this->controller, 'index'))) {
			$this->method = $method_name;
			$this->args = array_slice($elems, $i);
			foreach($this->args as $arg) if($arg != '') return false;
			return true;
		}
		return false;
	}

	private function check_method_exists($func, $method) {
		global $apps;
		$request = $apps->request;

		if(method_exists($func, $method_name = ($method . '_' . strtolower($request->get_request_verb())))) return $method_name;
		else if(method_exists($func, $method)) return $method;
		return false;
	}

}
