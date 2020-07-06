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
	
	function updateAllBalance($year, $month) {
		$users = $this->User_model->get_users(TRUE);
		foreach ($users as $userId => $user) {
			$data["balance"][$userId] = array();
			$userMoneyStatus = $this->Balance_model->getUserMoneyStatus($userId, $year, $month);
			$data["balance"][$userId]['bill'] = $userMoneyStatus['bill']['totalCost'];
			$data["balance"][$userId]['totalPayments'] = $userMoneyStatus['totalPayments'];
			$previousBalanceRow = $this->Balance_model->getPreviousBalance($userId);
			$data["balance"][$userId]['previousBalance'] = $previousBalanceRow['debt'];
			$data["balance"][$userId]['debt'] = $data["balance"][$userId]['totalPayments'] - $data["balance"][$userId]['bill'] + $data["balance"][$userId]['previousBalance']; 
		}
		return $data["balance"]; 
	}
	
	// Util function /////////////////////////////////////

	function getUserMoneyStatus($userId, $year, $month) {
		$userMoneyStatus['bill'] = array();
		$userMoneyStatus['bill'] = $this->Resa_model->getBill($userId, $year, $month); 

		$userMoneyStatus["payments"] = array();
		$userPayments = $this->Payment_model->get_payment_where(array('user_id' => $userId, 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month ));
	    
		$userMoneyStatus["totalPayments"] = 0;
		if (sizeof($userPayments)==0) {
  			$userMoneyStatus["payments"][0]["status"]="-";
			$userMoneyStatus["payments"][0]["amount"]="-";
   			$userMoneyStatus["payments"][0]["payment_date"]="-";
			$userMoneyStatus["payments"][0]["type"]="-";
			$userMoneyStatus["payments"][0]["bank_id"]="-";
			$userMoneyStatus["payments"][0]["cheque_Num"]="-";
		} else {
			foreach ($userPayments as $curPayment) {
				$datePayment = strtotime($curPayment['month_paided']);
				$curPayment['datePaid'] = strftime("%B %Y", $datePayment);
				$userMoneyStatus["payments"][] = $curPayment;
				$userMoneyStatus["totalPayments"] += $curPayment['amount'];
			}
		}
		return $userMoneyStatus;
	}
	
	function getPreviousBalance($userId) {
		$previousBalance = 0;
		$sql = "SELECT user_id, max(date), debt FROM balance where user_id = $userId";
		$query = $this->db->query($sql);
		$previousBalance = $query->row_array();
		return $previousBalance;
	}
	
}
?>
