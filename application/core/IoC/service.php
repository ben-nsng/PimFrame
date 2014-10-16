<?php

class IoC_Service {

	private $c;
	private $services = array();
	private $models = array();
	private $controllers = array();

	// service - service
	//
	// controller - service, model
	//
	// model - service, model

	public function __construct($container) {
		$this->c = $container;
	}

	public function add_service($new_service) {
		if(in_array($new_service, $this->services)) return;
		foreach($this->services as $service) {
			//register existing service to new service
			$this->c[$new_service]->$service = $this->c[$service];
		}
		$request_services = array_merge($this->services, $this->models, $this->controllers);
		foreach($request_services as $service) {
			//register new service to existing service
			$this->c[$service]->$new_service = $this->c[$new_service];
		}
		$this->services[] = $new_service;
	}

	public function add_controller($new_controller) {
		if(in_array($new_controller, $this->controllers)) return;
		$request_services = array_merge($this->services, $this->models);
		foreach($request_services as $service) {
			//register existing service to new service
			$this->c[$new_controller]->$service = $this->c[$service];
		}
		$this->controllers[] = $new_controller;
	}

	public function add_model($new_model) {
		if(in_array($new_model, $this->models)) return;
		$request_services = array_merge($this->services, $this->models);
		foreach($request_services as $service) {
			//register existing service to new service
			$this->c[$new_model]->$service = $this->c[$service];
		}
		$request_services = array_merge($this->controllers, $this->models);
		foreach($request_services as $service) {
			//register new service to existing service
			$this->c[$service]->$new_model = $this->c[$new_model];
		}
		$this->models[] = $new_model;
	}

}