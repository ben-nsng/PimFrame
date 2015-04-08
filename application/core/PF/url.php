<?php

class PF_Url {

	public function __construct() {
	}

	
	public function encode($url) {
		return
			str_replace('+', '-', 
				strtolower(
					urlencode(
						str_replace('/', '%2f',
							str_replace('#', '%23',
								$url
							)
						)
					)
				)
			);
	}

	public function decode($url) {
		return
			str_replace('%2f', '/', 
				str_replace('%23', '#',
					urldecode(
						str_replace('-', '+', 
							$url
						)
					)
				)
			);
	}
	
}

?>
