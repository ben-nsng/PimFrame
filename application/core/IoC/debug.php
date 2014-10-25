<?php

class IoC_Debug {

	private $message;

	public function __construct() {
	}

	public function trace() {
		
		$traces = debug_backtrace();

		foreach($traces as $trace) {
			$this->message .= 'file: '. $trace['file'] . ' | line:' . $trace['line'] . ' | 	function: ' . $trace['function'] . "\n";
		}
		$this->message .= "---end trace---\n";
	}

	public function log($message) {
		$this->message .= var_export($message, true);
		$this->message .= "\n---end log---\n";
	}

	public function get_message() {
		return nl2br($this->message);
	}

}
