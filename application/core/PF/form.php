<?php

class PF_Form {

	private $success;

	public function __construct() {
		$this->success = true;
	}

	public function status($success) {
		$this->success = $success;
	}

	public function select($dataset, $attributes, $default_value, $value, $text) {
		$html = '<select ';
		$name = '';
		$default = '';
		foreach($attributes as $key => $val) {
			if($key == 'default') {
				$default = $val;
				continue;
			}
			if($key == 'name')
				$name = $val;
			$html .= $key . '="' . $val . '" ';
		}
		$html = substr($html, 0, -1) . '>';

		if($default != '') $html .= '<option value="">' . $default . '</option>';

		foreach($dataset as $data) {
			$cur = $value($data);
			if($this->success) {
				if($default_value != '' && $cur == $default_value)
					$html .= '<option value="' . $cur . '" selected>' . $text($data) . '</option>';
				else
					$html .= '<option value="' . $cur . '">' . $text($data) . '</option>';
			}
			else {
				if($this->request->post($name) == $cur || (strlen($name) > 2 && substr($name, -2) == '[]' && $cur == $default_value))
					$html .= '<option value="' . $cur . '" selected>' . $text($data) . '</option>';
				else
					$html .= '<option value="' . $cur . '">' . $text($data) . '</option>';
			}
		}

		$html .= '</select>' . "\n";
		echo $html;
	}

	public function text($attributes, $default_value) {
		$html = '<input type="text" ';
		$name = '';
		foreach($attributes as $key => $val) {
			if($key == 'name')
				$name = $val;

			if(is_int($key))
				$html .= $val . ' ';
			else
				$html .= $key . '="' . $val . '" ';
		}

		if($this->success) {
			if($default_value != '')
				$html .= 'value="' . $default_value . '" ';
			else
				$html .= 'value=""';
		}
		else {
			$html .= 'value="' . $this->request->post($name) . '" ';
		}

		$html = substr($html, 0, -1) . '/>';
		echo $html;
	}

	public function textarea() {
	}

}
