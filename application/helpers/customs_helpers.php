<?php 
if (!function_exists('pr')) {
	function pr($arr = array(), $ret = FALSE) {
		echo "<pre>";
		print_r($arr);
		if (!$ret) {
			die;
		}

		echo "</pre>";
	}
}