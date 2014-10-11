<?php

class IoC_Debug {

	public function __construct() {
	}

	public function trace() {
		
		$traces = debug_backtrace();

		foreach($traces as $trace) {
			echo 'file: '. $trace['file'] . ' | line:' . $trace['line'] . ' | 	function: ' . $trace['function'] . "<br />\n";
		}
		echo "-----\n";

	}

}