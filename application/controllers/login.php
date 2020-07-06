<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
//		$this->output->enable_profiler(TRUE);

		$this->load->model('User_model');
		$this->load->model('Resa_model');
		$this->load->library('form_validation');

		$data['title'] = 'Login';

		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|valid_email');
		$this->form_validation->set_rules('pass', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('login', 'Login', 'callback_username_not_exists');
		 
		if($this->form_validation->run() == FALSE) {
			//Field validation failed.  User redirected to login page
			$this->load->view('templates/header_login', $data);
			$this->load->view('user/viewLogin', $data);
			$this->load->view('templates/footer');
		} else {
			//Go to user area
			$user = $this->session->userdata('user');
			redirect('user/'.$user["id"], 'refresh');
		}

	}

	/*
	 * Form validation callback functions
	 */

	function username_not_exists($password) {

		//Field validation succeeded.  Validate against database
		$username = $this->input->post('username');
		$password = $this->input->post('pass');

		//query the database
		$result = $this->User_model->login($username, $password);
		if($result) {
			return TRUE;
		} else {
			$this->form_validation->set_message('username_not_exists', 'Authentification incorrect'.$result);
			return FALSE;
		}
	}
}
?>