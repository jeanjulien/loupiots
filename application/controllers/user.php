<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('Resa_model');
		$this->load->model('User_model');
		$this->load->model('Period_model');
		$this->load->model('Payment_model');
		$this->load->model('Cost_model');
		$this->load->helper('url');
		$this->load->helper('dob'); 
		$this->load->helper('form');
		$this->load->library('form_validation');
		
	}

	function index() {
		$data['title'] = '';

 		if($this->session->userdata('logged_in')) {
 			redirect('user/'.$this->session->userdata('id'), 'refresh');
 		} else {
 			$this->load->view('templates/header_login', $data);	
			$this->load->view('user/viewLogin');
			$this->load->view('templates/footer');
 		}
	}
	
	public function create() {
		//$this->output->enable_profiler(TRUE);
		
		$data['title'] = 'Ajouter une famille';
		
		//get session data & init form
		$loggedId = $this->session->userdata('id');
		if (!isset($loggedId) || !is_numeric($loggedId) ) {
			show_404();
		}
		$data['loggedPrivilege'] = $this->session->userdata('privilege');
		if ($data['loggedPrivilege'] == 3) {
			$data['users'] = $this->User_model->get_users();
			$data['usersOption'] = $this->User_model->get_option_users();
			$selId = $this->input->post('selId');
			if (isset($selId) && $selId != "") {
				$data['userId'] = $this->input->post('selId');
			} else {
				$data['userId'] = $loggedId;
			}
		} else {
			$data['usersOption'] = $this->User_model->get_option_users($loggedId);
			$data['userId'] = $loggedId;
			$data['users'] = $this->User_model->get_users(FALSE, $data['userId']);
		}
		$data['user'] = $this->User_model->get_users(FALSE, $data['userId']);
		$data['privilegeOptions'] = array(1=>'Utilisateur', 2=>'Animateur', 3=>'Administrateur');
		
		//get form posted values
		$create = $this->input->post('create');
		$select = $this->input->post('select');
		$update = $this->input->post('update');

		$mail = $this->input->post('mail');
		$name = $this->input->post('name');
		$privilege = $this->input->post('privilege');
		
		//check form data & set default values
		if (isset($select) && $select!="") {
			$data['userId'] = $this->input->post('selId');
			$data['user'] = $this->User_model->get_users(FALSE, $data['userId']);
		} elseif (isset($create) && $create!="") {
			$data['userId'] = $this->input->post('selId');
			$this->form_validation->set_rules('mail', 'Mail', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('name', 'Nom', 'trim|required|xss_clean');
			$this->form_validation->set_rules('privilege', 'Privilege', 'required');
			$this->form_validation->set_rules('exists', 'Exists', 'callback_username_already_exists');
			$pass = "welcome.".$this->input->post('mail');
			if ($this->form_validation->run() !== FALSE) {
				$data['userId'] = $this->User_model->create($mail, $pass, $name, $privilege);
				$data['user'] = $this->User_model->get_users(FALSE, $data['userId']);
			}
		} elseif (isset($update) && $update!="") {
			$data['userId'] = $this->input->post('selId');
			$this->form_validation->set_rules('mail', 'Mail', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('name', 'Nom', 'trim|required|xss_clean');
			$this->form_validation->set_rules('privilege', 'Privilege', 'required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[confPassword]|xss_clean');
			$this->form_validation->set_rules('confPassword', 'Password confirmation', 'trim|required|xss_clean');
			$pass = $this->input->post('password');
			if ($this->form_validation->run() !== FALSE) {
				$data['userId'] = $this->User_model->update($data['userId'], $mail, $pass, $name, $privilege);
				$data['user'] = $this->User_model->get_users(FALSE, $data['userId']);
			}
		}
		
		//go back to user page
		$this->load->view('templates/header', $data);	
		$this->load->view('admin/viewCreateUser', $data);
		$this->load->view('templates/footer');
		
	}
	
	public function viewUser($id = null, $year = null, $month = null) {
//		$this->output->enable_profiler(TRUE);
			
		if (!isset($id) || ($this->session->userdata('id')!=$id && $this->session->userdata('privilege')<2)) {
			show_404();
		}
		
		$selId = $this->input->post('selId');
		if (isset($selId) && $selId != "") {
			$data['userId'] = $this->input->post('selId');
		} else {
			$data['userId'] = $id;
		}
		
		$data['user'] = $this->User_model->get_users(TRUE, $data['userId']);
		
		if (!isset($year) && !isset($month)) {
			$year = $this->input->post('year');
			$month = $this->input->post('month');
			if ($month=="" || $year=="") {
				$nextMonth = mktime(0, 0, 0, date("m")+1, date("d"), date("Y"));
				$year=date("Y", $nextMonth);
				$month=date("n", $nextMonth);
			}
		}
		setlocale(LC_ALL, 'fr_FR','fra');
		$curMonth = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$data['getData']['curMonthYear']= mktime(0, 0, 0, date("n", $curMonth), 1, date("Y", $curMonth));
		$data['getData']['viewMonthYear'] = mktime(0, 0, 0, $month, 1, $year);

		$data['title'] = $data['user']['name'];
		$data['getData']['year']=$year;
		$data['getData']['month']=$month;
		$data['getData']['monthStr']=strftime("%B", mktime(0, 0, 0, $month, 10, $year));
		$data['getData']['month-1Str']=strftime("%B", mktime(0, 0, 0, $month-1, 10, $year));
		$data['getData']['month-2Str']=strftime("%B", mktime(0, 0, 0, $month-2, 10, $year));
		$data['getData']['user_id']=$data['userId'];
		
		$data['usersOption'] = $this->User_model->get_option_users();
		
		$data['payment'] = array();
		$payments = $this->Payment_model->get_payment_where(array('user_id' => $data['userId'], 'YEAR(month_paided)' => $year, 'MONTH(month_paided)' => $month ));
		foreach ($payments as $curPayment) {
			$datePayment = strtotime($curPayment['month_paided']);
			$curPayment['datePaid'] = strftime("%B %Y", $datePayment);
			$data['payment'][] = $curPayment;
		}
		$data['bill'] = $this->Resa_model->getBill($data['userId'], $year, $month); 

		$this->load->view('templates/header', $data);
		$this->load->view('user/viewUser', $data);
		$this->load->view('templates/footer');
	}
	
	function logout() {
		$this->session->sess_destroy();
		redirect('login/', 'refresh');
	}

	
	
	/*
	 * Form validation callback functions
	*/
	function username_already_exists($mail) {
		$mail = $this->input->post('mail');
		//query the database
		$where = array('mail'=>$mail);
		$result = $this->User_model->get_users_where($where);
	
		if($result) {
			$this->form_validation->set_message('username_already_exists', 'La famille existe deja!');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	
}

?>