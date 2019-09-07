<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Balance_model extends CI_Model {
	var $balance_table = 'balance';
	
	public function __construct() {
		$this->load->database();
	}
		
	function create($balance) {
		$balance["id"] = "";
		if(!$this->db->insert($this->balance_table, $balance)) { 
			return FALSE;						
		}
		return TRUE;
	}

	public function get_balances($id = FALSE) {
		if ($id === FALSE) {
			$query = $this->db->get($this->balance_table);
			return $query->result_array();
		}
		$query = $this->db->get_where($this->balance_table, array('id' => $id));
		return $query->row_array();
	}
	
	public function get_balance_where($where) {
		$query = $this->db->get_where($this->balance_table, $where);
		return $query->result_array();
	}
	
	public function get_balance_where_unique($where) {
		$query = $this->db->get_where($this->balance_table, $where);
		return $query->row_array();
	}
	
	function delete($id = FALSE) {
		if ($id === FALSE) {
			return FALSE;
		}
		$this->db->delete($this->balance_table, array('id' => $id));
		if ($this->db->affected_rows() > 0)
			return TRUE;
		return FALSE;

	}

	function update($id, $balance) {
		$this->db->where('id', $id);
		$this->db->update($this->balance_table, $balance); 
	}
	
	// Util function /////////////////////////////////////
	
}
?>
