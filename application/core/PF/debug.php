<?php

class PF_Debug {

	private $message;

	public function __construct() {
	}

	public function trace() {
		
		$traces = debug_backtrace();

		foreach($traces as $trace) {
			$this->message .= 'file: '. (isset($trace['file']) ? $trace['file'] : '') . ' | line:' . (isset($trace['line']) ? $trace['line'] : '') . ' | 	function: ' . $trace['function'] . "\n";
		}
		$this->message .= "---end trace---\n";
	}

	public function log($message, $end = false) {
		$this->message .= nl2br(var_export($message, true));
		$this->message .= nl2br("\n");
		if($end)
			$this->message .= "\n---end log---\n";
	}

	public function get_message() {
		return nl2br($this->message);
	}

}
