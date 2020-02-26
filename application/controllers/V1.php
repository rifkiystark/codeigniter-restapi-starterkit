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
	}
}
