<?php

class IoC_Database {

	private $conn_str;
	private $usr;
	private $pass;
	private $pdo;

	public function __construct($config) {
		$this->conn_str = $config['dbdriver'] . ':host=' . $config['hostname'] . ';dbname=' . $config['database'];
		$this->usr = $config['username'];
		$this->pass = $config['password'];
	}

	public function load() {
		if(!isset($this->pdo))
			$this->pdo = new PDO($this->conn_str, $this->usr, $this->pass);
	}

	public function get_pdo() {
		return $this->pdo;
	}

	public function onetomany($one, $many) {
		$stmt = $this->execute("SELECT GROUP_CONCAT(CONCAT('${many}.', column_name) SEPARATOR ',\\'$$]]//\\',') AS columns FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='${many}'");
		$result = $stmt->result();
		$columns = $result[0]->columns;
		$stmt = $this->execute("SELECT ${one}.*,GROUP_CONCAT(CONCAT($columns) SEPARATOR '$$$]]]///') AS ${many}_columns FROM $one INNER JOIN $many ON ${many}.${one}_id=${one}.id GROUP BY ${many}.${one}_id");
		$result = $stmt->result();

		$relation = array();
		$celltitles = explode(",'$$]]//',", $columns);
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

    public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
        // else throw exception
    }
}

class IoC_Database_Statement {

	private $db;
	private $stmt;

	public function __construct($db) {
		$this->db = $db;
	}

	public function query($sql, $placeholders = array()) {
		$pdo = $this->db->get_pdo();
		$this->stmt = $pdo->prepare($sql);
		$this->stmt->execute($placeholders);

		return $this;
	}

	public function result() {
		if($this->stmt != null) return $this->stmt->fetchAll(PDO::FETCH_OBJ);
		return array();
	}

	public function num_rows() {
		if($this->stmt != null) return $this->stmt->rowCount();
		return 0;
	}

}