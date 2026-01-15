<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Manila');
		ini_set('display_errors', 'off');
	}

	function render_page_login($content, $data = NULL)
	{
		$data['header']  = $this->load->view('auth/header', $data, TRUE);
		$data['content'] = $this->load->view($content, $data, TRUE);
		$data['footer']  = $this->load->view('auth/footer', $data, TRUE);

		$this->load->view('auth/index', $data);
	}

	function render_page($content, $data = NULL)
	{
		$data['header']			= $this->load->view('main/header', $data, TRUE);
		$data['topbar']			= $this->load->view('main/topbar', $data, TRUE);
		$data['sidebar']		= $this->load->view('main/sidebar', $data, TRUE);
		$data['content']		= $this->load->view($content, $data, TRUE);
		$data['ourjs'] 			= $this->load->view('main/ourjs', $data, TRUE);
		$data['footer'] 		= $this->load->view('main/footer', $data, TRUE);

		$this->load->view('main/index', $data);
	}

	function render_page_cashier($content, $data = NULL)
	{
		$data['header']			= $this->load->view('main/header', $data, TRUE);
		$data['topbar']			= $this->load->view('main/topbar', $data, TRUE);
		$data['sidebar']		= $this->load->view('main/sidebar', $data, TRUE);
		$data['content']		= $this->load->view($content, $data, TRUE);
		$data['footer'] 		= $this->load->view('main/footer', $data, TRUE);

		$this->load->view('main/index', $data);
	}
}