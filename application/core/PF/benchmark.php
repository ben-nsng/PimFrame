<?php

class PF_Benchmark {

	private $stime = null;
	private $etime = null;
	private $apps = null;

	public function __construct($apps) {
		$this->apps = $apps;
	}

	public function load() {
		$this->stime = microtime(true);
	}

	public function unload() {
		$this->etime = microtime(true);

		$this->apps->response->add_parser(function(&$body) {
			$body = preg_replace('/\$runtime/', $this->etime - $this->stime, $body);
		});
	}

}
