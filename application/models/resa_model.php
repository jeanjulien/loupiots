<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Resa_model extends CI_Model {
	var $resa_table = 'reservation';
	var $period_table = 'period';
	var $child_table = 'child';
	var $user_table = 'users';
	
	public function __construct() {
		$this->load->database();
		$this->load->model('Period_model');
		$this->load->model('Payment_model');
		$this->load->model('Balance_model');
	}
		
	/**
	 * Create a resa
	 *
	 * @return	bool
	 */
	function insert($resa) {
		$dbResa=$this->get_resa_where(array('child_id' => $resa['child_id'], 'period_id' => $resa['period_id'], 'date' => $resa['date'] ));
		if (sizeof($dbResa)==0) {
			if($this->db->insert($this->resa_table, $resa)) { 
				return TRUE;						
			}
		}
		return FALSE;
	}

	public function get_resa($id = FALSE) {
		if ($id === FALSE) {
			$query = $this->db->get($this->resa_table);
			return $query->result_array();
		}
		$query = $this->db->get_where($this->resa_table, array('id' => $id));
		return $query->row_array();
	}
	
	public function get_resa_where($where) {
		$this->db->where($where);
		$query = $this->db->get($this->resa_table);
		return $query->result_array();
	}
	
	public function get_full_resa_where($where) {
		$this->db->where($where);
		$this->db->join('period', 'period.id = reservation.period_id');
		$query = $this->db->get($this->resa_table);
		
		return $query->result_array();
	}
	
	function delete($id = FALSE) {
		if ($id === FALSE) {
			return FALSE;
		}
		$this->db->delete($this->resa_table, array('id' => $id));
		if ($this->db->affected_rows() > 0)
			return TRUE;
		return FALSE;

	}

	function validateResaByMonth($newClosedMonth, $closedMonth) {
		$resas = array();
		$sql = "SELECT * FROM ".$this->resa_table." WHERE date > '".date("Y-m-d", $newClosedMonth)."' and resa_type = 2 ";
		$resas = $this->db->query($sql);
		if ($resas->num_rows() > 0) {
			foreach ($resas->result_array() as $resa) {
				$resa['resa_type']=1;
				$this->db->update($this->resa_table, $resa, "id = ".$resa['id']);
			}
		}
		
		$sql = "SELECT * FROM ".$this->resa_table." WHERE date > '".date("Y-m-d", $closedMonth)."' and date < '".date("Y-m-d", $newClosedMonth)."' and resa_type = 1 ";
		$resas = $this->db->query($sql);
		if ($resas->num_rows() > 0) {
			foreach ($resas->result_array() as $resa) {
				$resa['resa_type']=2;
				$this->db->update($this->resa_table, $resa, "id = ".$resa['id']);
			}
		}
		return $sql;
	}

	function validateResaByDate($lastCloseDate, $closeDate) {
		$resas = array();
		//$sql = "SELECT * FROM ".$this->resa_table." WHERE MONTH(date)='".date("m", $closedMonth)."' and YEAR(date)='".date("Y", $closedMonth)."'";
		$sql = "SELECT * FROM ".$this->resa_table." WHERE date > '".date("Y-m-d", $closeDate)."' and resa_type = 2 ";
		$resas = $this->db->query($sql);
		if ($resas->num_rows() > 0) {
			foreach ($resas->result_array() as $resa) {
				$resa['resa_type']=1;
				$this->db->update($this->resa_table, $resa, "id = ".$resa['id']);
			}
		}
		
		$sql = "SELECT * FROM ".$this->resa_table." WHERE date > '".date("Y-m-d", $lastCloseDate)."' and date < '".date("Y-m-d", $closeDate)."' and resa_type = 1 ";
		$resas = $this->db->query($sql);
		if ($resas->num_rows() > 0) {
			foreach ($resas->result_array() as $resa) {
				$resa['resa_type']=2;
				$this->db->update($this->resa_table, $resa, "id = ".$resa['id']);
			}
		}
		return $sql;
	}
	
	function getClassroomCallPerDay($date, $classId, $AMPM) {
		$results = array();
		$sql = "SELECT child.name as childName, users.name, max(period.stop_time) as time";
		$sql .= " FROM ".$this->resa_table.", ".$this->period_table.", ".$this->child_table.", ".$this->user_table." ";
		$sql .= " WHERE reservation.child_id=child.id and reservation.period_id=period.id and child.user_id=users.id ";
		$sql .= " AND period.type = '".$AMPM."' AND reservation.date = '".date("Y-m-d", strtotime($date))."' AND child.class_id = '".$classId."'";
		$sql .= " GROUP BY child.name, users.name";
		return $this->db->query($sql)->result_array();
	}

	function getTotalCost($userId) {
		$results = array();
		$sql = "SELECT *";
		$sql .= " FROM ".$this->resa_table.", ".$this->period_table.", ".$this->child_table.", ".$this->user_table." ";
		$sql .= " WHERE reservation.child_id=child.id and reservation.period_id=period.id and child.user_id=users.id ";
		$sql .= " AND users.id = '".$userId."'";
		$resas = $this->db->query($sql)->result_array();
		
		$price = 0;
		foreach ($resas as $resa) {
			$type = $resa['resa_type'];
			if( $type==2) {
				$price += $resa['price'];
			} elseif($type==3) {
				$price += LOUP_DEPASSEMENT_PRICE;
			}		
		}	
		return $price;
	}
	
	// Util function /////////////////////////////////////
	function setResaFromPostData($post) {
		$date = mktime(0, 0, 0, $post['month'], $post['day'], $post['year']);
		$resa['date'] = date("Y-m-d", $date);
		$resa['period_id'] = $post['period'];
		$resa['child_id'] = $post['child'];
		
		$closedMonth = file_get_contents('lastValidate.txt');
		$closedWeek = file_get_contents('lastVisit.txt');
		$closedDate = ($closedMonth>$closedWeek) ? $closedMonth : $closedWeek;
		
		if ($date >= $closedDate) {
			$resa['resa_type'] = "1";   								// normal
		} elseif ($this->session->userdata('privilege')>=2) {			// validee
			$resa['resa_type'] = "3";									// rajout
		} else {
			return FALSE;
		}
		return $resa;
	}
	
	public function create($post, $output=array()) {
		if ($this->insert($post)) {
			$output[] = $post;
			//Recursive on adjacent period
			$nextPeriod = $this->Period_model->getNextPeriod($post["period_id"]);
			if ($nextPeriod) {
				$post['period_id'] = $nextPeriod["id"];
				return $this->create($post, $output);
			}
		}
		return $output;
	}

	public function get_cost($resas) {
		$periodPrices=array();
		foreach ($resas as $resa) {
			if($resa['resa_type'] == 3) {
				$resaPrice = LOUP_DEPASSEMENT_PRICE;
			} else {
				$resaPrice = $resa['price'];
			}
			if (!array_key_exists($resaPrice, $periodPrices)) {
				$periodPrices[$resaPrice] = 1;
			} else {
				$periodPrices[$resaPrice] += 1;
			}
		}
		
		$cost["str"] = "";
		$cost["total"] = 0;
		foreach($periodPrices as $price => $number) {
			if ($cost["str"] == "") {
				$cost["str"] = $price."x".$number;
			} else {
				$cost["str"] += " + ".$price."x".$number;
			}
			$cost["total"] += $price*$number;
		}
		$cost["perioarray"] = $periodPrices;
		return $cost;

	}
	
	public function getBill($userId, $year, $month) {
		$monthBilled = date('n', mktime(0, 0, 0, $month-1, 1, $year)); //mois facturé
		$yearBilled = date('Y', mktime(0, 0, 0, $month-1, 1, $year)); //mois facturé
		$monthPrevBill = date('n', mktime(0, 0, 0, $month-2, 1, $year)); //mois precedent le mois facturé
		$yearPrevBill = date('Y', mktime(0, 0, 0, $month-2, 1, $year));
		
		$children = $this->db->get_where('child', array('user_id' => $userId, 'is_active' => true))->result_array();
		$bill['children']['total']['costResa'] = 0;
		$bill['children']['total']['costDep'] = 0;
		$bill['total'] = 0;
		foreach ($children as $child) {
			$childNum=$child['id'];
			//Resa du mois facturé
			$resas[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $yearBilled, 'MONTH(date)' => $monthBilled, 'resa_type !=' => 3 ));
			if (sizeof($resas[$childNum])>0) {
				$price = $resas[$childNum][0]['price'];
				$childResaPrice = sizeof($resas[$childNum])*$price;
				$bill['children'][$childNum]['costResaStr'] = sizeof($resas[$childNum])." x ".$price." = ".$childResaPrice;
				$bill['children']['total']['costResa'] += $childResaPrice;				
			} else {
				$childResaPrice = 0;
				$bill['children'][$childNum]['costResaStr'] = "0";
			}
			
			//Dépassement du mois precedant le mois facturé
			$depassement[$childNum]= $this->Resa_model->get_full_resa_where(array('child_id' => $childNum, 'YEAR(date)' => $yearPrevBill, 'MONTH(date)' => $monthPrevBill, 'resa_type =' => 3 ));
			if (sizeof($depassement[$childNum])>0) {
				$childDepPrice = sizeof($depassement[$childNum])*LOUP_DEPASSEMENT_PRICE;
				$bill['children'][$childNum]['costDepStr'] = sizeof($depassement[$childNum])." x ".LOUP_DEPASSEMENT_PRICE." = ".$childDepPrice;
				$bill['children']['total']['costDep'] += $childDepPrice;				
			} else {
				$childDepPrice = 0;
				$bill['children'][$childNum]['costDepStr'] = "0";
			}
			
			$bill['children'][$childNum]['sum'] = $childResaPrice + $childDepPrice;
			$bill['total'] += $bill['children'][$childNum]['sum'];
			
			// balance calculation
			//payment Month-1
			$totalPayment = $this->Payment_model->get_total_payment_where(array('user_id' => $userId, 'YEAR(month_paided)' => $yearBilled, 'MONTH(month_paided)' => $monthBilled ));
			$bill['totalPayment'] = $totalPayment['amount'];
			//balance Month-2
			$bill['balanceM2'] = 0;
			$balanceM2 = $this->Balance_model->get_balance_where_unique(array('user_id' => $userId, 'YEAR(date)' => $yearPrevBill, 'MONTH(date)' => $monthPrevBill ));
		    if (isset($balanceM2['debt'])) {
				$bill['balanceM2'] = $balanceM2['debt'];
			}			
			//resa Month -1
			$bill['resaM1'] = $bill['children']['total']['costResa'];
			//dep Month -2
			$bill['depM2'] = $bill['children']['total']['costDep'];
			
			$bill['restToPay'] = round($bill['totalPayment'] + $bill['balanceM2'] - $bill['depM2'] - $bill['resaM1']);
			
		}
		
		return $bill;
	}
	
}
?>
