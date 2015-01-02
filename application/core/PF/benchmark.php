<?php

class PF_Benchmark {

	private $stime = null;
	private $etime = null;

	public function __construct() {
	}

	public function load() {
		$this->stime = microtime(true);
	}

	public function unload($PF) {
		$this->etime = microtime(true);

		$response = $PF->response;

		$response->add_parser(function(&$body) {
			$body = preg_replace('/\$runtime/', $this->etime - $this->stime, $body);
		});
	}

}
