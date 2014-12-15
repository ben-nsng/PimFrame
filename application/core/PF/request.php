<?php

class PF_Request {

	private $url_elements;
	private $verb;
	private $verbs = array('get', 'post', 'put', 'delete');
	private $parsed;
	private $apps = null;

	public function __construct($apps) {
		$this->apps = $apps;

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

	// ** GET POST PUT DELETE ** //

	public function clear() {
		foreach($this->verbs as $verb) {
			$verb .= 's';
			$this->$verb = array();
		}
	}

	public function get_request_verb() {
		return $this->verb;
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

	// ** PATH INFO ROUTING ** //

	public function set_pathinfo($path_info = '') {
		if($path_info != '')
			$this->url_elements = explode('/', $path_info);
		else if(isset($_SERVER['PATH_INFO']))
			$this->url_elements = explode('/', $_SERVER['PATH_INFO']);
		else if(IS_PATH_REWRITE) {
			$this->rewrite_pathinfo();
			$this->set_pathinfo();
			return;
		}
		array_shift($this->url_elements);
	}

	public function rewrite_pathinfo() {
		if(!isset($_SERVER['PATH_INFO'])) {
			$_SERVER['PATH_INFO'] = $_SERVER['QUERY_STRING'];
			if(($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
				$_SERVER['QUERY_STRING'] = substr($_SERVER['REQUEST_URI'], $pos + 1);

				$parameters = array();
				parse_str($_SERVER['QUERY_STRING'], $parameters);
				$this->gets = $parameters;
			}
		}
	}

	private function parse_incoming_params() {
		if(IS_RESTFUL_CALL) {
			$this->rewrite_pathinfo();
		}

		$parameters = array();
 
		// first of all, pull the GET vars
		if (isset($_SERVER['QUERY_STRING'])) {
			parse_str($_SERVER['QUERY_STRING'], $parameters);

		}
		$this->gets = $parameters;

		$parameters = array();
 
		$body = file_get_contents("php://input");
		$content_type = false;
		if(isset($_SERVER['CONTENT_TYPE'])) {
			$content_type = $_SERVER['CONTENT_TYPE'];
		}

		if(stripos($content_type, 'application/json') === 0) {
			$body_params = json_decode($body);
			if($body_params) {
				foreach($body_params as $param_name => $param_value) {
					$parameters[$param_name] = $param_value;
				}
			}
			$this->format = "json";
		}
		else if(stripos($content_type, 'application/x-www-form-urlencoded') === 0) {
			parse_str($body, $postvars);
			foreach($postvars as $field => $value) {
				$parameters[$field] = $value;
			}
			$this->format = "html";
		}
		else if(stripos($content_type, 'multipart/form-data') === 0) {
			if(!IS_RESTFUL_CALL) {
				$this->gets = $_GET;
			}
				$this->posts = $_POST;
			return;
		}

		if($this->verb != 'get') {
			$verb = $this->verb . 's';
			$this->$verb = $parameters;
		}
	}

	public function get_url_elements() {
		return $this->url_elements;
	}

}
