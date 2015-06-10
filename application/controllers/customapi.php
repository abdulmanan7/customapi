<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Customapi extends CI_Controller {

	protected $userId;
	protected $encrypted_key = "54%&*$#$";

	function __construct() {
		parent::__construct();

		$this->load->model('customapi_model');
	}
	public function index() {
	}
	public function check_user($id = '') {
		$result = $this->customapi_model->check_user($id);
		$status = $this->validate_user($id, $result['secret_key']);
		if ($status) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	Private function register_user($userId) {
		$appKey = $this->genarate_key($userId, TRUE);
		$secKey = $this->genarate_key($userId);
		$data = array(
			'id' => $userId,
			'app_key' => $appKey,
			'secret_key' => $secKey,
		);
		$this->customapi_model->update($data, $this->userId);
	}
	public function add_user() {
		$this->userId = $this->customapi_model->add();
		$this->register_user($this->userId);
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */