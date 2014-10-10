<?php

class IoC_Service {

	private $c;
	private $services = array();

	public function __construct($container) {
		$this->c = $container;
	}

	public function add_service($new_service) {
		if(is_string($new_service)) {
			if(in_array($new_service, $this->services)) return;
			foreach($this->services as $service) {
				//register new service to existing service
				$this->c[$service]->$new_service = $this->c[$new_service];
				//register existing service to new service
				$this->c[$new_service]->$service = $this->c[$service];
			}
			$this->services[] = $new_service;
		}
	}

}