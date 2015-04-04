<?php

abstract class PF_Entry extends PF_Model {

	abstract protected function pk();
	abstract protected function table();
	abstract protected function columns();
	abstract protected function created_at();
	abstract protected function updated_at();
	abstract protected function deleted_at();

	private $where = null;
	private $where_args = array();
	private $join = null;

	public function __construct() {
		parent::__construct();
	}

	protected function join($join) {
		$this->join = $join;
		return $this;
	}

	protected function where($where, $args = array()) {
		$this->where = $where;
		$this->where_args = $args;
		return $this;
	}

	protected function data_check($posts, $self_defined_columns = array()) {
		$keys = array_keys($posts);

		if(count($self_defined_columns) == 0)
			$columns = $this->columns();
		else
			$columns = $self_defined_columns;

		foreach($keys as $column)
			if(!in_array($column, $columns) && $column != $this->pk()) {
				// var_dump($column);
				// exit;
				return false;
			}
		return true;
	}

	protected function trans_start() {
		$this->database->trans_start();
	}

	protected function trans_end() {
		return $this->database->trans_end();
	}

	protected function create($vals) {

		//INSERT STATEMENT
		$sql = 'INSERT INTO ' . $this->table();

		//COMPOSE COLUMNS
		$stmt_columns = '(';

		//$sql .= '(`' . implode('`,`', $this->columns()) . '`' . ($this->created_at() ? ',`created_at`' : '') . ($this->updated_at() ? ',`updated_at`' : '') . ')';

		//COMPOSE VALUES
		$stmt_vals = 'VALUES(';

		//$sql .= ' VALUES(' . substr(str_repeat('?, ', count($this->columns())), 0, -2) . ($this->created_at() ? ', NOW()' : '') . ($this->updated_at() ? ', NOW()' : '') . ')';

		//COMPOSE ARGS
		$args = array();
		foreach($this->columns() as $column) {
			if(isset($vals[$column])) {
				$stmt_columns .= '`' . $this->table() . '`.`' . $column . '`, ';
				$stmt_vals .= '?, ';
				$args[] = $vals[$column];
			}
		}

		if($this->created_at()) {
			$stmt_columns .= '`' . $this->table() . '`.`created_at`, ';
			$stmt_vals .= 'NOW(), ';
		}

		if($this->updated_at()) {
			$stmt_columns .= '`' . $this->table() . '`.`updated_at`, ';
			$stmt_vals .= 'NOW(), ';
		}

		$sql .= substr($stmt_columns, 0, -2) . ') ' . substr($stmt_vals, 0, -2) . ') ';

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $args);
	}

	protected function read($id = NULL, $self_defined_columns = array(), PF_Paginator $paginator = null) {
		if(count($self_defined_columns) == 0)
			$txt_columns = ' `' . implode('`, `', $this->columns()) . '`';
		else
			$txt_columns = ' ' . implode(',', $self_defined_columns);

		//SELECT STATEMENT
		$sql = 'SELECT' . ($paginator !== NULL ? ' SQL_CALC_FOUND_ROWS' : '') . $txt_columns;// . ', `' . $this->table() . '`.`' . $this->pk() . '`';
		// if($this->created_at())
		// 	$sql .= ', `' . $this->table() . '`.`created_at`';
		// if($this->updated_at())
		// 	$sql .= ', `' . $this->table() . '`.`updated_at`';
		// if($this->deleted_at())
		// 	$sql .= ', `deleted_at`';

		$sql .= ' FROM ' . $this->table();
		if($this->join !== NULL) $sql .= ' ' . $this->join;

		// if($id === NULL && $this->where === NULL) {

		// 	if($this->deleted_at()) $sql .= ' WHERE ' . $this->table() . '.deleted_at IS NULL';
		// 	if($this->updated_at()) $sql .= ' ORDER BY updated_at DESC';
		// 	return $this->database->execute($sql);
		// }
		
		if($this->deleted_at())
			$sql .= ' WHERE ' . $this->table() . '.deleted_at IS NULL';
		else
			$sql .= ' WHERE 1=1';

		$args = array();

		if($id !== NULL) {
			$sql .= ' AND ' . $this->table() . '.id=?';
			$args[] = $id;
			if($this->where !== NULL) $sql .= ' AND';
		}

		if($this->where !== NULL) {
			$sql .= ' AND ' . $this->where;
			$args = array_merge($args, $this->where_args);
		}

		// if($this->updated_at()) $sql .= ' ORDER BY updated_at DESC';
		if(method_exists($this, 'order_by'))
			$sql .= ' ' . $this->order_by();
			
		if($paginator !== NULL) {
			$query = $this->database->paginate($paginator->page(), $paginator->limit())->execute($sql, $args);
			$paginator->set_total(
					$this->database->execute('SELECT FOUND_ROWS() AS rows')->first()->rows
			);
			return $query;
		}
		else
			return $this->database->execute($sql, $args);
	}

	protected function update($id = NULL, $vals, $others = array()) {

		//UPDATE STATEMENT
		$sql = 'UPDATE ' . $this->table() . ' SET ';
		//COMPOSE COLUMNS AND VALUES
		

		//COMPOSE ARGS
		$args = array();
		foreach($this->columns() as $column)
			if(isset($vals[$column])) {
				$sql .= $this->table() . '.' . $column . '=?, ';
				$args[] = $vals[$column];
			}

		foreach($others as $col => $val) {
			$sql .= $this->table() . '.' . $col . '=?, ';
			$args[] = $val;
		}

		$sql = substr($sql, 0, -2);

		if($this->updated_at())
			$sql .= ',' . $this->table() . '.`updated_at`=NOW()';

		$sql .= ' WHERE';
		
		//COMPOSE WHERE
		if($id !== NULL) {
			$sql .= ' id=?';
			$args[] = $id;
			if($this->where !== NULL) $sql .= ' AND';
		}

		if($this->where !== NULL) {
			$sql .= ' ' . $this->where;
			$args = array_merge($args, $this->where_args);
		}

		// var_dump($sql);
		// var_dump($args);
		// exit;

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $args);
	}

	protected function delete($id = NULL) {

		//DELETE STATEMENT
		if($this->deleted_at())
			//SOFT DELETE IF deleted_at exists
			$sql = 'UPDATE ' . $this->table() . ' SET `deleted_at`=NOW() WHERE `' . $this->pk() . '`=?';
		else
			//HARD DELETE IF deleted_at not exists
			$sql = 'DELETE FROM ' . $this->table() . ' WHERE `' . $this->pk() . '`=?';

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $id);
	}


}

?>
