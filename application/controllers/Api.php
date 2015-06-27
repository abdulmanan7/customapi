<?php
require APPPATH . '/libraries/REST_Controller.php';

class api extends REST_Controller {
	function user_get() {
		if (!$this->get('id')) {
			$this->response(NULL, 400);
		}
		$this->load->model('user_model');
		$user = $this->user_model->get($this->get('id'));

		if ($user) {
			$this->response($user, 200); // 200 being the HTTP response code
		} else {
			$this->response(NULL, 404);
		}
	}

	function user_post() {
		pr($_FILES['image']);
		$result = $this->user_model->update($this->post('id'), array(
			'name' => $this->post('name'),
			'email' => $this->post('email'),
		));

		if ($result === FALSE) {
			$this->response(array('status' => 'failed'));
		} else {
			$this->response(array('status' => 'success'));
		}

	}

	function users_get() {
		$users = $this->user_model->get_all();

		if ($users) {
			$this->response($users, 200);
		} else {
			$this->response(NULL, 404);
		}
	}
	public function get_user_secret_key($api_key, $userid = NULL) {
		$this->load->model('user_model');
		$result = $this->user_model->user_sec_key($api_key);
		return $result['secret_key'];
	}

	public function clientRequest_post() {

		//getting values coming in headers
		$headers = array();
		foreach (getallheaders() as $name => $value) {
			$headers[$name] = $value;
		}
		$this->load->model('user_model');
		$jdata = json_encode($headers);
		$this->db->insert('request_header', array('signature' => urldecode($headers['X-Signature']), 'api_key' => $headers['X-Api-Key'], 'data' => $jdata));

		//Getting values coming in Post request.
		$entityBody = file_get_contents('php://input', 'r');
		parse_str($entityBody, $post_data);

		$pData = json_decode($post_data['data'], true);
		$s_key = "9FGGkTzBpt65S7MAIJGberJvhFo=";
		if (isset($post_data['customerId'])) {
			$customer_id = $post_data['customerId'];
		}
		switch ($headers['X-Type']) {

			//This is will be executed when in client request header x-type define as validate
			case 'validate':
				$sdata = $s_key . 'customerId=1' . "utc=" . $headers['X-Utc'] . "GETCUSTOMER";
				$encrypted_sdata = $this->customapi_lib->createHmacsha($sdata, 'SHA256', $s_key);
				if ($customer_id == '1' && urldecode($headers['X-Signature']) === $encrypted_sdata) {
					$this->response(array('sdata' => $sdata,
						'hashofsdata' => $encrypted_sdata,
						'message' => "Verified!! the customer is valid",
						'status' => "200",
						'customerId' => "1",
						'customer_name' => "jonDoe",
						'account' => 'ADc-Cash',
						'address' => 'New york city',
						'phone' => '+122232123213'), 200);
				} else {
					$this->response(array('message' => 'Customer is not valid ', "status" => 404), 404);
				}
				break;

			//This is will be executed when in client request header x-type define as register
			case 'register':
				// $sdata = $headers['X-Api-Key'] . $pData["customerId"] . $pData['amount'] . $pData['station'] . $pData['terminal'] . $headers['X-Utc'] . "REGISTER";
				$sdata = $headers['X-Api-Key'] . "customerId=" . $pData["customerId"] . "utc=" . $headers['X-Utc'] . "REGISTER";
				$encrypted_sdata = $this->customapi_lib->createHmacsha($sdata, 'SHA256', $s_key);
				if (urldecode($headers['X-Signature']) === $encrypted_sdata) {
					$this->response(array(
						'txcode' => '12313',
						'status' => "200"));
				} else {
					$this->response(array('message' => 'Error processing your data,please try again later ', 'status' => 404), 404);
				}
				break;
			default:
				$this->response(array("message" => 'Please Define X-Type in the header !', "status" => 400), 400);
				break;
		}

	}
	public function Request_post() {

		//getting values coming in headers
		$headers = array();
		foreach (getallheaders() as $name => $value) {
			$headers[$name] = $value;
		}
		$this->load->model('user_model');
		$jdata = json_encode($headers);
		$this->db->insert('request_header', array('signature' => urldecode($headers['X-Signature']), 'api_key' => $headers['X-Api-Key'], 'data' => $jdata));

		//Getting values coming in Post request.
		$entityBody = file_get_contents('php://input', 'r');
		parse_str($entityBody, $post_data);
		//validate string to match with comming Hstring.
		$s_key = "9FGGkTzBpt65S7MAIJGberJvhFo=";
		if (isset($post_data['customerId'])) {
			$customer_id = $post_data['customerId'];
		} else {
			$this->response('Customer id is not provided ', 404);
		}
		$sdata = $headers['X-Api-Key'] . $post_data["customerId"] . $post_data['amount'] . $post_data['station'] . $post_data['terminal'] . $headers['X-Utc'] . "REGISTER";
		$encrypted_sdata = $this->customapi_lib->createHmacsha($sdata, 'SHA256', $s_key);
		if (urldecode($headers['X-Signature']) === $encrypted_sdata) {
			$this->response(array(
				'txcode' => $post_data['txcode'],
				'status' => "200"));
		} else {
			$this->response('Error processing your data,please try again later ', 404);
		}
	}
}
?>