<?php

class Custom_Paginator extends PF_Paginator {

	public function __construct() {
		parent::__construct();
	}

	//@override
	public function prev($on, $lastpage) {
		return 'Prev | ';
	}

	//@override
	public function page_num($on, $page) {
		return ' ' . $page . ' ';
	}
	
	//@override
	public function page_text() {
		return ' Pages ';
	}
	
	//@override
	public function next($on, $nextpage) {
		return ' | Next';
	}
	
	//@override
	public function ellipsis() {
		return '...';
	}


}

?>
