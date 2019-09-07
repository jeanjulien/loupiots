<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class report extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('Resa_model');
        $this->load->model('Calendar_model');
        $this->load->model('User_model');
        $this->load->model('Child_model');
        $this->load->model('Class_model');
        $this->load->model('Period_model');
        $this->load->model('Days_model');
        $this->load->model('Payment_model');
        $this->load->model('Cost_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('dob');
        $this->load->library('form_validation');
        
    }
    
    function index() {
    }
    
    public function classroomCall() {
        //$this->output->enable_profiler(TRUE);
        
        $data['title'] = "Feuille d'appel";
        
        //check access rights
        $data['loggedId'] = $this->session->userdata('id');
        if (!isset($data['loggedId']) || !is_numeric($data['loggedId']) ) {
            show_404();
        }
        $data['loggedPrivilege'] = $this->session->userdata('privilege');
        if ($data['loggedPrivilege'] < 2) {
            show_404();
        }
        
        $days = $this->Calendar_model->getDaysOfWeeks($this->input->post('weekCall'));
        
        $classes = $this->Class_model->get_class();
        $data["startDate"]=current($days);
        
        $dayLine = "<tr>";
        foreach( $days as $daysName => $dateStr ) {
            $dayLine .= "<td>$daysName</td>";
            $data["endDate"]=$dateStr;
        }
        $dayLine .= "</tr>";
        
        $outputPM = $outputAM = "";
        foreach ($classes as $class) {
            $outputPM .= "<table border=1 width='80%'><tr>";
            $outputPM .= "<tr><td colspan='".sizeof($days)."'>".$class['class']."</td></tr>";
            $outputPM .= $dayLine;
            $outputPM .= "<tr>";
            
            $outputAM .= "<table border=1 width='80%'><tr>";
            $outputAM .= "<tr><td colspan='".sizeof($days)."'>".$class['class']."</td></tr>";
            $outputAM .= $dayLine;
            $outputAM .= "<tr>";
            
            foreach( $days as $daysName => $dateStr ) {
                $resasPM = $this->Resa_model->getClassroomCallPerDay($dateStr, $class["id"], "PM");
                $outputPM .= "<td>";
                foreach( $resasPM as $resaPM ) {
                    $outputPM .= $resaPM["childName"]." ".$resaPM["name"]." ".substr($resaPM["time"], 0, 5);
                    $outputPM .= "<br>";
                }
                $outputPM .= "</td>";
                
                $resasAM = $this->Resa_model->getClassroomCallPerDay($dateStr, $class["id"], "AM");
                $outputAM .= "<td>";
                foreach( $resasAM as $resaAM ) {
                    $outputAM .= $resaAM["childName"]." ".$resaAM["name"]." ".substr($resaAM["time"], 0, 5);
                    $outputAM .= "<br>";
                }
                $outputAM .= "</td>";
                
            }
        }
        $outputFoot = "</tr>";
        $outputFoot .= "</table>";
        $outputFoot .= "<div class='holder_content_separator'></div>";
        
        $data["outputPM"] = $outputPM.$outputFoot;
        $data["outputAM"] = $outputAM.$outputFoot;
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewClassroomCall', $data);
        $this->load->view('templates/footer');
    }
    
    public function weeklySummary() {
//        $this->output->enable_profiler(TRUE);
        
        $data['title'] = "Recapitulatif par semaine";
        
        //check access rights
        $data['loggedId'] = $this->session->userdata('id');
        if (!isset($data['loggedId']) || !is_numeric($data['loggedId']) ) {
            show_404();
        }
        $data['loggedPrivilege'] = $this->session->userdata('privilege');
        if ($data['loggedPrivilege'] < 2) {
            show_404();
        }
        
        //initialisation
        $weekCall = $this->input->post('weekCall');
        $onlyActive = $this->input->post('onlyActive');
        if ($weekCall=="") {
            $onlyActive = TRUE;
            $weekCall = date("d-m-Y");
        }
        $data['onlyActive'] = $onlyActive;
        $days = $this->Calendar_model->getDaysOfWeeks($weekCall);
        $data["startDate"]=current($days);
        
        $periods = $this->Days_model->get_daysPeriods();
        $users = $this->Child_model->get_fullChildren($onlyActive);
        $data["users"] = $users;
        $numCol=1;
        
        $data["output"] = "<table border='1px' width='100%'>\n";
        
        //Days title row
        $data["output"] .= "<tr>\n";
        $data["output"] .= "<td rowspan='2'>Enfants</td>\n";
        foreach( $days as $daysName => $dateStr ) {
            $data["output"] .= "<td colspan=".sizeof($periods[$daysName]).">$daysName</td>\n";
            $data["endDate"]=$dateStr;
        }
        
        $data["output"] .= "</tr>\n";
        
        //Period title row
        $data["output"] .= "<tr>\n";
        foreach( $days as $daysName => $dateStr ) {
            $pmPeriodsSize[$daysName] = $amPeriodsSize[$daysName] = 0;
            foreach ($periods[$daysName] as $period) {
                if ("PM"==$period["type"]) {
                    $time=explode(":", $period["stop_time"]);
                    $data["output"] .= "<td>-".$time[0].":".$time[1]."</td>\n";
                    $pmPeriodsSize[$daysName]++;
                } else {
                    $time=explode(":", $period["start_time"]);
                    $data["output"] .= "<td bgcolor='#bbb'>".$time[0].":".$time[1]."-</td>\n";
                    $amPeriodsSize[$daysName]++;
                }
                $numCol++;
            }
        }
        $data["output"] .= "</tr>\n";
        
        foreach( $days as $daysName => $dateStr ) {
            $totalAM[$daysName] = $totalPM[$daysName] = 0;
            $date = date("Y-m-d", strtotime($dateStr));
            
            $resas = $this->Resa_model->get_resa_where(array('date' => $date));
            foreach ($resas as $r) {
                $periodId = $r["period_id"];
                $childId = $r["child_id"];
                $resa[$date][$childId][$periodId] = 1;
            }
        }
        
        //Table body
        foreach ($users as $user) {
            $userName = isset($user['user_name']) ? $user['user_name'] : NULL;
            //User row title
            $data["output"] .= "<tr>\n";
            $data["output"] .= "<td bgcolor='#bbb' colspan='$numCol'>&nbsp;&nbsp;&nbsp;".$userName."</td>\n";
            $data["output"] .= "</tr>\n";
            //Child row
            if (array_key_exists('children', $user)) {
                $children = $user["children"];
                foreach ($children as $child) {
                    $data["output"] .= "<tr>\n";
                    $data["output"] .= "<td>".$child["name"]."</td>\n";
                    $childId = $child["id"];
                    
                    foreach( $days as $daysName => $dateStr ) {
                        $date = date("Y-m-d", strtotime($dateStr));
                        $addAM = $addPM = 0;
                        
                        foreach ($periods[$daysName] as $period) {
                            $periodId = $period["periodId"];
                            
                            if ("PM"==$period["type"]) {
                                $bgColor="";
                            } else {
                                $bgColor="bgcolor='#bbb'";
                            }
                            if (isset($resa[$date][$childId][$periodId])) {
                                $data["output"] .= "<td $bgColor align='center'>X</td>\n";
                                if ("PM"==$period["type"]) {
                                    $addPM=1;
                                } else {
                                    $addAM=1;
                                }
                                if(isset($totalPeriod[$daysName][$periodId])) {
                                    $totalPeriod[$daysName][$periodId] += 1;
                                } else {
                                    $totalPeriod[$daysName][$periodId] = 1;
                                }
                            } else {
                                $data["output"] .= "<td $bgColor>&nbsp;</td>\n";
                            }
                        }
                        $totalAM[$daysName] += $addAM;
                        $totalPM[$daysName] += $addPM;
                    }
                    $data["output"] .= "</tr>\n";
                }
            }
        }
        
        // Total period
        $data["output"] .= "<tr>\n";
        $data["output"] .= "<td colspan='$numCol'>&nbsp;</td>\n";
        $data["output"] .= "</tr>\n";
        
        $data["output"] .= "<tr>\n";
        $data["output"] .= "<td rowspan='2'>&nbsp;</td>\n";
        foreach( $days as $daysName => $dateStr ) {
            foreach ($periods[$daysName] as $period) {
                if ("PM"==$period["type"]) {
                    $bgColor="";
                } else {
                    $bgColor="bgcolor='#bbb'";
                }
                $periodId=$period["periodId"];
                if(isset($totalPeriod) && isset($totalPeriod[$daysName])) {
                    $data["output"] .= "<td $bgColor align='center'>".$totalPeriod[$daysName][$periodId]."</td>\n";
                    
                } else {
                    $data["output"] .= "<td $bgColor align='center'>&nbsp;</td>\n";
                }
            }
        }
        $data["output"] .= "</tr>\n";
        
        // Total
        $data["output"] .= "<tr>\n";
        
        foreach( $days as $daysName => $dateStr ) {
            $data["output"] .= "<td bgcolor='#bbb' align='center' colspan='".$amPeriodsSize[$daysName]."'>".$totalAM[$daysName]."</td>\n";
            $data["output"] .= "<td align='center' colspan='".$pmPeriodsSize[$daysName]."'>".$totalPM[$daysName]."</td>\n";
        }
        $data["output"] .= "</tr>\n";
        
        $data["output"] .= "</table>\n";
        $data["output"] .= "<div class='holder_content_separator'></div>";
        
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewWeeklySummary', $data);
        $this->load->view('templates/footer');
    }
    
    public function userCalendar($id = null, $year = null, $month = null) {
        $data['title'] = 'Calendrier des reservations';
        
        if (!isset($id) || ($this->session->userdata('id')!=$id && $this->session->userdata('privilege')<2)) {
            show_404();
        }
        
        if (!isset($year) || !isset($month)) {
            $nextMonth = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
            $year=date("Y", $nextMonth);
            $month=date("m", $nextMonth);
        }
        $data['year']=$year;
        $data['month']=$month;
        $data['user'] = $this->User_model->get_users(TRUE, $id);
        
        $this->Calendar_model->init($id);
        
        $this->load->library('calendar', $this->Calendar_model->conf);
        $cal_data = $this->Calendar_model->get_calendar_data($id, $year, $month);
        $data['output']=$this->calendar->generate($year, $month, $cal_data, TRUE);
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewUserCalendar', $data);
        $this->load->view('templates/footer');
        
    }
    
    public function paymentHistory($userId = null, $year = null, $month = null) {
        $this->output->enable_profiler(TRUE);
        
        //check access rights
        $data['loggedId'] = $this->session->userdata('id');
        if (!isset($data['loggedId']) || !is_numeric($data['loggedId']) ) {
            show_404();
        }
        $data['loggedPrivilege'] = $this->session->userdata('privilege');
        if ($this->session->userdata('id')!=$userId && $this->session->userdata('privilege')<2) {
            show_404();
        }
        
        //initialisation
        if (isset($userId) && $userId!='') {
            $data['userId'] = $userId;
        } else {
            $data['userId'] = $this->input->post('selId');
        }
        if ($data['loggedPrivilege'] >= 2) {
            $data['usersOption'] = $this->User_model->get_option_users();
            $selId = $this->input->post('selId');
        } else {
            $data['usersOption'] = $this->User_model->get_option_users($data['loggedId']);
        }
        if (!isset($year) || !isset($month)) {
            $nextMonth = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
            $year=date("Y", $nextMonth);
            $month=date("m", $nextMonth);
        }
        
        $data['title'] = 'Liste des paiements';
        
        $curYear=$year;
        for ($i = $month; $i >= ($month-13); $i--) {
            if ($i>=1) {
                $curMonth=$i;
            } else {
                $curMonth=12+$i;
                $curYear=$year-1;
            }
            $curDate = strtotime( $curYear."-".$curMonth."-01" );
            $data['dates'][$i]['month'] = date("m", $curDate);
            $data['dates'][$i]['year'] = date("Y", $curDate);
            $where = array('user_id'=>$data['userId'], 'YEAR(month_paided)' => $curYear, 'MONTH(month_paided)' => $curMonth);
            $data['dates'][$i]['payments'] = $this->Payment_model->get_payment_where($where);
            $data['dates'][$i]['monthlyStatus'] = $this->Cost_model->getCost($curYear, $curMonth, $data['userId']);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewFamilyPayment', $data);
        $this->load->view('templates/footer');
    }
    
    public function cheque() {
        //$this->output->enable_profiler(TRUE);
        
        $data['title'] = "Remise de cheque";
        
        //check access rights
        $data['loggedId'] = $this->session->userdata('id');
        if (!isset($data['loggedId']) || !is_numeric($data['loggedId']) ) {
            show_404();
        }
        $data['loggedPrivilege'] = $this->session->userdata('privilege');
        if ($data['loggedPrivilege'] < 3) {
            show_404();
        }
        
        //initialisation
        $year = $this->input->get_post('year');
        $month = $this->input->get_post('month');
        if ($month=="" || $year=="") {
            $year=date("Y");
            $month=date("n");
        }
        $data['month'] = $month;
        $data['year'] = $year;
        
        $where = array('type'=>"Cheque", 'status'=>"3", 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month);
        $data['cheques'] = $this->Payment_model->get_full_payment_where($where);
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewCheque', $data);
        $this->load->view('templates/footer');
        
    }
    
    public function balance() {
//        $this->output->enable_profiler(TRUE);
        
        $data['title'] = "Balance comptable";
        
        //check access rights
        $data['loggedId'] = $this->session->userdata('id');
        if (!isset($data['loggedId']) || !is_numeric($data['loggedId']) ) {
            show_404();
        }
        $data['loggedPrivilege'] = $this->session->userdata('privilege');
        if ($data['loggedPrivilege'] < 3) {
            show_404();
        }
        
        //initialisation
        $year = $this->input->get_post('year');
        $month = $this->input->get_post('month');
        if ($month=="" || $year=="") {
            $year=date("Y");
            $month=date("n");
        }
        if ($month==1) {
            $prevMonth=12;
            $prevYear=$year-1;
        } else {
            $prevMonth=$month-1;
            $prevYear=$year;
        }
        
        $data['month'] = $month;
        $data['year'] = $year;
        
        //somme recu (paiement valide)
        $where = array('status'=>"3", 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month);
        $data['validated'] = $this->Payment_model->get_total_payment_where($where);
        
        //somme en cours (paiment declare ou recu)
        $where = array('YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month);
        $data['declared'] = $this->Payment_model->get_total_payment_where($where);
        
        //reservations dues
        //cout des resas du mois courant
        $resas= $this->Resa_model->get_full_resa_where(array('YEAR(date)' => $year, 'MONTH(date)' => $month, 'resa_type !=' => 3 ));

        $data['resa'] = $this->Resa_model->get_cost($resas);
        //cout des depassemants du mois precedant
        $depassementPrev= $this->Resa_model->get_full_resa_where(array('YEAR(date)' => $prevYear, 'MONTH(date)' => $prevMonth, 'resa_type' => 3 ));
        $data['depassementPrev'] = $this->Resa_model->get_cost($depassementPrev);
        
        $data["totalResa"]= $data['depassementPrev']['total'] + $data['resa']['total'] ;
        $data["rest"] = $data["totalResa"] - $data['validated']['amount'];
        
        //dettes mois precedent
        $lastMonthCosts = $this->Cost_model->get_cost_where(array('YEAR(month_paided)' => $prevYear, 'MONTH(month_paided)' => $prevMonth ));
        $data["debt"]=0;
        foreach ( $lastMonthCosts as $lastMonthCost ) {
            $data["debt"] += $lastMonthCost["debt"];
        }
        
        $data["totaldu"]= $data['debt'] + $data["rest"];
        
        $this->load->view('templates/header', $data);
        $this->load->view('report/viewBalance', $data);
        $this->load->view('templates/footer');
        
    }
    
}

?>