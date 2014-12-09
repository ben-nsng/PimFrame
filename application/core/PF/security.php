<?php

class PF_Security {
	
	private $salt;
	private $key;
	private $iv;

	public function __construct($config) {
		$this->salt = $config['salt'];
		$this->key = $config['aeskey'];
	}

	public function encrypt($message, $useiv = false) {
		$td = mcrypt_module_open('rijndael-256', '', 'cbc', '');

		$ks = mcrypt_enc_get_key_size($td);
		
		if($useiv) {
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		}
		else {
			$iv = $this->nulliv($ks);
		}
		$this->iv = $iv;

		$key = substr($this->key, 0, $ks);

		mcrypt_generic_init($td, $key, $iv);

		$encrypted = mcrypt_generic($td, $message);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return base64_encode($encrypted);
	}

	public function decrypt($message, $iv = '') {
		$td = mcrypt_module_open('rijndael-256', '', 'cbc', '');

		$ks = mcrypt_enc_get_key_size($td);
		
		if($iv == '') $iv = $this->nulliv($ks);

		mcrypt_generic_init($td, $this->key, $iv);

		$message = mdecrypt_generic($td, base64_decode($message));

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $message;
	}

	public function hash($message) {
		return hash('sha512', $this->salt . $message);
	}

	public function iv() {
		return $this->iv;
	}

	private function nulliv($size) {
		$iv = '';
		for($i = 0; $i < $size; $i++)
			$iv .= '0';
		return $iv;
	}

}

?>
