<?php

class m1_Test extends PF_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('m1_TestModel');
	}

	public function index() {
		echo 'm1_test';
		echo $this->m1_TestModel->data();
	}
	
}
