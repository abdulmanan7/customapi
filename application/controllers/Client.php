<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require APPPATH . '/libraries/REST_Controller.php';

class Client extends CI_Controller {
	public function request_key() {
		$this->load->library('rest', array(
			'server' => 'http://sajidshah.com/customapi/',
			'api_key' => 'b35f83d49cf0585c6a104476b9dc3694eee1ec4e',
			'api_name' => 'X-API-KEY',
		));
		$app_key = $this->rest->put('key/create');
		$secret_key = $this->customapi_lib->createHmacsha($app_key['key'], 'sha1');

		if (isset($app_key['key'])) {
			$data = array(
				'app_key' => $app_key['key'],
				'secret_key' => $secret_key,
			);
			$this->load->model('user_model');
			$this->userId = $this->user_model->add($data);

		}
		$result_keys = array('app_key' => $app_key['key'], 'secret_key' => $secret_key);
		print_r($result_keys);
		die;

	}
	function request_test() {
		$this->load->library('rest', array(
			'server' => 'http://sajidshah.com/customapi/api/',
			'api_key' => 'b35f83d49cf0585c6a104476b9dc3694eee1ec4e',
			'api_name' => 'X-API-KEY',
		));
		$created_key = $this->rest->post('clientRequest', array(
			'id' => '1',
			'CustomerId' => '1',
			'amount' => '2450',
			'operatorName' => 'Jondoe',
			'operator' => '12',
			'teminalId' => '123',
			'time_date' => '12/12/12 12:12:12',
		), 'json');
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
	public function get_user($id = 3, $api_key = "b35f83d49cf0585c6a104476b9dc3694eee1ec4e") {
		$this->load->library('rest', array(
			'server' => 'http://sajidshah.com/customapi/',
			'api_key' => $api_key,
			'api_name' => 'X-API-KEY',
		));
		$user = $this->rest->get('api/user', array('id', $id), 'json');
		// $this->rest->info($created_key);
		print_r($user);
		die;

	}
	function request_curl($url = NULL) {
		$utc = time();
		$customerName = "Jon doe";
		$customerId = '1';
		$terminal_id = '1234';
		$opr_name = 'Jane';
		$opr_id = '10';
		$account = '12312312';
		$amount = '1200';
		$address = 'London ';
		$url = 'localhost/mapi';
		$api_k = "308e37aa3570523de041d7442028b9fb54243a0d";

		$secret_key = $this->get_user_secret_key($api_k);
		$post = "address=" . $address . "&account=" . $account . "&url=" . $url . "&customer_name=" . $customerName . "&api_key=" . $api_k . "&id=1&customerId=" . $customerId . "&amount=" . $amount . "&operatorName=" . $opr_name . "&operatorid=" . $opr_id . "&terminalId=" . $terminal_id . "&time_date=" . $utc;
		$encrypted_sdata = $this->_hmac_sha($customerId . $customerName . $account . $address . $url, $secret_key);
		$header_data = array(
			"Accept: application/json",
			"X-API-KEY:" . $api_k,
			"X-UTC:" . $utc,
			"X-Signature:" . $encrypted_sdata,
		);
		$ch = curl_init();
		$curlOpts = array(
			CURLOPT_URL => 'http://sajidshah.com/customapi/api/clientRequest',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $header_data,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,

			CURLOPT_HEADER => 1,
		);
		curl_setopt_array($ch, $curlOpts);
		$answer = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

		$info = curl_getinfo($ch);
		// $headers = $this->get_headers_from_curl_response($answer);

		// If there was an error, show it
		if (curl_error($ch)) {
			die(curl_error($ch));
		}

		curl_close($ch);

		echo "<pre>";
		echo $answer;
		// print_r($headers);
	}
	private function _generate_key($str) {
		$this->load->helper('security');
		$salt = do_hash($str);
		$new_key = substr($salt, 0, config_item('rest_key_length'));
		return $new_key;
	}
	private function _hmac_sha($str = '', $sec_key = NULL, $tec = 'SHA256') {
		// $sec_key = '8/iAlrQThKQSvNkfEYu9YcueiRs=';
		$enc_string = hash_hmac($tec, $str, $sec_key, true);
		return base64_encode($enc_string);
	}
	function get_headers_from_curl_response($response) {
		$headers = array();

		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

		foreach (explode("\r\n", $header_text) as $i => $line) {
			if ($i === 0) {
				$headers['http_code'] = $line;
			} else {
				list($key, $value) = explode(': ', $line);

				$headers[$key] = $value;
			}
		}

		return $headers;
	}
	public function get_user_secret_key($api_key, $userid = NULL) {
		$this->load->model('user_model');
		$result = $this->user_model->user_sec_key($api_key);
		return $result['secret_key'];
	}
}
?>