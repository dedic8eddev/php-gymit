<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends Public_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
	}

	public function index()
	{
	    if($this->ion_auth->logout()){
			// Destroy sess & redirect to login
			session_destroy();
			redirect('login');
		}
	}
}
