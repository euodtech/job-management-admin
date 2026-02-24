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
		$data['breadcrumbs'] = $this->_generate_breadcrumbs();

		$data['header']			= $this->load->view('main/header', $data, TRUE);
		$data['topbar']			= $this->load->view('main/topbar', $data, TRUE);
		$data['sidebar']		= $this->load->view('main/sidebar', $data, TRUE);
		$data['profile_modal']	= $this->load->view('main/profile_modal', $data, TRUE);
		$data['content']		= $this->load->view($content, $data, TRUE);
		$data['ourjs'] 			= $this->load->view('main/ourjs', $data, TRUE);
		$data['footer'] 		= $this->load->view('main/footer', $data, TRUE);

		$this->load->view('main/index', $data);
	}

	private function _generate_breadcrumbs()
	{
		$labels = array(
			'home'                  => 'Dashboard',
			'user-list'             => 'Rider',
			'company-list'          => 'Company',
			'customer-list'         => 'Customer',
			'line-interruption-job' => 'Line Interruption',
			'reconnection-job'      => 'Reconnection',
			'short-circuit-job'     => 'Short Circuit',
			'disconnection-job'     => 'Disconnection',
			'reschedule-job'        => 'Reschedule Job',
			'job-summary'           => 'Job Summary',
			'report-driver'         => 'Rider Report',
			'report-job'            => 'Job Report',
			'report-customer'       => 'Customer Report',
			'map'                   => 'Maps',
			'vehicle'               => 'Vehicle',
		);

		$segment = $this->uri->segment(1);
		$breadcrumbs = array();

		if ($segment === 'home' || !isset($labels[$segment])) {
			$breadcrumbs[] = array('label' => 'Dashboard', 'url' => '');
		} else {
			$breadcrumbs[] = array('label' => 'Dashboard', 'url' => base_url('home'));
			$breadcrumbs[] = array('label' => $labels[$segment], 'url' => '');
		}

		return $breadcrumbs;
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

	protected function audit_log($action, $details = [])
	{
		$logData = [
			'admin_id'   => $this->session->userdata('AdminID'),
			'action'     => $action,
			'ip'         => $this->input->ip_address(),
			'details'    => json_encode($details),
			'created_at' => date('Y-m-d H:i:s')
		];
		$this->db->insert('AuditLog', $logData);
	}
}