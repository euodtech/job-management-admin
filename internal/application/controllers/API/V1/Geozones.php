<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Get Token
require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Geozones extends ApiToken
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }


    /**
     * GET /Geozones/Icons
     * Get list of icons used for geozones
     */
    public function getIcons()
    {
        $url = 'https://connect.traxroot.com/api/Geozones/Icons';

        $res = $this->requestCurl($url, 'GET', null, array('Accept: text/plain'));

        $this->_respond($res);
    }

    /**
     * GET /Geozones
     * Get list of geozones
     */
    public function getGeozones()
    {
        $url = 'https://connect.traxroot.com/api/Geozones';

        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);
    }

    /**
     * GET /Geozones/{id}
     * @param int $id
     */
    public function getGeozoneById($id = null)
    {
        if (!$id) return $this->_badRequest('Missing geozone ID');

        $url = 'https://connect.traxroot.com/api/Geozones/' . intval($id);

        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);
    }

    /**
     * PUT /Geozones
     * Update/create entity. If id is not set, new entity will be created.
     * We read raw input (application/json)
     */
    public function saveGeozone()
    {
        $raw = file_get_contents('php://input') ?: json_encode($this->input->post());
        
        $decodedInput = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON: ' . json_last_error_msg());
        }

        if (isset($decodedInput['id']) && $decodedInput['id'] > 0) {
            $url = 'https://connect.traxroot.com/api/Geozones' . $decodedInput['id'];
            $res = $this->requestCurl($url, 'PUT', $decodedInput);
        } else {
            $url = 'https://connect.traxroot.com/api/Geozones';
            $res = $this->requestCurl($url, 'POST', $decodedInput);
        }

        // Handle response
        $this->_respond($res);
    }

    /**
     * DELETE /Geozones/{id}
     * @param int $id
     */
    public function deleteGeozone($id = null)
    {
        if (!$id) return $this->_badRequest('Missing geozone ID');

        $url = 'https://connect.traxroot.com/api/Geozones/' . intval($id);

        $res = $this->requestCurl($url, 'DELETE');

        $this->_respond($res);
    }


    // Helper response
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

        $decoded = json_decode($res['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output
                ->set_status_header(502)
                ->set_content_type('application/json', 'utf-8')
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