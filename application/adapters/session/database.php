<?php

class Database_Session extends PF_Session {

	private $pf_id = null;
	private $cookies = array();
	private $flash_cookies = array();
	private $need_insert = false;
	private $need_update = false;

	public function __construct() {
		parent::__construct();
	}

	public function load() {

		global $apps;
		$this->database = $apps->database;
		$this->config = $apps->config;

		if(isset($_COOKIE['pf_cookie'])) {
			$this->pf_id = $_COOKIE['pf_cookie'];

			//if cookie key length is not 128, reset it
			if(strlen($this->pf_id) != 128) $this->pf_id = $this->make_id();

			//check if the record exists, if not reset it
			$sql = 'SELECT id, cookie_value FROM pf_session WHERE id=? AND user_agent=? AND ip_addr=?';
			$query = $this->database->execute($sql, array($this->pf_id, $this->get_ua(), $this->get_ip()));
			if($query->num_rows() == 0) $this->pf_id = $this->make_id();
			else {
				$result = $query->result();
				$this->cookies = json_decode($result[0]->cookie_value, true);

				if(isset($this->cookies[$this->flash_key])) {
					$this->flash_cookies = $this->cookies[$this->flash_key];
					$this->remove($this->flash_key);
				}
			}

		}
		else {
			$this->pf_id = $this->make_id();
		}
	}

	public function set($key, $val) {
		$this->need_update = true;
		$this->cookies[$key] = $val;
	}

	public function get($key) {
		if(isset($this->flash_cookies[$key])) return $this->flash_cookies[$key];
		if(isset($this->cookies[$key])) return $this->cookies[$key];
		return false;
	}

	public function remove($key) {
		$this->need_update = true;
		unset($this->cookies[$key]);
	}

	public function flash($key, $val) {
		$this->need_update = true;

		if(isset($this->cookies[$this->flash_key])) {
			$this->cookies[$this->flash_key][$key] = $val;
		}
		else {
			$this->cookies[$this->flash_key] = array(
				$key => $val
				);
		}
	}

	public function __destruct() {
		if($this->need_insert) {
			$this->database->execute('INSERT INTO pf_session(id, user_agent, ip_addr, cookie_value) VALUES(?, ?, ?, ?)', 
				array($this->pf_id, $this->get_ua(), $this->get_ip(), json_encode($this->cookies)));
		}
		else if($this->need_update) {
			$this->database->execute('UPDATE pf_session SET cookie_value=? WHERE id=? AND user_agent=? AND ip_addr=?',
				array(json_encode($this->cookies), $this->pf_id, $this->get_ua(), $this->get_ip()));
		}
	}

	private function get_ua() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	private function get_ip() {
		return $_SERVER['REMOTE_ADDR'];
	}

	private function make_id() {
		$crypt = $this->config->get('crypt');

		do {
			$id = hash('sha512', $crypt['salt'] . microtime());
		} while($this->database->execute('SELECT 1 FROM pf_session WHERE id=?', $id)->num_rows() != 0);

		$this->need_insert = true;
		setcookie('pf_cookie', $id, time() + (86400 * 30), '/');

		return $id;
	}

}

?>
