<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
// require APPPATH . '/helpers/authorization_helper.php';
// require APPPATH . '/helpers/Validator.php';
// First, run 'composer require pusher/pusher-php-server'

require '././vendor/autoload.php';



class Guru extends CI_Controller
{

	use REST_Controller {
		REST_Controller::__construct as private __resTraitConstruct;
	}


	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->__resTraitConstruct();
		$this->load->model(array('GuruModel', 'MasterSiswaModel', 'JadwalModel', 'KelasModel', 'SiswaModel', 'MasterKehadiranModel', 'KehadiranModel'));
		$this->load->helper(['jwt', 'authorization', 'Validator']);
		$this->days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
	}

	function index()
	{
		echo "wakwaw";
	}

	function upload_post()
	{
		$this->response(["Status" => 200], 200);
	}

	function login_post()
	{

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

		$dataLogin = $this->GuruModel->get_where($data);


		if ($dataLogin->num_rows() > 0) {
			$dataResponse['guru'] = [
				'email' => $dataLogin->row('email'),
				'nama' => $dataLogin->row('nama'),
				'jenkel' => $dataLogin->row('jenkel')
			];

			$dataToken['wali'] = $dataLogin->row('id_wali');

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

	function jadwal_get()
	{
		$hari = $this->get('hari');
		if ($hari) {

			$this->response(
				$this->JadwalModel->getJadwal(1, $hari)->result(),
				200
			);
		} else {
			$schedules = [];
			foreach ($this->days as $day) {
				$schedules[$day] = $this->JadwalModel->getJadwal(1, $day)->result();
			}
			$this->response(
				$schedules,
				200
			);
		}
	}

	function kelas_get()
	{
		$dataKelas = $this->KelasModel->getKelas(1)->result();
		$kelas['kelas'] = [];
		foreach ($dataKelas as $kl) {

			array_push($kelas['kelas'], array("id" => $kl->id_kelas, "kelas" => $kl->kelas));
		}
		$this->response(
			$kelas,
			200
		);
	}

	function siswa_get()
	{
		$idKelas = $this->get('kelas');


		$dataSiswa = $this->SiswaModel->getSiswa($idKelas)->result();
		$this->response($dataSiswa, 200);
	}

	function kehadiran_post()
	{
		$idMapel = $this->post('mapel');
		$masterKehadiran = array(
			'id_mapel' => 1,
			'tanggal' => date('Y-m-d')
		);


		$kehadiran = json_decode($this->post('kehadiran'));
		foreach ($kehadiran->data as $khdrn) {
			if (!(isset($khdrn->id_siswa) && isset($khdrn->kehadiran))) {
				$response = array(
					"msg" => "request tidak valid"
				);
				$this->response($response, 400);
				exit();
			}
		}

		if ($this->MasterKehadiranModel->insert($masterKehadiran) == 1) {
			$idMasterKehadiran = $this->MasterKehadiranModel->get_where($masterKehadiran)->row('id_master_kehadiran');
		}

		foreach ($kehadiran->data as $khdrn) {
			$dataKehadiran = array(
				'id_master_kehadiran' => $idMasterKehadiran,
				'id_siswa' => $khdrn->id_siswa,
				'kehadiran' => $khdrn->kehadiran
			);

			$this->KehadiranModel->insert($dataKehadiran);
		}

		$response = array(
			'status' => 'success',
			'msg' => 'Kehadiran berhasil di kirim',
			'data' => array()
		);
		$this->response($response, 400);
	}
}
