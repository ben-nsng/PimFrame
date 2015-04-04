<?php

class Custom_Paginator extends PF_Paginator {

	public function __construct() {
		parent::__construct();
	}

	//@override
	public function prev($on, $lastpage) {
		if($on) return '<a data-page="' . $lastpage . '" href="javascript:void(0)">Prev</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
		else return '<a href="javascript:void(0)">Prev</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
	}

	//@override
	public function page_num($on, $page) {
		if($on) return '<a class="active" href="javascript:void(0)">' . $page . '</a>';
		return '<a data-page="' . $page . '" href="javascript:void(0)">' . $page . '</a>';
	}
	
	//@override
	public function page_text() {
		return ' Pages ';
	}
	
	//@override
	public function next($on, $nextpage) {
		if($on) return '&nbsp;&nbsp;|&nbsp;&nbsp;<a data-page="' . $nextpage . '" href="javascript:void(0)">Next</a>';
		else return '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)">Next</a>';
	}
	
	//@override
	public function ellipsis() {
		return '...';
	}


}

?>
