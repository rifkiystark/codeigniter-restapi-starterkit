<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
// require APPPATH . '/helpers/authorization_helper.php';
// require APPPATH . '/helpers/Validator.php';
// First, run 'composer require pusher/pusher-php-server'

require '././vendor/autoload.php';



class Admin extends CI_Controller
{

	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}


	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model(array('WaliModel'));
		$this->load->helper(['jwt', 'authorization', 'Validator']);
	}

	function index()
	{
		echo "wakwaw";
	}

	function upload_post()
	{
		header("Access-Control-Allow-Origin: *");
		$this->response(["Status" => 200], 200);
	}

	function login_post()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Expose-Headers: Content-Length, X-JSON");
		header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: *");

		$this->form_validation->set_data($this->post());
		$this->form_validation->set_rules(Validator::Login());

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status' => 'Failed',
				'msg' => $this->validation_errors()
			];
			$this->response($data, 400);
			exit();
		}
		$data['email'] = $this->post('email');
		$data['password'] = md5($this->post('password'));

		$dataLogin = $this->WaliModel->get_where($data);
		if ($dataLogin->num_rows() > 0) {

			$token = AUTHORIZATION::generateToken($data);
			$data['token'] = $token;
			$this->response($data, 200);
		} else {
			$response = [
				'status' => 'Failed',
				'msg' => 'Email atau Password salah'
			];
			$this->response($response, 400);
		}
	}
}
