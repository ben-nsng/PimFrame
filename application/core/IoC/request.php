<?php

class IoC_Request {

	public $url_elements;
	public $verb;
	private $gets = array();
	private $posts = array();
	private $parsed;

	public function __construct() {
		$this->verb = $_SERVER['REQUEST_METHOD'];
		$this->parsed = false;

		$this->parseIncomingParams();
		// initialise json as default format
		$this->format = 'json';
		if(isset($this->parameters['format'])) {
			$this->format = $this->parameters['format'];
		}
	}

	public function set_pathinfo($path_info = '') {
		if(!isset($_SERVER['PATH_INFO']) && $path_info == '') return;
		if(isset($_SERVER['PATH_INFO']))
			$this->url_elements = explode('/', $_SERVER['PATH_INFO']);
		else if($path_info != '')
			$this->url_elements = explode('/', $path_info);
	}

	public function get($key = '') {
		if($key == '') return $this->gets;
		if(!isset($this->gets[$key])) return false;
		return $this->gets[$key];
	}

	public function post($key = '') {
		if($key == '') return $this->posts;
		if(!isset($this->posts[$key])) return false;
		return $this->posts[$key];
	}

	private function parseIncomingParams() {
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
				break;
		}
		$this->posts = $parameters;
	}

}