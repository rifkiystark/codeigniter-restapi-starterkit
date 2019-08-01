<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class V1 extends CI_Controller
{

	use REST_Controller {
	REST_Controller::__construct as private __resTraitConstruct;
	}

	
	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model(array('Usermodel', 'Techniciansmodel','Verificationsmodel'));
		$this->load->helper(['jwt', 'authorization']);
		$this->base_url = "http://localhost//gawe-api/";
	}


	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	function validation_input($config, $data)
	{
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules($config);

		if ($this->form_validation->run() == FALSE) {
			$response = array(
				'status' => 400,
				'error' => $this->form_validation->error_array()
			);
			$this->response($response, 400);
			exit();
		}
	}

	function verifications_post(){
		$data['userId'] = $this->post('userid');
		$data['code'] = $this->post('code');
		
		$verifdata = $this->Verificationsmodel->select_where($data);
		if ($verifdata->num_rows() == 1){
			$dataupdate = array(
				'isVerifiedEmail' => true
			);
			$where = array(
				'userId' => $data['userId']
			);

			$this->Usermodel->update_user($where, $dataupdate);
			$response = [
				'status' => 200,
				'message' => 'Request Failed !'
			];
			$this->response($response, 200);

		} else {
			$response = [
				'status' => 400,
				'message' => 'Request Failed !'
			];
			$this->response($response, 400);
		}
	}


	function register_post()
	{
		$config = [
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|max_length[50]|valid_email|is_unique[users.email]',
				'errors' => [
					'required' => 'You must provide an email',
					'max_length' => 'Maximum Email length is 50 characters',
					'valid_email' => 'Email not valid',
					'is_unique' => 'Email already taken, use another email.'
				],
			],
			[
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'required|max_length[50]',
				'errors' => [
					'required' => 'You must provide a name.',
					'max_length' => 'Maximum Name length is 50 characters',
				],
			],
			[
				'field' => 'telp',
				'label' => 'Telp',
				'rules' => 'required|is_natural|max_length[15]|is_unique[users.telp]',
				'errors' => [
					'required' => 'You must provide a telephone number.',
					'is_natural' => 'Only contains numbers',
					'max_length' => 'Maximum Telp length is 15 characters',
					'is_unique' => 'Telp Number already taken, use another telp number.',
				],
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|min_length[8]',
				'errors' => [
					'required' => 'You must provide a Password.',
					'min_length' => 'Minimum Password length is 8 characters.',
				],
			],
		];

		$data = $this->input->post();

		$this->validation_input($config, $data);

		// if valid input
		$data['email'] = $this->post('email');
		$data['name'] = $this->post('name');
		$data['telp'] = $this->post('telp');
		$data['isVerifiedEmail'] = false;
		$data['password'] = md5($this->post('password'));

		if ($this->Usermodel->register($data)) {

			$length = 6;
			$characters = '0123456789';
			$charactersLength = strlen($characters);
			$unique_kode = '';
			for ($i = 0; $i < $length; $i++) {
				$unique_kode .= $characters[rand(0, $charactersLength - 1)];
			}
			
			$iduser = $this->Usermodel->select_user($data)->row('userId');
			$verifdata = array (
				'userId' => $iduser,
				'code'	=> $unique_kode
			);

			$this->Verificationsmodel->insert($verifdata);
			$this->email($data['email'], $unique_kode);
			
			$response = [
				'status' => 200,
				'userId' => $iduser,
				'message' => 'Request Successful !'
			];
			$this->response($response, 200);
		} else {
			$response = [
				'status' => 400,
				'message' => 'Request Failed !'
			];
			$this->response($response, 400);
		}
	}

	function email($to, $msg){
		$config = [
			'mailtype'  => 'html',
			'charset'   => 'utf-8',
			'protocol'  => 'smtp',
			'smtp_host' => 'ssl://smtp.gmail.com',
			'smtp_user' => 'ananda.rifkiy33@gmail.com',    // Ganti dengan email gmail kamu
			'smtp_pass' => 'wakwaw123',      // Password gmail kamu
			'smtp_port' => 465,
			'crlf'      => "\r\n",
			'newline'   => "\r\n"
		];
		// Load email library and passing configured values to email library
		$this->load->library('email', $config);
		// Sender email address
		$this->email->from('ananda.rifkiy33@gmail.com', 'ananda.rifkiy33@gmail.com');
		//send multiple email
		$this->email->to($to);
		// Subject of email
		$this->email->subject("Kode OTP Registrasi GAWE");
		// Message in email
		$this->email->message($msg);
		// It returns boolean TRUE or FALSE based on success or failure
		$this->email->send(); 
		echo $this->email->print_debugger();
	}


	private function login_post()
	{
		$config = [
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|max_length[50]|valid_email',
				'errors' => [
					'required' => 'We need both username and password',
					'max_length' => 'Maximum Email length is 50 characters',
					'valid_email' => 'Email not valid',
				],
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required',
				'errors' => [
					'required' => 'You must provide a Password.',
				],
			],
		];

		$data = $this->input->post();

		$this->validation_input($config, $data);

		// if valid input
		$data['email'] = $this->post('email');
		$data['isVerifiedEmail'] = true;
		$data['password'] = md5($this->post('password'));

		$user = $this->Usermodel->select_user($data);
		if ($user->num_rows() == 1) {
			$tokenData = array(
				'userId' => $user->row()->userId,
				'email' => $user->row()->email,
			);
			$token = AUTHORIZATION::generateToken($tokenData);
			$response = array(
				'status' => 200,
				'message' => 'Login Successful !',
				'token' => $token
			);

			$this->response($response, 200);
		} else {
			$response = array(
				'status' => 400,
				'message' => 'Email or Password did not match !'
			);
			$this->response($response, 400);
		}
	}

	function cektoken_post()
	{
		$data = $this->verify_request();
		echo json_encode($data);
	}

	private function verify_request()
	{
		// Get all the headers
		$headers = $this->input->request_headers();
		// Extract the token
		$token = $headers['Authorization'];
		// Use try-catch
		// JWT library throws exception if the token is not valid
		try {
			// Validate the token
			// Successfull validation will return the decoded user data else returns false
			$data = AUTHORIZATION::validateToken($token);
			if ($data === false) {
				$status = 401;
				$response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
				$this->response($response, $status);
				exit();
			} else {
				$user = [
					"userId" => $data->userId,
					"email" => $data->email
				];
				if ($this->Usermodel->select_user($user)->num_rows() == 1) {
					return $data;
				} else {
					$status = 401;
					$response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
					$this->response($response, $status);
					exit();
				}
			}
		} catch (Exception $e) {
			// Token is invalid
			// Send the unathorized access message
			$status = 401;
			$response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
			$this->response($response, $status);
		}
	}

	function registertech_post()
	{

		//	gettechnicianId for validation
		$technicians = $this->Techniciansmodel->select();
		$technicianId = "";
		for ($i = 0; $i < $technicians->num_rows(); $i++) {
			$tmpId = (string) $technicians->result()[$i]->technicianId;
			$technicianId .= $tmpId . ",";
		}

		$config = [
			[
				'field' => 'type',
				'label' => 'Service Category',
				'rules' => 'required|is_numeric|in_list[' . $technicianId . ']',
				'errors' => [
					'required' => 'You must provide a service category'
				],
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|max_length[50]|valid_email|is_unique[users.email]',
				'errors' => [
					'required' => 'You must provide an email',
					'max_length' => 'Maximum Email length is 50 characters',
					'valid_email' => 'Email not valid',
					'is_unique' => 'Email already taken, use another email.'
				],
			],
			[
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'required|max_length[50]',
				'errors' => [
					'required' => 'You must provide a name.',
					'max_length' => 'Maximum Name length is 50 characters',
				],
			],
			[
				'field' => 'telp',
				'label' => 'Telp',
				'rules' => 'required|is_natural|max_length[15]|is_unique[users.telp]',
				'errors' => [
					'required' => 'You must provide a telephone number.',
					'is_natural' => 'Only contains numbers',
					'max_length' => 'Maximum Telp length is 15 characters',
					'is_unique' => 'Telp Number already taken, use another telp number.'
				],
			],
			[
				'field' => 'identityNumber',
				'label' => 'KTP Number',
				'rules' => 'required|is_natural|max_length[30]|is_unique[users.identityNumber]',
				'errors' => [
					'required' => 'You must provide a KTP number.',
					'is_natural' => 'Only contains numbers',
					'max_length' => 'Maximum KTP length is 30 characters',
					'is_unique' => 'KTP number already taken.'
				],
			],
			[
				'field' => 'identityPhoto',
				'label' => 'KTP Scan',
				'rules' => '',
				'errors' => [
					
				],
			],
			[
				'field' => 'balance',
				'label' => 'Balance',
				'rules' => 'required|is_numeric',
				'errors' => [
					'required' => 'You must provide a Password.',
				],
			],
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|min_length[8]',
				'errors' => [
					'required' => 'You must provide a Password.',
					'min_length' => 'Minimum Password length is 8 characters.',
				],
			],
		];

		$data = $this->input->post(); 
		$this->validation_input($config, $data);

		$configUpload = array(
			'upload_path' => "./identities/",
			'allowed_types' => "jpg|png|jpeg",
			'overwrite' => TRUE,
			'max_size' => "4096",
			'file_name' => $this->post('identityNumber')
		);

		$this->load->library('upload', $configUpload);
		$this->upload->initialize($configUpload);

		if (!$this->upload->do_upload('identityPhoto')) {
			$error = array('error' => $this->upload->display_errors());

			$this->response($error, 400);
			exit();
		} else {
			$dataGambar = $this->upload->data();
		}
		
		// if valid input
		$data['type'] = $this->post('type');
		$data['email'] = $this->post('email');
		$data['name'] = $this->post('name');
		$data['telp'] = $this->post('telp');
		$data['identityNumber'] = $this->post('identityNumber');
		$data['identityPhoto'] = $dataGambar['file_name'];
		$data['balance'] = $this->post('balance');
		$data['poin'] = 0;
		$data['isVerifiedEmail'] = true;
		$data['password'] = md5($this->post('password'));

		if ($this->Usermodel->registertech($data)) {
			$response = [
				'status' => 200,
				'message' => 'Request Successful !'
			];
			$this->response($response, 200);
		} else {
			$response = [
				'status' => 400,
				'message' => 'Request Failed !'
			];
			$this->response($response, 400);
		}
	}
}
