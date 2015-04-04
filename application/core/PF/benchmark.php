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

		$etime = $this->etime;
		$stime = $this->stime;

		$response->add_parser(function(&$body) use($etime, $stime) {
			$body = preg_replace('/\$runtime/', $etime - $stime, $body);
		});
	}

}
