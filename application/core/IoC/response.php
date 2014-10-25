<?php

class IoC_Response {

	private $parsers;
	private $messages;
	private $is_error_404;
	private $is_redirect;

	public function __construct() {
		$this->parsers = array();
		$this->messages = array();
		$this->is_error_404 = false;
		$this->is_redirect = false;
		$this->add_parser(function($body) {
			if(IS_RESTFUL_CALL) {
				if($this->is_error_404) return json_encode(array('error' => 'The page does not exist!'));
				if($body === NULL) return json_encode(array());
				else return json_encode($body);
			}
			return $body;
		});
	}

	public function error_404() {
		$this->is_error_404 = true;
	}

	public function redirect($page) {
		header('location: ' . $page);
		$this->is_redirect = true;
		exit;
	}

	public function add_parser($parser) {
		$this->parsers[] = $parser;
	}

	public function parse($body) {
		if($this->is_redirect) return "";
		
		foreach($this->parsers as $parser)
			$body = $parser($body);
		return $body;
	}

	public function message($key, $message = '') {
		if($message == '') return $this->messages[$key];
		else $this->messages[$key] = $message;
	}

	public function get_last_message() {
		if(count($this->messages) > 0) return $this->messages[count($this->messages) - 1];
		return null;
	}

}
