<?php

class IoC_Database {

	private $conn_str;
	private $usr;
	private $pass;
	private $pdo;
	private $is_trans;
	private $trans_err;
	private $trans_count;
	private $sql_paginate;

	public function __construct($config) {
		$this->conn_str = $config['dbdriver'] . ':host=' . $config['hostname'] . ';dbname=' . $config['database'];
		$this->usr = $config['username'];
		$this->pass = $config['password'];

		// transaction flag
		$this->is_trans = false;
		$this->trans_err = false;
		$this->trans_count = 0;
	}

	public function __destruct() {
		if($this->trans_err) {
			//while(!$this->trans_end());
		}
	}

	public function load() {
		if(!isset($this->pdo)) {
			$this->pdo = new PDO($this->conn_str, $this->usr, $this->pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->execute('SET NAMES UTF8');
			$this->execute('SET SESSION group_concat_max_len = 100000');
			$this->execute('SET time_zone = "+08:00";');
		}
	}

	public function quote($str) {
		return $this->pdo->quote($str);
	}

	public function paginate() {
		$page = intval($this->request->get('page'));
		$limit = intval($this->request->get('limit'));
		
		if($page && $limit)
			$this->sql_paginate = ' LIMIT ' . $limit * ($page - 1) . ', ' . $limit;
		return $this;
	}

	//**
	//** smart transaction engine
	//** rollback will be called automatically
	//** use trans_start and trans_end if the scope need transaction
	//**

	public function trans_start() {
		//not allow if the transaction is started
		$this->trans_count++;
		if($this->is_trans) return;

		//set the flag to begin transaction
		$this->is_trans = true;
		$this->pdo->beginTransaction();
	}

	public function trans_end() {
		//not allow if the scope is not in transaction
		if(!$this->is_trans) return true;
		if(--$this->trans_count == 0) {
			if($this->trans_err) {
				//if rollback was called, rollback the transaction and reset flag
				$this->pdo->rollback();
				$this->is_trans = false;
				$this->trans_err = false;
				$this->trans_count = 0;
				return false;
			}
		
			//no transaction scope, commit the transaction
			$this->is_trans = false;
			return $this->pdo->commit();
		}
		return true;
	}

	public function rollback() {
		//not allow if the scope is not in transaction
		if(!$this->is_trans) return;
		//transaction has error, rollback transaction in next trans_end
		$this->trans_err = true;
	}

	public function last_insert_id() {
		return $this->pdo->lastInsertId();
	}

	public function get_pdo() {
		return $this->pdo;
	}

	public function onetomany($one, $many, $where = '') {
		if($where != '') $where = 'WHERE ' . $where;

		//select column name and column name for next query
		$stmt = $this->execute("SELECT
			GROUP_CONCAT(CONCAT('${many}.', column_name) SEPARATOR ',') AS raw_columns,
			GROUP_CONCAT(CONCAT('IF(${many}.', column_name, ' IS NULL, \\'\\', ${many}.', column_name, ')') SEPARATOR ',\\'$$]]//\\',') AS columns
			FROM information_schema.columns
			WHERE table_schema=DATABASE() AND table_name='${many}'");

		$result = $stmt->result();
		$celltitles = explode(',', $result[0]->raw_columns);

		//query for one to many relation
		$columns = $result[0]->columns;
		$stmt = $this->execute("SELECT
			${one}.*,GROUP_CONCAT(CONCAT($columns) SEPARATOR '$$$]]]///') AS ${many}_columns
			FROM $one
			INNER JOIN $many ON ${many}.${one}_id=${one}.id
			$where
			GROUP BY ${many}.${one}_id");
		$result = $stmt->result();

		//build tree
		$relation = array();
		foreach($result as $id => $row) {
			foreach($row as $key => $val) {
				if($key == $many . '_columns') {
					$relation[$id][$many] = array();
					
					$rows = explode('$$$]]]///', $val);
					foreach($rows as $cells) {
						$cell = explode('$$]]//', $cells);
						for($i = 0; $i < count($cell); $i++)
							if($i == 0) $relation[$id][$many][] = array(substr($celltitles[$i], strlen($many) + 1) => $cell[$i]);
							else $relation[$id][$many][count($relation[$id][$many]) - 1][substr($celltitles[$i], strlen($many) + 1)] = $cell[$i];
					}
					
				}
				else
					$relation[$id][$key] = $val;
			}
		}
		return array($one => $relation);
	}

	public function execute($sql, $placeholders = array()) {
		try {
			if($this->is_trans && $this->trans_err) return null;
			return $GLOBALS['container']['database_statement']->query($sql . $this->sql_paginate, $placeholders);
		}
		catch (PDOException $e) {
			$this->debug->trace();
			var_dump($e);
			var_dump($sql);
			var_dump($placeholders);
			$this->rollback();
			return null;
		}
		finally {
			$this->sql_paginate = '';
		}
	}

}

class IoC_Database_Statement {

	private $db;
	private $stmt;
	private $result;
	private $result_array;

	public function __construct($db) {
		$this->db = $db;
	}

	public function query($sql, $placeholders = array()) {
		$pdo = $this->db->get_pdo();

		if(count($placeholders) == 0)
			$this->stmt = $pdo->query($sql);
		else {
			$this->stmt = $pdo->prepare($sql);
			$this->stmt->execute($placeholders);
		}

		return $this;
	}

	public function result() {
		if($this->result != null) return $this->result;
		if($this->stmt != null) {
			$this->result = $this->stmt->fetchAll(PDO::FETCH_OBJ);
			return $this->result;
		}
		return array();
	}

	public function result_array() {
		if($this->result_array != null) return $this->result_array;
		if($this->stmt != null) {
			$this->result_array = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			return $this->result_array;
		}
		return array();
	}

	public function num_rows() {
		if($this->stmt != null) return $this->stmt->rowCount();
		return 0;
	}

}
