<?php

class HomeController extends PF_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('HomeModel');
	}

	public function index() {
		$data['data'] = $this->HomeModel->data();
		$this->load->view("home");
	}
	
}
