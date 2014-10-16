<?php

class IoC_Request {

	public $url_elements;
	public $verb;
	private $verbs = array('get', 'post', 'put', 'delete');
	private $parsed;

	public function __construct() {
		// initialize params array
		$this->clear();

		// get verb
		if(isset($_SERVER['REQUEST_METHOD']))
			$this->verb = strtolower($_SERVER['REQUEST_METHOD']);

		// set flag
		$this->parsed = false;

		// parse params
		$this->parse_incoming_params();

		// initialise json as default format
		$this->format = 'json';
		if(isset($this->parameters['format'])) {
			$this->format = $this->parameters['format'];
		}
	}

	public function clear() {
		foreach($this->verbs as $verb) {
			$verb .= 's';
			$this->$verb = array();
		}
	}

	public function set_verb($verb) {
		$this->verb = strtolower($verb);
	}

	//setter
	private function _set_verb($verb, $key, $val) {
		$verb .= 's';
		$this->{$verb}[$key] = $val;
	}

	//getter
	private function _verb($verb, $key = '') {
		$verb .= 's';
		if($key == '') return $this->$verb;
		$verb_arr = $this->$verb;
		if(!isset($verb_arr[$key])) return false;
		return $verb_arr[$key];
	}

	public function __call($method_name, $args) {
		$method_name = strtolower($method_name);
		if(in_array($method_name, $this->verbs)) {
			array_unshift($args, $method_name);
			return call_user_func_array(array($this, '_verb'), $args);
		}
		else {
			$parts = explode('_', $method_name);
			if(count($parts) == 2 && $parts[0] == 'set' && in_array($parts[1], $this->verbs)) {
				array_unshift($args, $parts[1]);
				call_user_func_array(array($this, '_set_verb'), $args);
			}
		}
	}

	public function set_pathinfo($path_info = '') {
		if(!isset($_SERVER['PATH_INFO']) && $path_info == '') return;
		if(isset($_SERVER['PATH_INFO']))
			$this->url_elements = explode('/', $_SERVER['PATH_INFO']);
		else if($path_info != '')
			$this->url_elements = explode('/', $path_info);
	}

	private function parse_incoming_params() {
		$parameters = array();
 
		// first of all, pull the GET vars
		if (isset($_SERVER['QUERY_STRING'])) {
			parse_str($_SERVER['QUERY_STRING'], $parameters);
		}
		$this->gets = $parameters;

		$parameters = array();
 
		// now how about PUT/POST bodies? These override what we got from GET
		$body = file_get_contents("php://input");
		$content_type = false;
		if(isset($_SERVER['CONTENT_TYPE'])) {
			$content_type = $_SERVER['CONTENT_TYPE'];
		}
		switch($content_type) {
			case "application/json":
				$body_params = json_decode($body);
				if($body_params) {
					foreach($body_params as $param_name => $param_value) {
						$parameters[$param_name] = $param_value;
					}
				}
				$this->format = "json";
				break;
			case "application/x-www-form-urlencoded":
				parse_str($body, $postvars);
				foreach($postvars as $field => $value) {
					$parameters[$field] = $value;
				}
				$this->format = "html";
				break;
			default:
				// we could parse other supported formats here
				if(stripos($content_type, 'multipart/form-data') === 0) {
					$this->gets = $_GET;
					$this->posts = $_POST;
					return;
				}
				break;
		}

		if($this->verb != 'get') {
			$verb = $this->verb . 's';
			$this->$verb = $parameters;
		}
	}

}
