<?php

class PF_Route {

	private $apps = null;
	private $controller = null;
	private $method = null;
	private $args = null;

	public function __construct($apps) {
		$this->apps = $apps;
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

		//no matched controller inside folder
		if($controller == '') {

			//check if request controller exists
			if(count($elems) > 0 && class_exists($elems[0])) {

				//get the controller name first
				$this->controller = ucfirst($elems[0]);

				//check if the method exists in the controller
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
				
			}
			else {
				//otherwise, use default controller
				$controller = $this->apps->config->get('config')['controller']; //default controller

				if(class_exists($controller)) {
					$this->controller = ucfirst($controller);

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
				}
			}
		}
		else {
			//matched controller inside folder
			//var_dump($controller . $elems[$i]);
			if(isset($elems[$i]) && class_exists($controller . $elems[$i])) {

				$this->controller = $controller . ucfirst($elems[$i++]);

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

			if($success) $result = call_user_func_array(array($controller, $method), $args);

			if(method_exists($controller, 'post_routing')) $controller->post_routing();

			return $result;
		}
		else
			return $this->response->error_404();
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

	private function check_method_exists($func, $method) {
		if(method_exists($func, $method_name = ($method . '_' . strtolower($this->apps->request->get_request_verb())))) return $method_name;
		else if(method_exists($func, $method)) return $method;
		return false;
	}

}
