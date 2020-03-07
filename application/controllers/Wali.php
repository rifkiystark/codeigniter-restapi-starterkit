<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
// require APPPATH . '/helpers/authorization_helper.php';
// require APPPATH . '/helpers/Validator.php';
// First, run 'composer require pusher/pusher-php-server'

require '././vendor/autoload.php';



class Wali extends CI_Controller
{

	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}


	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model(array('WaliModel', 'MasterSiswaModel', 'SiswaModel', 'KelasModel', 'MasterKelasModel'));
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
		$dataResponse['wali'] = [
			'email' => $dataLogin->row('email'),
			'nama' => $dataLogin->row('nama'),
			'jenkel' => $dataLogin->row('jenkel')
		];
		$dataResponse['siswa'] = [];
		$dataToken['wali'] = $dataLogin->row('id_wali');
		$dataToken['siswa'] = [];
		if ($dataLogin->num_rows() > 0) {
			$where = [
				'id_wali' => $dataLogin->row('id_wali')
			];
			$masterSiswaList = $this->MasterSiswaModel->get_where($where)->result();
			foreach ($masterSiswaList as $masterSiswa) {
				$siswa = $this->SiswaModel->get_where(
					array(
						'id_siswa' => $masterSiswa->id_master_siswa
					)
				);

				$kelas = $this->KelasModel->get_where(array("id_kelas" => $siswa->row('id_kelas')));

				$dataSiswa['nama'] = $masterSiswa->nama;
				$dataSiswa['nis'] = $masterSiswa->nis;
				$dataSiswa['tahun_ajaran'] = $kelas->row("tahun_ajaran");
				$dataSiswa['kelas'] = $this->MasterKelasModel->get_where(array('id_master_kelas' => $kelas->row('id_master_kelas')))->row('kelas');
				// echo json_encode($dataSiswa);
				// exit();




				array_push($dataToken['siswa'], $masterSiswa->id_master_siswa);

				// unset($siswa->id_master_siswa);
				// unset($siswa->id_wali);

				array_push($dataResponse['siswa'], $dataSiswa);
			}

			$token = AUTHORIZATION::generateToken($dataToken);
			$dataResponse['token'] = $token;
			$this->response($dataResponse, 200);
		} else {
			$response = [
				'status' => 'Failed',
				'msg' => 'Email atau Password salah'
			];
			$this->response($response, 400);
		}
	}
}
