<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Get Token
require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Profile extends ApiToken
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * GET /Profile
     * Get all user profile
     */
    public function getProfile()
    {
        $url = 'https://connect.traxroot.com/api/Profile';
        $res = $this->requestCurl($url, 'GET');
        $this->_respond($res);
    }

    // Response helpers (inherited from Geozones)
    private function _respond($res)
    {
        if (!$res['success']) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['error' => $res['error'] ?: 'Request failed']));
            return;
        }

        if ($res['httpCode'] == 401) {
            $this->session->unset_userdata('api_access_token');
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['error' => 'Token expired']));
            return;
        }

        // $decoded = json_decode($res['body'], true);
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     $this->output
        //         ->set_status_header(502)
        //         ->set_content_type('application/json', 'utf-8')
        //         ->set_output(json_encode(['error' => 'Invalid JSON from upstream']));
        //     return;
        // }

        // $this->output
        //     ->set_status_header($res['httpCode'] ?: 200)
        //     ->set_content_type('application/json', 'utf-8')
        //     ->set_output(json_encode($decoded));

        $rawBody = $res['body'];

        // kalau JSON kamu ternyata string dengan tanda kutip di luar
        if (substr($rawBody, 0, 1) === '"' && substr($rawBody, -1) === '"') {
            $rawBody = trim($rawBody, '"');        // hapus kutip luar
            $rawBody = stripslashes($rawBody);     // hapus backslash
        }

        $this->output
            ->set_status_header($res['httpCode'] ?: 200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output($rawBody);
    }

    private function _badRequest($msg)
    {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['error' => $msg]));
    }
}
