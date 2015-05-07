<?php

class PF_File {

	public function __construct() {

	}

	public function upload($name) {
		return new PF_File_Handle($name);
	}

	public function uploads($name) {
		$files = array();

		$cnt = count($_FILES[$name]['name']);
		for($i = 0; $i < $cnt; $i++) {
			$files[] = new PF_File_Handle($name, 
				array(
					'name' => $_FILES[$name]['name'][$i],
					'type' => $_FILES[$name]['type'][$i],
					'tmp_name' => $_FILES[$name]['tmp_name'][$i],
					'error' => $_FILES[$name]['error'][$i],
					'size' => $_FILES[$name]['size'][$i]
				)
			);
		}

		return $files;
	}

}

class PF_File_Handle {

	private $name = null;
	private $handle = null;
	private $upload_path = './application/uploads/';

	public function __construct($name, $info = null) {
		$this->name = $name;

		if($info !== null) {
			$this->handle = $info;
			return;
		}
		
		if(isset($_FILES[$this->name])) {
			$this->handle = array(
				'name' => $_FILES[$this->name]['name'],
				'type' => $_FILES[$this->name]['type'],
				'tmp_name' => $_FILES[$this->name]['tmp_name'],
				'error' => $_FILES[$this->name]['error'],
				'size' => $_FILES[$this->name]['size']
				);
		}
	}

	public function is_uploaded() {
		return isset($this->handle) && $this->handle['error'] == 0;
	}

	public function random_save() {
		$file_name = $random_name = sha1_file($this->handle['tmp_name']);

		$i = 0;
		while(file_exists($this->upload_path . $file_name)) {
			$file_name = $random_name . '_' . (++$i);
		}
		move_uploaded_file($this->handle['tmp_name'], $this->upload_path . $file_name);
		return $file_name;
	}

	public function location() {
		if(isset($this->handle)) return $this->handle['tmp_name'];
		return '';
	}

	public function realname() {
		if(isset($this->handle)) return basename($this->handle['name']);
		return '';
	}

	public function size() {
		if(isset($this->handle)) return $this->handle['size'];
		return '';
	}

	public function type() {
		if(isset($this->handle)) return $this->handle['type'];
		return '';
	}

	public function extension() {
		if(isset($this->handle)) {
			$name = $this->realname();
			return strtolower(substr($name, strrpos($name, '.') + 1));
		}
		return '';
	}
}

?>
