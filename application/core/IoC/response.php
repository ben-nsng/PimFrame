<?php

class IoC_Response {

	private $parsers;
	private $messages;
	private $is_error_404;

	public function __construct() {
		$this->parsers = array();
		$this->messages = array();
		$this->is_error_404 = false;
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
		exit;
	}

	public function add_parser($parser) {
		$this->parsers[] = $parser;
	}

	public function parse($body) {
		foreach($this->parsers as $parser)
			$body = $parser($body);
		return $body;
	}

	public function message($message) {
		$this->messages[] = $message;
	}

	public function get_last_message() {
		if(count($this->messages) > 0) return $this->messages[count($this->messages) - 1];
		return null;
	}

}