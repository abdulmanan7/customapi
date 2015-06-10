<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Name:  Customapi Model
 *
 * Author:  Abdul Manan
 *
 */

class User_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	//CRUD Start
	public function add($data) {
		$this->db->insert('api_users', $data);
		return $this->db->insert_id();
		// return ($this->db->_error_message()) ? 'error try again' : $this->db->insert_id();
	}
	public function update($data, $user_id) {
		$this->db->where('id', $user_id);
		$this->db->update('api_users', $data);
	}
	public function check_user($user_id = NULL) {
		$query = null; //emptying in case
		$query = $this->db->get_where('api_users', array( //making selection
			'id' => $user_id,
		));

		$count = $query->num_rows(); //counting result from query

		if ($count === 0) {
			return FALSE;
		}
		return $query->row_array();
	}
	public function get($id) {
		$this->db->select('*');
		$this->db->from('api_users');
		$this->db->where('id', $id);
		return $this->db->get()->row_array();
	}
}