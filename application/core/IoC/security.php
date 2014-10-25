<?php

class IoC_Security {
	
	private $salt;
	private $key;

	public function __construct($config) {
		$this->salt = $config['salt'];
		$this->key = $config['aeskey'];
	}

	public function encrypt($message) {
	}

	public function decrypt($message) {
	}

	public function hash($message) {
		return hash('sha512', $this->salt . $message);
	}

}
