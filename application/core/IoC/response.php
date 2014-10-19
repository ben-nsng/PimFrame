<?php

class IoC_Response {

	private $parsers;

	public function __construct() {
		$this->parsers = array();
	}

	public function error_404() {
		$json = array('error' => 'The page does not exist!');
		return json_encode($json);
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
}