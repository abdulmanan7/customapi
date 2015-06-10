<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require APPPATH . '/libraries/REST_Controller.php';

class Client extends CI_Controller {
	function request_key() {
		$this->load->library('rest', array(
			'server' => 'http://localhost/projects/myapi/',
			'api_key' => 'b35f83d49cf0585c6a104476b9dc3694eee1ec4e',
			'api_name' => 'X-API-KEY',
		));
		$created_key = $this->rest->put('/key/create', 'json');
		// $this->rest->info($created_key);
		print_r($created_key);
		die;

	}
	public function check_user($id = '') {
		$result = $this->user_model->check_user($id);
		$status = $this->validate_user($id, $result['secret_key']);
		if ($status) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	public function register_user() {
		$apiKey = $this->request_key();
		$data = array(
			'app_key' => $appKey,
		);
		$this->userId = $this->user_model->add($data);
	}
	//Some encryption function
	public function validate_user($id = '', $sec_key) {
		$decrypted = $this->encrypt->decode($sec_key, $this->encrypted_key . $id);
		if ($decrypted === $id) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	public function get_user($id = 3, $api_key = "ff0ee06d7ba095ef9a7276e2802ca9c997c06918") {
		$this->load->library('rest', array(
			'server' => 'http://localhost/projects/myapi/',
			'api_key' => $api_key,
			'api_name' => 'X-API-KEY',
		));
		$user = $this->rest->get('api/user', array('id', $id), 'json');
		// $this->rest->info($created_key);
		print_r($user);
		die;

	}
	function ci_curl($user_id = 1, $api_key = NULL, $format = 'json') {
		// $username = 'admin';
		// $password = '1234';
		$bUrl = base_url('api/user/id/' . $user_id . '/format/' . $format);
		$this->load->library('curl');
		// echo $bUrl;die;
		$this->curl->create($bUrl);

		// Optional, delete this line if your API is open
		// $this->curl->http_login($username, $password);

		$this->curl->post(array(
			'id' => $user_id,
			// 'email' => $new_email,
		));

		$result = json_decode($this->curl->execute());

		if (isset($result->status) && $result->status == 'success') {
			echo 'User has been updated.';
		} else {
			echo 'Something has gone wrong';
		}
	}

}
?>