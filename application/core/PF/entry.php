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
			if(!in_array($column, $columns)) return false;
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
		$sql .= '(`' . implode('`,`', $this->columns()) . '`' . ($this->created_at() ? ',`created_at`' : '') . ')';

		//COMPOSE VALUES
		$sql .= ' VALUES(' . substr(str_repeat('?, ', count($this->columns())), 0, -2) . ($this->created_at() ? ', NOW()' : '') . ')';

		//COMPOSE ARGS
		$args = array();
		foreach($this->columns() as $column)
			$args[] = $vals[$column];

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $args);
	}

	protected function read($id = NULL, $self_defined_columns = array(), PF_Paginator $paginator = null) {
		if(count($self_defined_columns) == 0)
			$columns = $this->columns();
		else
			$columns = $self_defined_columns;

		//SELECT STATEMENT
		$sql = 'SELECT' . ($paginator !== NULL ? ' SQL_CALC_FOUND_ROWS' : '') . ' `' . implode('`, `', $columns) . '`, `' . $this->pk() . '`';
		if($this->created_at())
			$sql .= ', `created_at`';
		if($this->updated_at())
			$sql .= ', `updated_at`';
		if($this->deleted_at())
			$sql .= ', `deleted_at`';

		$sql .= ' FROM ' . $this->table();

		if($id === NULL && $this->where === NULL)
			return $this->database->execute($sql);

		$sql .= ' WHERE';

		$args = array();

		if($id !== NULL) {
			$sql .= ' id=?';
			$args[] = $id;
			if($this->where !== NULL) $sql .= ' AND';
		}

		if($this->where !== NULL) {
			$sql .= ' ' . $this->where;
			$args = array_merge($args, $this->where_args);
		}

		if($paginator !== NULL) {
			$query = $this->database->paginate($paginator->page(), $paginator->limit())->execute($sql, $args);
			$paginator->set_total(
					$this->database->execute('SELECT FOUND_ROWS() AS rows')->result()[0]->rows
			);
			return $query;
		}
		else
			return $this->database->execute($sql, $args);
	}

	protected function update($id = NULL, $vals) {

		//UPDATE STATEMENT
		$sql = 'UPDATE ' . $this->table();

		//COMPOSE COLUMNS AND VALUES
		$sql .= ' SET `' . substr(implode('`=?, `', $this->columns()), 0, -3) . '`=?';
		if($this->updated_at())
			$sql .= ' , `updated_at`=NOW() WHERE';

		//COMPOSE ARGS
		$args = array();
		foreach($this->columns as $column)
			$args[] = $vals[$column];
		
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

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $args);
	}

	protected function delete($id = NULL) {

		//DELETE STATEMENT
		if($this->deleted_at())
			//SOFT DELETE IF deleted_at exists
			$sql = 'UPDATE FROM ' . $this->table() . ' SET `deleted_at`=NOW() WHERE `' . $this->pk() . '`=?';
		else
			//HARD DELETE IF deleted_at not exists
			$sql = 'DELETE FROM ' . $this->table() . ' WHERE `' . $this->pk() . '`=?';

		//EXECUTE STATEMENT
		return $this->database->execute($sql, $id);
	}


}

?>
