<?php

class IoC_Upload {

	public function __construct() {
	}

	public function file($name) {
		$stmt = new IoC_Upload_File;
		return $stmt->set_file($name);
	}
}

class IoC_Upload_File {

	private $name;
	private $handle;

	public function __construct() {
		$this->handle == null;
	}

	public function set_file($name) {
		$this->name = $name;
		if(isset($_FILES[$this->name]))
			$this->handle = $_FILES[$this->name];
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
		var_dump($this->handle);
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



}

?>