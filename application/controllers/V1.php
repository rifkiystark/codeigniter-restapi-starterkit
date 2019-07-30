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
	function register_post() {
		$data['email'] = $this->post('email');
		$data['name'] = $this->post('name');
		$data['telp'] = $this->post('telp');
		$data['password'] = $this->post('password');

		$tokenData['email'] = $data['email'];
		$tokenData['password'] = $data['password'];

		if ($this->Usermodel->register($data)){
			$token = AUTHORIZATION::generateToken($tokenData);
			$response =[
				'status' => 200,
				'token' => $token,
				'msg' => 'Request Successful !'
			];
			$this->response($response, 200);
		} else {
			$response = [
				'status' => 401,
				'msg'	=> 'Request Failed !'
			];
			$this->response(["token" => "000"], 401);
		}
	}

	private function login_post(){
		$data = $this->verify_request();
		$this->response($data, 200);
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
				"email"	=> $data->email,
				"password" => $data->password
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
