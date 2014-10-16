<?php

class IoC_Storage {

	public function __construct() {
	}

	public function load($path = '/', $file_name = '') {
		$path = $this->get_storage_path($path);
		if(file_exists($path . $file_name)) return file_get_contents($path . $file_name);
		return null;
	}

	public function save_raw($path = '/', $raw) {
		$path = $this->get_storage_path($path);
		while(file_exists($path . ($file_name = uniqid('', true))));
		if(file_put_contents($path . $file_name, $raw) !== false)
			return $file_name;
		return null;
	}

	public function save($path = '/', $old_file_name, $new_file_name) {
		if(!file_exists($old_file_name)) return null;
		//$path = $this->get_storage_path($path);
		mkdir($path, 0777, true);
		return rename($old_file_name, $path . $new_file_name);
	}

	/*
	private function get_storage_path($path = '/') {
		if(substr($path, 0, 1) != '/') $path = '/' . $path;
		if(substr($path, -1) != '/') $path .= '/';
		if($path == '/')
			return BASE_DIR . '/application/storage' . $path;
		else
			return $path;
	}
	*/
}

?>