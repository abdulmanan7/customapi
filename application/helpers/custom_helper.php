<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('dateformat')) {
	function dateformat($var = '', $time = FALSE) {
		if ($time) {
			$newDate = date("M d, Y, g:i a", strtotime($var));
		} else {
			$newDate = date("M d, Y", strtotime($var));
		}
		return $newDate;
	}
}
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