<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Map extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('status') != "kusam") {
            redirect(base_url('auth'));
        }

        $this->load->library('form_validation');
        $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->helper('idr_helper');
    }

    public function geozones()
    {
        $data['title'] = "Efms | Maps";

        // echo base_url('v1/api/traxroot/objectsMerge');
        // die;
        
        $this->render_page('main/map/map', $data);
    }
}
