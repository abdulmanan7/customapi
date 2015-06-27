<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Customer extends CI_Controller {

	public $userId = 1;
	protected $secret_key;
	protected $app_key;
	protected $bank_app_key = "eY/etUTuQV9SFC/nTXXT3S6jE8kI6GxDiC9ugjEIjJz9DkhRtPn4cQdJZiElb1Bqv/b32Tj4F8YGpXZwl3q6Jw==";
	function __construct() {
		parent::__construct();
		$this->load->model('customapi_model');
	}
	public function index() {
		// $sdata = "eY/etUTuQV9SFC/nTXXT3S6jE8kI6GxDiC9ugjEIjJz9DkhRtPn4cQdJZiElb1Bqv/b32Tj4F8YGpXZwl3q6Jw==1433515371http://www.bazmoapps.com/nibssagg/api/Payment?SchoolId=DEMO&PaymentReference=AA129GET";
		// $encrypted_sdata=$this->customapi_lib->createHmacsha($sdata);
		// print_r($encrypted_sdata);
		// // $this->encrypt();
		$this->load->view('test');
		if ($this->input->post()) {
			if ($this->customapi_lib->validate_user($this->userId, $this->secret_key)) {
				echo "You are not authorize user !";
			} else {
				$this->prepData();
			}
		}
		if (isset($this->userId)) {
			$data = "Please Provide Payment Details to Process!";
			$this->load->view('customer', $data);
		} else {
			$this->load->view('login');
		}
	}
	private function get_user($id) {
		$result = $this->customapi_model->get_user($id);
		$this->secret_key = $result['secret_key'];
		$this->userId = $result['id'];
		$this->app_key = $result['app_key'];
	}
	public function prepData() {
		$schoolId = $this->input->post('schoolId');
		$pay_ref = $this->input->post('paymeny_ref');
		$utc = $this->customapi_lib->utcTimestamp();
		//Uri uri = new Uri(String.Format("http://www.bazmoapps.com/nibssagg/api/Payment?SchoolId={0}&PaymentReference={1}", schoolId, paymentReference));
		//$url=urlencode("http://localhost/projects/customapi/customer/customer?SchoolId=DEMO&PaymentReference=AA127");

		$url = "http://www.bazmoapps.com/nibssagg/api/Payment?SchoolId=DEMO&PaymentReference=AA129GET";
		// $url="http://localhost/projects/customapi/customer?SchoolId=DEMO&PaymentReference=AA127GET";

		$sdata = $this->bank_app_key . $utc . $url;
		// $sdata= "eY/etUTuQV9SFC/nTXXT3S6jE8kI6GxDiC9ugjEIjJz9DkhRtPn4cQdJZiElb1Bqv/b32Tj4F8YGpXZwl3q6Jw==1433515371http://www.bazmoapps.com/nibssagg/api/Payment?SchoolId=DEMO&PaymentReference=AA129GET";
		$encrypted_sdata = $this->customapi_lib->createHmacsha($sdata);
		// echo $encrypted_sdata."<br/>" ;die;
		$result = array(
			'Stamp' => $utc,
			'sdata' => $sdata,
			'hash' => $encrypted_sdata,
			'url' => $url,
			'app_key' => $this->bank_app_key,
		);

		$this->request($result);
		echo "<pre>";
		print_r($result);
		echo "</pre>";
	}
	public function init_user() {
		if ($this->input->post('user_id')) {
			$this->get_user($this->input->post('user_id'));
			redirect("/customer");
		}
	}
	function request($data) {
		$header_data = array(
			"Content-Type: application/x-www-form-urlencoded",
			"X-PSK:" . $data['app_key'],
			"X-Stamp:" . $data['Stamp'],
			"X-Signature:" . $data['hash'],
		);
		$ch = curl_init();
		$curlOpts = array(
			CURLOPT_URL => 'www.bazmoapps.com/nibssagg/api/Payment?SchoolId=DEMO&PaymentReference=AA127',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $header_data,
			CURLOPT_FOLLOWLOCATION => true,
		);
		curl_setopt_array($ch, $curlOpts);
		$answer = curl_exec($ch);
		// If there was an error, show it
		if (curl_error($ch)) {
			die(curl_error($ch));
		}

		curl_close($ch);
		echo $answer;
	}

	function encrypt() {
		$string = "eY/etUTuQV9SFC/nTXXT3S6jE8kI6GxDiC9ugjEIjJz9DkhRtPn4cQdJZiElb1Bqv/b32Tj4F8YGpXZwl3q6Jw==1433515371http://www.bazmoapps.com/nibssagg/api/Payment?SchoolId=DEMO&PaymentReference=AA129GET";
		$key = "162902e822c1f27a86e40d01e991893e918b0ae2bb49adcce6f26496829c9dbeba4b0386a3550a8e753a9d07c2e9e2dcecee350106593a5dbdbf88a494f8dd56";
		$hash = hash_hmac('SHA256', $string, $key, true);
		echo base64_encode($hash);
		echo "<br>" . "avPTSv0xDQ0onBj3TioZWETrsvhBMKsGSvmt8PEWkLQ=";
	}
	public function test() {
		// Start session (also wipes existing/previous sessions)
		$this->curl->create('http://localhost/projects/myapi/key/index_put.php/');

// Option & Options
		$this->curl->option(CURLOPT_BUFFERSIZE, 10);
		$this->curl->options(array(CURLOPT_BUFFERSIZE => 10));

// More human looking options
		$this->curl->option('buffersize', 10);

// Login to HTTP user authentication
		$this->curl->http_login('admin', '1234');

// Post - If you do not use post, it will just run a GET request
		$post = array('level' => 1);
		// $this->curl->post($post);
		$this->curl->put($post);

// Cookies - If you do not use post, it will just run a GET request
		// $vars = array('foo' => 'bar');
		// $this->curl->set_cookies($vars);

// Proxy - Request the page through a proxy server
		// Port is optional, defaults to 80
		// $this->curl->proxy('http://example.com', 1080);
		// $this->curl->proxy('http://example.com');

// Proxy login
		// $this->curl->proxy_login('username', 'password');s

// Execute - returns responce
		echo $this->curl->execute();

// Debug data ------------------------------------------------

// Errors
		// $this->curl->error_code; // int
		// $this->curl->error_string;

// Information
		// return $this->curl->info; // array
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */