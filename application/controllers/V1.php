<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
// First, run 'composer require pusher/pusher-php-server'

require '././vendor/autoload.php';



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
		$this->load->model(array('Usermodel', 'Techniciansmodel','Verificationsmodel','Ordermodel'));
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
	
	function tespusher_post(){
		$options = array(
			'cluster' => 'ap1',
			'useTLS' => true
		);
		$pusher = new Pusher\Pusher(
			'86ae86846caf575fc32e',
			'22dfb0816724f170230f',
			'837299',
			$options
		);
		
		$data['message'] = 'hello world';
		$pusher->trigger('tes', 'my-event', $data);
	}
	
	function verifications_post(){
		$data['email'] = $this->post('email');
		$data['code'] = $this->post('code');
		
		$verifdata = $this->Verificationsmodel->select_where($data);
		if ($verifdata->num_rows() == 1){
			$dataupdate = array(
				'isVerifiedEmail' => true
			);
			$where = array(
				'email' => $data['email']
			);
			
			$this->Usermodel->update_user($where, $dataupdate);
			$this->Verificationsmodel->delete_where($data);
			$response = [
				'status' => 200,
				'message' => 'Request Succesed !'
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
			$unique_code = "";
			for ($i = 0; $i < $length; $i++) {
				$char = $characters[rand(0, $charactersLength - 1)];
				$unique_kode .= $char;
				$unique_code .= $char." ";
			}
			
			$verifdata = array (
				'email' => $data['email'],
				'code'	=> $unique_kode
			);
			
			$msg = '<html xmlns="http://www.w3.org/1999/xhtml"><head>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<title>Verify your email address</title>
			<style type="text/css" rel="stylesheet" media="all">
			/* Base ------------------------------ */
			*:not(br):not(tr):not(html) {
				font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
				-webkit-box-sizing: border-box;
				box-sizing: border-box;
			}
			body {
				width: 100% !important;
				height: 100%;
				margin: 0;
				line-height: 1.4;
				background-color: #F5F7F9;
				color: #839197;
				-webkit-text-size-adjust: none;
			}
			a {
				color: #414EF9;
			}
			
			/* Layout ------------------------------ */
			.email-wrapper {
				width: 100%;
				margin: 0;
				padding: 0;
				background-color: #F5F7F9;
			}
			.email-content {
				width: 100%;
				margin: 0;
				padding: 0;
			}
			
			/* Masthead ----------------------- */
			.email-masthead {
				padding: 25px 0;
				text-align: center;
				background: #fff;
			}
			.email-masthead_logo {
				max-width: 400px;
				border: 0;
			}
			.email-masthead_name {
				font-size: 16px;
				font-weight: bold;
				color: #839197;
				text-decoration: none;
				text-shadow: 0 1px 0 white;
			}
			
			/* Body ------------------------------ */
			.email-body {
				width: 100%;
				margin: 0;
				padding: 0;
				border-top: 1px solid #E7EAEC;
				border-bottom: 1px solid #E7EAEC;
				background-color: #FFFFFF;
			}
			.email-body_inner {
				width: 570px;
				margin: 0 auto;
				padding: 0;
			}
			.email-footer {
				width: 570px;
				margin: 0 auto;
				padding: 0;
				text-align: center;
			}
			.email-footer p {
				color: #839197;
			}
			.body-action {
				width: 100%;
				margin: 30px auto;
				padding: 0;
				text-align: center;
			}
			.body-sub {
				margin-top: 25px;
				padding-top: 25px;
				border-top: 1px solid #E7EAEC;
			}
			.content-cell {
				padding: 35px;
			}
			.align-right {
				text-align: right;
			}
			
			/* Type ------------------------------ */
			h1 {
				margin-top: 0;
				font-size: 19px;
				font-weight: bold;
				text-align: left;
			}
			h2 {
				margin-top: 0;
				font-size: 16px;
				font-weight: bold;
				text-align: left;
			}
			h3 {
				margin-top: 0;
				font-size: 14px;
				font-weight: bold;
				text-align: left;
			}
			p {
				margin-top: 0;
				font-size: 16px;
				line-height: 1.5em;
				text-align: left;
			}
			p.sub {
				font-size: 12px;
			}
			p.center {
				text-align: center;
			}
			
			/* Buttons ------------------------------ */
			.button {
				display: inline-block;
				width: 200px;
				background-color: #414EF9;
				border-radius: 3px;
				color: #ffffff;
				font-size: 24px;
				font-weight: bold;
				line-height: 45px;
				text-align: center;
				text-decoration: none;
				-webkit-text-size-adjust: none;
				mso-hide: all;
			}
			.button--green {
				background-color: #28DB67;
			}
			.button--red {
				background-color: #FF3665;
			}
			.button--blue {
				background-color: #827ffb;
			}
			
			/*Media Queries ------------------------------ */
			@media only screen and (max-width: 600px) {
				.email-body_inner,
				.email-footer {
					width: 100% !important;
				}
			}
			@media only screen and (max-width: 500px) {
				.button {
					width: 100% !important;
				}
			}
			</style>
			</head>
			<body>
			<table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
			<tbody><tr>
			<td align="center">
			<table class="email-content" width="100%" cellpadding="0" cellspacing="0">
			<!-- Logo -->
			<tbody><tr>
			<td class="email-masthead">
			<a class="email-masthead_name"><img src="https://gaweapi.azurewebsites.net/images/logo.png" style="width: 120px"></a>
			</td>
			</tr>
			<!-- Email Body -->
			<tr>
			<td class="email-body" width="100%">
			<table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
			<!-- Body content -->
			<tbody><tr>
			<td class="content-cell">
			<h1>Verify your email address</h1>
			<p><b>Hi, '.$data["name"].',</b><p/>
			<p>Thank you for signing up for Gawe! We`re excited to have you as an early user.</p>
			<table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0">
			<tbody><tr>
			<td align="center">
			<div>
			<div class="button button--blue">'.$unique_code.'</div>
			</div>
			</td>
			</tr>
			</tbody></table>
			<p>Thank you,<br>Gawe Team</p>
			<!-- Sub copy -->
			<table class="body-sub">
			<tbody><tr>
			<td>
			<p class="sub">if you don`t feel like registering on the gawe platform, just ignore this email
			</p>
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			<tr>
			<td>
			<table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0">
			<tbody><tr>
			<td class="content-cell">
			<p class="sub center">
			Bayanaka, Inc.
			<br>Lab Riset, Software Engineering, Institut Teknologi Telkom Purwokerto
			</p>
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			</tbody></table>
			</td>
			</tr>
			</tbody></table>
			
			</body></html>';
			
			$this->Verificationsmodel->insert($verifdata);
			$this->email($data['email'], $msg);
			
			$response = array(
				'status' => 200,
				'message' => 'Request Successful'
			);
			$this->response($response, 200);
		} else {
			$response = array(
				'status' => 400,
				'message' => 'Request Failed'
			);
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
		$this->email->from('ananda.rifkiy33@gmail.com', 'Gawe');
		//send multiple email
		$this->email->to($to);
		// Subject of email
		$this->email->subject("Verifikasi Akun Gawe");
		// Message in email
		$this->email->message($msg);
		// It returns boolean TRUE or FALSE based on success or failure
		$this->email->send(); 
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
		$data['password'] = md5($this->post('password'));
		
		$user = $this->Usermodel->select_user($data);
		if ($user->num_rows() == 1) {
			if(!$user->row('isVerifiedEmail')){
				$response = array(
					'status' => 401,
					'message' => 'Email not verified !',
				);
				$this->response($response, 401);
			} else {
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
			}
			
		} else {
			$response = array(
				'status' => 400,
				'message' => 'Email or Password did not match !'
			);
			$this->response($response, 400);
		}
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
			exit();
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
	
	function uploadimage($config, $filename, $field){
		
	}
	
	function order_post(){
		$img_name = [];
		$i = 0;
		$data_user = $this->verify_request();
		$config = [
			[
				'field' => 'description',
				'label' => 'Description',
				'rules' => 'required',
			],
		];
		$data = $this->input->post();
		$this->validation_input($config, $data);

		//	create orderCode
		$length = 10;
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$order_code = "";
		for ($i = 0; $i < $length; $i++) {
			$char = $characters[rand(0, $charactersLength - 1)];
			$order_code .= $char;
		}

		$configUpload = array(
			'upload_path' => "./orders/",
			'allowed_types' => "jpg|png|jpeg",
			'overwrite' => TRUE,
			'max_size' => "4096",
			'file_name' => $order_code."_1.png"
		);
		
		$this->load->library('upload', $configUpload);
		$this->upload->initialize($configUpload);
		
		if (!$this->upload->do_upload('photo1')) {
			$error['photo1'] = array('error' => $this->upload->display_errors());
		} else {
			$dataGambar = $this->upload->data();
			$img_name[$i] = $dataGambar['file_name'];
			$i++;
		}
		
		$configUpload = array(
			'upload_path' => "./orders/",
			'allowed_types' => "jpg|png|jpeg",
			'overwrite' => TRUE,
			'max_size' => "4096",
			'file_name' => $order_code."_2.png"
		);
		
		$this->load->library('upload', $configUpload);
		$this->upload->initialize($configUpload);
		
		if (!$this->upload->do_upload('photo2')) {
			$error['photo1'] = array('error' => $this->upload->display_errors());
		} else {
			$dataGambar = $this->upload->data();
			$img_name[$i] = $dataGambar['file_name'];
			$i++;
		}
		
		
		$configUpload = array(
			'upload_path' => "./orders/",
			'allowed_types' => "jpg|png|jpeg",
			'overwrite' => TRUE,
			'max_size' => "4096",
			'file_name' => $order_code."_3.png"
		);
		
		$this->load->library('upload', $configUpload);
		$this->upload->initialize($configUpload);
		
		if (!$this->upload->do_upload('photo3')) {
			$error['photo1'] = array('error' => $this->upload->display_errors());
		} else {
			$dataGambar = $this->upload->data();
			$img_name[$i] = $dataGambar['file_name'];
			$i++;
		}

		$order = array(
			'customerId' 	=> $data_user->userId,
			'description' 	=> $this->post('description'),
			'status'		=> 'Menunggu',
			'orderCode'		=> $order_code,
			'photos'		=> json_encode($img_name)
		);

		if ($this->Ordermodel->insert($order)){
			$response = array(
				'status'	=> 200,
				'message'   => 'Success',
				'data'		=> $order,
				'error'     => $error
			);
			$this->response($response, 200);
		} else {
			$response = array(
				'status'	=> 400,
				'message'   => 'Failed'
			);
			$this->response($response, 400);
		}
	}
}
