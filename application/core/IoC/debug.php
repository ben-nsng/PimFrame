<?php

class IoC_Debug {

	private $c;

	public function __construct($container) {
		$this->c = $container;
	}

	public function trace() {
		
		$traces = debug_backtrace();

		foreach($traces as $trace) {
			echo 'file: '. $trace['file'] . ' | line:' . $trace['line'] . ' | 	function: ' . $trace['function'] . "<br />\n";
		}
		echo "-----\n";

	}

}