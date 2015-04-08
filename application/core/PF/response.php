<?php

class PF_Response {

	private $parsers;
	private $messages;
	private $is_error_404;
	private $is_redirect;
	private $is_post;
	private $html_post;


	public function __construct() {
		$this->parsers = array();
		$this->messages = array();
		$this->is_error_404 = false;
		$this->is_redirect = false;
		$this->is_post = false;
		$this->html_post = '';
	}

	public function load() {
		//create parser for debug and clean up
		global $apps;

		$self = $this;
		$this->add_parser(function(&$body) use($self, $apps) {
			if(ENVIRONMENT == 'development') {
				if(isset($apps->debug)) {
					$debug = $apps->debug;
					$body = preg_replace('/\$debug/', $debug->get_message(), $body);
				}
			}
			if(IS_RESTFUL_CALL) {
				if($self->is_error_404) $body = json_encode(array('error' => 'The page does not exist!'));
				if($body === NULL) $body = json_encode(array());
			}
		});

		ob_start(array($this, "output"));
	}

	// ** routing ** //

	public function error_404() {
		$this->is_error_404 = true;
	}

	//redirect with get or post request
	public function redirect($page, $posts = array()) {
		if($this->is_redirect) return;
		global $host_name;

		if(count($posts) == 0) {
			if(substr($page, 0, 1) == '/') $page = $host_name . substr($page, 1);
			header('location: ' . $page);
		}
		else {
			$this->is_post = true;
			$this->html_post = '<form name="frm" action="' . $page . '" method="POST">';
			foreach($posts as $key => $val)
				$this->html_post .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
			$this->html_post .= '</form><script>document.frm.submit();</script>';
		}
		$this->is_redirect = true;
		exit;
	}

	public function reroute($page) {
		GLOBAL $apps;
		return $apps->run($page);
	}

	// ** message ** //

	public function message($key, $message = '') {
		if($message == '') {
			if(isset($this->messages[$key]))
				return $this->messages[$key];
			else
				return false;
		}
		else $this->messages[$key] = $message;
	}

	public function get_last_message() {
		if(count($this->messages) > 0) return $this->messages[count($this->messages) - 1];
		return null;
	}

	// ** parser ** //

	public function add_parser($parser) {
		$this->parsers[] = $parser;
	}

	public function parse($body) {
		//call this line when redirect with post request
		if($this->is_post) return $this->html_post;
		//call this line when header redirect
		if($this->is_redirect) return "";
		//call this line when error 404
		if($this->is_error_404) return "";
		
		//otherwise, run parser for the body
		foreach($this->parsers as $parser)
			$parser($body);	//pass reference into parser
		return $body;
	}

	// ** ob buffering ** //

	public function unload() {
		ob_end_flush();
		if($this->is_error_404) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			echo "<h1>404 Not Found</h1>";
			echo "The page that you have requested could not be found.";
		}
	}

	private function output($buffer) {
		return $this->parse($buffer);
	}

}