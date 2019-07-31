<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class V1 extends CI_Controller {

	use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
	function __construct()
    {
        // Construct the parent class
        parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model(array('Usermodel'));
		$this->load->helper(['jwt', 'authorization']);
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

	function validation_input($config, $data){
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules($config);

		if($this->form_validation->run()==FALSE){
			$response = array(
				'status' => 400,
				'error' => $this->form_validation->error_array()
			);
			$this->response($response, 400);
			exit();
		}
	}


	function register_post() {
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
		$data['password'] = md5($this->post('password'));

		if ($this->Usermodel->register($data)){
			$response =[
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

	private function login_post(){
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
		if ($user->num_rows() == 1){
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

	function cektoken_post(){
		$data = $this->verify_request();
		echo json_encode($data);
	}
	
	private function verify_request() {
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
					"userId"=> $data->userId,
					"email" => $data->email
				];
				if ($this->Usermodel->select_user($user)->num_rows() == 1){
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
}
