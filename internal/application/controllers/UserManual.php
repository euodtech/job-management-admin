<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserManual extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('user_manual/index');
	}
}
