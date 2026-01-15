<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Get Token
require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Drivers extends ApiToken
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * GET /Drivers
     * Get list of drivers
     */
    public function getDrivers()
    {
        $url = 'https://connect.traxroot.com/api/Drivers';

        // $this->session->sess_destroy();
        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);

        // echo json_encode($this->session->userdata('api_access_token'));

        
    }

    /**
     * GET /Drivers/{id}
     * Get driver by ID
     * @param int $id
     */
    public function getDriverById($id = null)
    {
        if (!$id) {
            return $this->_badRequest('Missing driver ID');
        }

        $url = 'https://connect.traxroot.com/api/Drivers/' . intval($id);

        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);
    }

    /**
     * DELETE /Drivers/{id}
     * Delete driver by ID
     * @param int $id
     */
    public function deleteDriver($id = null)
    {
        if (!$id) {
            return $this->_badRequest('Missing driver ID');
        }

        $url = 'https://connect.traxroot.com/api/Drivers/' . intval($id);

        $res = $this->requestCurl($url, 'DELETE');

        $this->_respond($res);
    }

    /**
     * PUT /Drivers
     * Update/create driver. If id is not set, new driver will be created.
     */
    public function saveDriver()
    {
        // Read raw input (works for PUT/POST when sending JSON)
        $raw = file_get_contents('php://input') ?: json_encode($this->input->post());
        
        // validate JSON
        $decodedInput = json_decode($raw, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON: ' . json_last_error_msg());
        }

        $url = 'https://connect.traxroot.com/api/Drivers';

        $res = $this->requestCurl($url, 'PUT', $decodedInput);

        $this->_respond($res);
    }


    // Helper response
    private function _respond($res)
    {
        if (!$res['success']) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => $res['error'] ?: 'Request failed']));
            return;
        }

        if ($res['httpCode'] == 401) {
            $this->session->unset_userdata('api_access_token');
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Token expired']));
            return;
        }

        $decoded = json_decode($res['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output
                ->set_status_header(502)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid JSON from upstream']));
            return;
        }

        $this->output
            ->set_status_header($res['httpCode'] ?: 200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($decoded));
    }

    private function _badRequest($msg)
    {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['error' => $msg]));
    }

}