<?php

class PF_Upload {

	public function __construct() {
	}

	public function file($name) {
		$stmt = new PF_Upload_File;
		return $stmt->set_file($name);
	}

	//html5 for reading multiple uploaded file
	public function files($name) {
		$files = array();
		if(isset($_FILES[$name]))
			for($i = 0; $i < count($_FILES[$name]['name']); $i++) {
				$file = new PF_Upload_File;
				$file->set_file($name, $i);
				$files[] = $file;
			}
		return $files;
	}
}

class PF_Upload_File {

	private $name;
	private $handle;

	public function __construct() {
		$this->handle == null;
	}

	public function set_file($name, $i = -1) {
		$this->name = $name;
		if(isset($_FILES[$this->name])) {
			if($i == -1) $this->handle = $_FILES[$this->name];
			else $this->handle = array(
				'name' => $_FILES[$this->name]['name'][$i],
				'type' => $_FILES[$this->name]['type'][$i],
				'tmp_name' => $_FILES[$this->name]['tmp_name'][$i],
				'error' => $_FILES[$this->name]['error'][$i],
				'size' => $_FILES[$this->name]['size'][$i]
				);
		}
		else if(preg_match('/([a-zA-Z0-9]+)\[([a-zA-Z0-9]+)\]/', $name, $m) && isset($_FILES[$m[1]]['name'][$m[2]])) {
			$this->handle = array(
				'name' => $_FILES[$m[1]]['name'][$m[2]],
				'type' => $_FILES[$m[1]]['type'][$m[2]],
				'tmp_name' => $_FILES[$m[1]]['tmp_name'][$m[2]],
				'error' => $_FILES[$m[1]]['error'][$m[2]],
				'size' => $_FILES[$m[1]]['size'][$m[2]]
				);
		}
		return $this;
	}

	public function uploaded() {
		return isset($this->handle) && $this->handle['error'] == 0;
	}

	public function status() {
		if(!isset($this->handle)) return 'no_file';

		$error = $this->handle['error'];
		switch($error) {
			case UPLOAD_ERR_INI_SIZE:
				return 'size_limit';
			break;
			case UPLOAD_ERR_FORM_SIZE:
				return 'size_limit';
			break;
			case UPLOAD_ERR_PARTIAL:
				return 'partial_upload';
			break;
			case UPLOAD_ERR_NO_FILE:
				return 'no_file';
			break;
			default:
			break;
		}
		//var_dump($this->handle);
		return '';
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

	public function extension() {
		if(isset($this->handle)) {
			$name = $this->realname();
			return strtolower(substr($name, strrpos($name, '.') + 1));
		}
		return '';
	}



}

?>