<?php

//this is default paginator
abstract class PF_Paginator {

	private $info;
	private $nsp;

	public function __construct() {
		$this->nsp = 'default';
		$this->start_nsp($this->nsp);
	}

	//start another paginator
	public function start_nsp($nsp) {
		$this->nsp = $nsp;
		
		if(!isset($this->info[$nsp]))
		$this->info = 
		array(
			$nsp =>
			array(
				'page' => 1,
				'limit' => 1,
				'total' => 0,
				'width' => 7
			)
		);
	}

	public function end_nsp() {
		$this->nsp = 'default';
	}

	//set current page number
	public function set_page($page) {
		if(!is_numeric($page)) $page = 1;
		
		$this->info[$this->nsp]['page'] = $page;
	}

	//set limit of a page
	public function set_limit($limit) {
		$this->info[$this->nsp]['limit'] = $limit;
	}

	//set total number of items
	public function set_total($total) {
		$this->info[$this->nsp]['total'] = $total;
	}

	//set the width of the central part of page number
	public function set_width($width) {
		$this->info[$this->nsp]['width'] = $width;
	}

	//return page number of last page
	public function last_page() {
		$last_page = ceil($this->total() / $this->limit());
		if($last_page == 0) $last_page = 1;

		return $last_page;
	}

	//return the current page
	public function page() {
		return $this->info[$this->nsp]['page'];
	}

	//return the limit of a page
	public function limit() {
		return $this->info[$this->nsp]['limit'];
	}

	//return total number of item
	public function total() {
		return $this->info[$this->nsp]['total'];
	}

	public function width() {
		return $this->info[$this->nsp]['width'];
	}

	//return number of item in a page
	public function page_total() {
		$last_page = $this->last_page();

		if($this->page() == $this->last_page()) {
			if($this->total() == 0) return 0;
			else return $this->total() - ($last_page - 1) * $this->limit();
		}
		else
			return $this->limit();
	}

	public function view() {
		//prev part
		$html = '';

		if($this->page() > 1) {
			$html .= $this->prev(true, $this->page() - 1);
		}
		else {
			$html .= $this->prev(false, $this->page() - 1);
		}

		//central part
		$last_page = $this->last_page();
		$page = $this->page();

		$width = $this->width();
		$left = floor($width / 2);
		$offset = ($page - $left) < 1 ? 1 : $page - $left;
		$offset = ($offset + ($width - 1) > $last_page ? ($last_page - ($width - 1) < 1 ? 1 : $last_page - ($width - 1)) : $offset);

		if($page == 1)
			$html .= $this->page_num(true, 1);
		else
			$html .= $this->page_num(false, 1);

		if($last_page == 1)
			$html .= $this->page_text();

		if($offset > 2)
			$html .= $this->ellipsis();

		for($i = $offset; $i < $offset + $width && $i < $last_page; $i++) {
			if($i == 1 || $i == $last_page) continue;

			if($page == $i)
				$html .= $this->page_num(true, $i);
			else
				$html .= $this->page_num(false, $i);
		}

		if($offset + $width < $last_page)
			$html .= $this->ellipsis();

		if($last_page != 1) {
			if($page == $last_page)
				$html .= $this->page_num(true, $last_page);
			else
				$html .= $this->page_num(false, $last_page);
			$html .= $this->page_text();
		}

		//next part
		if($this->page() < $last_page) {
			$html .= $this->next(true, $this->page() + 1);
		}
		else {
			$html .= $this->next(true, $this->page() + 1);
		}

		return $html;
	}

	abstract function prev($on, $lastpage);
	abstract function page_num($on, $page);
	abstract function page_text();
	abstract function next($on, $nextpage);
	abstract function ellipsis();

}
