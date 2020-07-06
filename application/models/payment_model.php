<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_model extends CI_Model {
	var $payment_table = 'payment';
	var $banque_table = 'bank';
	var $user_table = 'users';

	public function __construct() {
		$this->load->database();
	}
		
	function create($payment) {
		//Insert payment into the database
		if(!$this->db->insert($this->payment_table, $payment)) { 
			return FALSE;						
		}
		return TRUE;
	}
	
	/**
	 * Update a payment
	 */
	function update($id, $payment) {
		$this->db->where('id', $id);
		$this->db->update($this->payment_table, $payment); 
	}
	

	public function get_payment($id = FALSE) {
		if ($id === FALSE) {
			$query = $this->db->get($this->payment_table);
			return $query->result_array();
		}
	
		$query = $this->db->get_where($this->payment_table, array('id' => $id));
		return $query->row_array();
	}
	
	public function get_payment_where($where) {
		$query = $this->db->get_where($this->payment_table, $where);
		return $query->result_array();
	}

	function get_total_payment_where($where) {
		$this->db->select_sum('amount');
		$this->db->from($this->payment_table);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	public function get_full_payment_where($where) {
		$this->db->select('*');
		$this->db->from($this->payment_table);
		$this->db->join('bank', 'bank.id = payment.bank_id');
		$this->db->join('users', 'users.id = payment.user_id');
		$this->db->where($where);
		
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function delete($id = FALSE) {
		if ($id === FALSE) {
			return FALSE;
		}
		$this->db->delete($this->payment_table, array('id' => $id));
		if ($this->db->affected_rows() > 0)
			return TRUE;
		return FALSE;

	}
	
	// Util function /////////////////////////////////////
	function setPaymentFromPostData($post) {
		$payment["user_id"] = $post['user_id'];
		$payment["amount"] = strtr($post['amount'], ",", ".");
		$date = mktime(0, 0, 0, $post['month'], 1, $post['year']);
		$payment['month_paided'] = date("Y-m-d", $date);
		$payment["payment_date"] = date('Y-m-d');
		$payment["type"] = $post['type'];
		$payment["bank_id"] = $post['bank'];
		if (isset($post['chequeNum'])) {
			$payment["cheque_Num"] = $post['chequeNum'];
		} else {
			$payment["cheque_Num"] = 0;
		}
		if (isset($post['status'])) {
			$payment["status"] = $post['status'];
		} else {
			$payment["status"] = 1;
		}		
		return $payment;
	}
	
	
	
}
?>
