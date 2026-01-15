<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Get Token
require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Users extends ApiToken
{
    public $CI;

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->CI) {
            $this->CI = &get_instance();
        }

        $this->CI->load->helper('url');
        $this->CI->load->library('curl');
    }

    // GET /Users - Get a list of users
    public function getUsers()
    {
        $url = 'https://connect.traxroot.com/api/Users';
        
        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);

    }

    // GET /Users/{id} - Get a user by ID
    public function getUserById($id = null)
    {
        if (!$id) return $this->_badRequest('Missing id');

        $url = 'https://connect.traxroot.com/api/Users/' . intval($id);
        
        $res = $this->requestCurl($url, 'GET');

        $this->_respond($res);
    }

    // POST /Users - Create a new user
    public function saveUser()
    {
        // Read raw input (works for POST and PUT)
        $raw = file_get_contents('php://input') ?: json_encode($this->input->post());
        
        // Validate JSON
        $decodedInput = json_decode($raw, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON input:' . json_last_error_msg());
        }

        // Determine whether to create or update based on the presence of an ID
        if (isset($decodedInput['id']) && $decodedInput['id'] > 0) {
            $url = 'https://connect.traxroot.com/api/Users/' . $decodedInput['id'];
            $res = $this->requestCurl($url, 'PUT', $decodedInput);
        } else {
            $url = 'https://connect.traxroot.com/api/Users';
            $res = $this->requestCurl($url, 'POST', $decodedInput);
        }

        // Handle response
        $this->_respond($res);
    }

    // DELETE /Users/{id} - Delete a user by ID
    public function deleteUser($id = null)
    {
        if (!$id) return $this->_badRequest('Missing ID');

        $url = 'https://connect.traxroot.com/api/Users/' . intval($id);
        
        $res = $this->requestCurl($url, 'DELETE');

        $this->_respond($res);
        
    }

    // Register user melalui Traxroot SelfRegister API
    public function selfRegister()
    {
        // Ambil raw JSON
        $raw = file_get_contents('php://input');

        if (!$raw) {
            return $this->_badRequest('Empty request body');
        }

        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON: ' . json_last_error_msg());
        }

        return $this->_processRegistration($decoded);
    }

    /**
     * INTERNAL REGISTER (NON-HTTP)
     * Dipanggil dari controller Company → create()
     */
    public function registerInternal($data)
    {
        return $this->_processRegistration($data, true);
    }

    private function _processRegistration($data, $internal = false)
    {
        $allowed = [
            "password",
            "passwordConfirmation",
            "name",
            "email",
            "contactName",
            "orgName",
            "phoneNumber",
            "timeZone",
            "geocoder",
            "language",
            "postAddress"
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        // Remove empty/null
        foreach ($filtered as $k => $v) {
            if ($v === null || $v === "") {
                unset($filtered[$k]);
            }
        }

        // Basic validation
        if (empty($filtered['email'])) {
            return $this->_errorResponse('Email is required', $internal);
        }
        if (empty($filtered['password']) || empty($filtered['passwordConfirmation'])) {
            return $this->_errorResponse('Password is required', $internal);
        }
        if ($filtered['password'] !== $filtered['passwordConfirmation']) {
            return $this->_errorResponse('Passwords do not match', $internal);
        }

        $url = 'https://connect.traxroot.com/api/Users/SelfRegister';

        $response = $this->CI->curl->simple_post(
            $url,
            json_encode($filtered),
            [
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ]
            ]
        );

        // If internal call → return raw result
        if ($internal) {
            return [
                'body' => $response,
                'status' => $this->CI->curl->info['http_code']
            ];
        }

        // If HTTP API mode → client response
        return $this->_respond($response);
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

    private function _errorResponse($msg, $internal)
    {
        if ($internal) {
            return [
                'status' => 400,
                'message' => $msg
            ];
        }

        return $this->_respond([
            'status' => 400,
            'message' => $msg
        ], 400);
    }
}
