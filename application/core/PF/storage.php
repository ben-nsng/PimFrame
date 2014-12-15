<?php

class PF_Storage {

	private $last_uploaded_file_from = '';
	private $last_uploaded_file_to = '';

	public function __construct() {
	}

	public function load_file($path = '/', $file_name = '') {
		$path = $this->get_storage_path($path);
		if(file_exists($path . $file_name)) return file_get_contents($path . $file_name);
		return null;
	}

	public function save_raw($path = '/', $file_name = '', $raw) {
		$path = $this->get_storage_path($path);
		if($file_name == '') while(file_exists($path . ($file_name = uniqid('', true))));
		if(file_put_contents($path . $file_name, $raw) !== false)
			return $file_name;
		return null;
	}

	public function save($path = '/', $old_file_name, $new_file_name) {
		if(!file_exists($old_file_name)) return null;
		//$path = $this->get_storage_path($path);
		if(!is_dir($path))
			mkdir($path, 0777, true);


		return rename($old_file_name, $path . $new_file_name);
	}

	public function save_uploaded_file($path = '/', $old_file_name, $new_file_name) {
		if(!file_exists($old_file_name)) {
			//if save twice, copy instead
			if($this->last_uploaded_file_from == $old_file_name && file_exists($this->last_uploaded_file_to)) {
				if(!is_dir($path))
					mkdir($path, 0777, true);
				copy($this->last_uploaded_file_to, $path . $new_file_name);
			}
			else
				return null;
		}

		if(!is_dir($path))
			mkdir($path, 0777, true);

		$this->last_uploaded_file_from = $old_file_name;
		$this->last_uploaded_file_to = $path . $new_file_name;
		return move_uploaded_file($this->last_uploaded_file_from, $this->last_uploaded_file_to);
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