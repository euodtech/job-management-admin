<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Objects extends ApiToken
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * GET /Objects
     * Get list of all objects
     */
    public function getObjects()
    {
        $url = 'https://connect.traxroot.com/api/Objects';
        $res = $this->requestCurl($url, 'GET');
        $this->_respond($res);
    }

    /**
     * GET /Objects/{id}
     * @param int $id
     */
    public function getObjectsById($id = null)
    {
        if (!$id) return $this->_badRequest('Missing object ID');
        $url = 'https://connect.traxroot.com/api/Objects/' . $id;
        $res = $this->requestCurl($url, 'GET');
        $this->_respond($res);
    }

    /**
     * DELETE /Objects/{id}
     * @param int $id
     */
    public function deleteObjects($id = null)
    {
        if (!$id) return $this->_badRequest('Missing object ID');
        $url = 'https://connect.traxroot.com/api/Objects/' . intval($id);
        $res = $this->requestCurl($url, 'DELETE');
        $this->_respond($res);
    }

    /**
     * PUT /Objects
     * Save or update object
     */
    public function saveObjects()
    {
        $raw = file_get_contents('php://input') ?: json_encode($this->input->post());
        $decodedInput = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON: ' . json_last_error_msg());
        }

        $url = 'https://connect.traxroot.com/api/Objects';
        $res = $this->requestCurl($url, 'PUT', $decodedInput, array('Content-Type: application/json'));
        $this->_respond($res);
    }

    /**
     * GET /Objects/Icons
     * Get list of icons used for objects
     */
    public function getObjectsIcons()
    {
        $url = 'https://connect.traxroot.com/api/Objects/Icons';
        $res = $this->requestCurl($url, 'GET', null, array('Accept: text/plain'));
        $this->_respond($res);
    }

    // Reuse _respond and _badRequest methods
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
