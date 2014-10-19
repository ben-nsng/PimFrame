<?php

class IoC_Debug {

	private $message;
	private $debug = true;

	public function __construct() {
	}

	public function trace() {
		
		if($this->debug) {
			$traces = debug_backtrace();

			foreach($traces as $trace) {
				$this->message .= 'file: '. $trace['file'] . ' | line:' . $trace['line'] . ' | 	function: ' . $trace['function'] . "\n";
			}
			$this->message .= "-----\n";
		}

	}

	public function log($message) {
		$this->message .= var_export($message, true);
		$this->message = nl2br($this->message);
	}

	public function get_message() {
		return $this->message;
	}

}