<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cost_model extends CI_Model {
	var $cost_table = 'cost';
	
	public function __construct() {
		$this->load->database();
		$this->load->model('Resa_model');
		$this->load->model('Payment_model');
		$this->load->model('Balance_model');
	}
		
	function create($cost) {
		$cost["id"] = "";
		if(!$this->db->insert($this->cost_table, $cost)) { 
			return FALSE;						
		}
		return TRUE;
	}

	public function get_costs($id = FALSE) {
		if ($id === FALSE) {
			$query = $this->db->get($this->cost_table);
			return $query->result_array();
		}
		$query = $this->db->get_where($this->cost_table, array('id' => $id));
		return $query->row_array();
	}
	
	public function get_cost_where($where) {
		$query = $this->db->get_where($this->cost_table, $where);
		return $query->result_array();
	}
	
	function delete($id = FALSE) {
		if ($id === FALSE) {
			return FALSE;
		}
		$this->db->delete($this->cost_table, array('id' => $id));
		if ($this->db->affected_rows() > 0)
			return TRUE;
		return FALSE;

	}

	function update($id, $cost) {
		$this->db->where('id', $id);
		$this->db->update($this->cost_table, $cost); 
	}
	
	// Util function /////////////////////////////////////
	function storeOnPaymentUpdate($payment) {
		$month = date("n", strtotime($payment['month_paided']));
		$year = date("Y", strtotime($payment['month_paided']));
		$userId = $payment['user_id'];

		$this->persistCost($month, $year, $userId);
		/* if payment is validated then 
		 * 		cost_N['paid']=cost_N['paid']+payment_N['amount'] 
		 * 		cost_N+1['debt']=geteCost_N - cost_N['paid']
		 */
	}
	
	function persistCost($month, $year, $userId) {
		$costCurrent = $this->getCost($year, $month, $userId);
		$cost['paid'] = $costCurrent['paid'];
		$cost['debt'] = $costCurrent['debt'];
 		$date = mktime(0, 0, 0, $month, 1, $year);
		$cost['month_paided'] = date("Y-m-d", $date);
 		$cost['user_id'] = $userId;
 		$DBCost = current($this->Cost_model->get_cost_where(array('user_id' => $userId, 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month )));
		if($DBCost) {
			$this->update($DBCost["id"], $cost);
		} else {
			$this->create($cost);
		}
		
		//Update cost next month if exists
		$nextMonth=$month+1;
		$nextYear=$year; 
		if ($nextMonth>12) {
			$nextMonth=$nextMonth-12;
			$nextYear=$year+1;
		}
		$DBNextCost = current($this->Cost_model->get_cost_where(array('user_id' => $userId, 'YEAR(month_paided)' => $nextYear, 'MONTH(month_paided)' => $nextMonth )));
		if($DBNextCost) {
			$this->persistCost($nextMonth, $nextYear, $userId);
		}
	}
	
	function getCost($year, $month, $userId) {
		$cost['sum']['cost'] = 0;
		$cost['sum']['depassement'] = 0;
		$cost['sum']['depassementPrev'] = 0;
		$cost['sum']['costPrev'] = 0;
		
		$prevDate = strtotime( $year."-".($month-1)."-01" );
		$prevMonth = date("m", $prevDate);
		$prevYear = date("Y", $prevDate);
		
		$children = $this->db->get_where('child', array('user_id' => $userId))->result_array();
		foreach ($children as $child) {
			$childNum=$child['id'];
			//cout des resas du mois courant
			$resas[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $year, 'MONTH(date)' => $month, 'resa_type !=' => 3 ));
			$cost['children'][$childNum]['resa'] = $this->Resa_model->get_cost($resas[$childNum]);
			//cout des depassemants du mois courant
			$depassement[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $year, 'MONTH(date)' => $month, 'resa_type' => 3 ));
			$cost['children'][$childNum]['depassement'] =$this->Resa_model->get_cost($depassement[$childNum]);
			    
			//cout des depassemants du mois precedant
			$depassementPrev[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $prevYear, 'MONTH(date)' => $prevMonth, 'resa_type' => 3 ));
			$cost['children'][$childNum]['depassementPrev'] = $this->Resa_model->get_cost($depassementPrev[$childNum]);
			//cout des resas du mois precedent
			$resasPrev[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $prevYear, 'MONTH(date)' => $prevMonth, 'resa_type !=' => 3 ));
			$cost['children'][$childNum]['resaPrev'] = $this->Resa_model->get_cost($resas[$childNum]);

			$cost['children'][$childNum]['total'] = $cost['children'][$childNum]['resa']["total"] + $cost['children'][$childNum]['depassementPrev']["total"];
	
			$cost['sum']['cost'] += $cost['children'][$childNum]['resa']["total"];
			$cost['sum']['depassementPrev'] += $cost['children'][$childNum]['depassementPrev']["total"];
			$cost['sum']['depassement'] += $cost['children'][$childNum]['depassement']["total"];
			$cost['sum']['costPrev'] += $cost['children'][$childNum]['resaPrev']["total"];
		}
 		
 		//Get validated month payment
		$payment = $this->Payment_model->get_total_payment_where(array('user_id' => $userId, 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month, 'status' => "3" ));
		if (isset($payment['amount'])) {
			$cost['paid'] = $payment['amount'];
		} else {
		$cost['paid'] = 0;
		}

		$lastMonthCost = current($this->Cost_model->get_cost_where(array('user_id' => $userId, 'YEAR(month_paided)' => $prevYear, 'MONTH(month_paided)' => $prevMonth )));
 		if (isset($lastMonthCost["debt"])) {
 			$cost['debtPrev'] = $lastMonthCost["debt"];
 			$cost['sum']['total'] = $cost['sum']['depassementPrev'] + $cost['sum']['cost'] + $cost['debtPrev'];
  		} else {
  			$cost['debtPrev'] = "-";
  			$cost['sum']['total'] = $cost['sum']['depassementPrev'] + $cost['sum']['cost'];
   		}
 		
 		$debt= $cost['sum']['total'] - $cost['paid'];
 		$cost['debt'] = round($debt, 2);

		return $cost;
	}
	
	public function getBalance($year, $month, $userId) {
		$balance['sum']['resa'] = 0;
		$balance['sum']['depassement'] = 0;
		$balance['sum']['total'] = 0;
		
		$children = $this->db->get_where('child', array('user_id' => $userId, 'is_active' => true))->result_array();
		foreach ($children as $child) {
			$childNum=$child['id'];
			//cout des resas du mois courant-1
			$resas[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $year, 'MONTH(date)' => $month-1, 'resa_type !=' => 3 ));
			
			if (sizeof($resas[$childNum])>0) {
				$price = $resas[$childNum][0]['price'];
				$totalResa = sizeof($resas[$childNum])*$price;
				$balance['children'][$childNum]['resaStr'] = sizeof($resas[$childNum])." x ".$price." = ".$totalResa;
			} else {
				$totalResa = 0;
				$balance['children'][$childNum]['resaStr'] = "0";
			}
			//cout des depassemants du mois courant-2
			$depassement[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $year, 'MONTH(date)' => $month-2, 'resa_type =' => 3 ));
			if (sizeof($depassement[$childNum])>0) {
				$price = $depassement[$childNum][0]['price'];
				$totalDepas = sizeof($depassement[$childNum])*$price;
				$balance['children'][$childNum]['depassementStr'] = sizeof($depassement[$childNum])." x ".$price." = ".$totalDepas;
			} else {
				$totalDepas = 0;
				$balance['children'][$childNum]['depassementStr'] = "0";
			}
			
			$balance['children'][$childNum]['total'] = $totalResa + $totalDepas;
			
			$balance['sum']['resa'] += $totalResa;
			$balance['sum']['depassement'] += $totalDepas;
		}
		$balance['sum']['total'] = $balance['sum']['resa'] + $balance['sum']['depassement'];
		
		//restant du du mois courant-2
		$balance['debt'] = 0;
		$balance = $this->Balance_model->get_balance_where(array('user_id' => $userId, 'YEAR(month)' => $year, 'MONTH(month)' => $month-2 ));
		if (sizeof($resas[$childNum])>0) {
			$balance['debt'] = current($balance);
		}
		
		return $balance;
	}
	
	
}
?>
