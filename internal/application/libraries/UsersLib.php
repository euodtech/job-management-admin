<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersLib
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
        $this->CI->load->library('curl');
        $this->CI->load->library('session'); 
    }

    protected function getApiToken()
    {
        $token = $this->CI->session->userdata('api_access_token');

        if ($token) {
            return $token;
        }

        // Login manual ke Traxroot
        $url = 'https://connect.traxroot.com/api/Token';

        $data = [
            'userName'  => $this->CI->config->item('traxroot_username'),
            'password'  => $this->CI->config->item('traxroot_password'),
            'subUserId' => 0,
            'language'  => 'en'
        ];

        $payload = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['accessToken'])) {
            $token = $result['accessToken'];
            $this->CI->session->set_userdata('api_access_token', $token);
            return $token;
        }

        log_message('error', 'Gagal login API Traxroot: ' . json_encode($result));
        return false;
    }

    /** --- CURL WRAPPER --- **/
    protected function requestCurl($url, $method = 'GET', $payload = null, $headers = array())
    {
        $token = $this->getApiToken();

        if (!$token) {
            return [
                'success' => false,
                'httpCode' => null,
                'body' => null,
                'error' => 'No API token'
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $defaultHeaders = [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ];

        $method = strtoupper($method);

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {

            if (is_array($payload)) {
                $payload = json_encode($payload);
            }

            $defaultHeaders[] = 'Content-Type: application/json-patch+json';

            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            }

        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $allHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        $body = curl_exec($ch);
        $curlErr = null;

        if ($body === false) {
            $curlErr = curl_error($ch);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Jika token expired → auto reset
        if ($httpCode == 401) {
            $this->CI->session->unset_userdata('api_access_token');
        }

        return [
            'success' => $body !== false,
            'httpCode' => $httpCode,
            'body' => $body,
            'error' => $curlErr
        ];
    }

    /** --- GET USERS --- **/
    public function getUsers()
    {
        $url = 'https://connect.traxroot.com/api/Users';

        $res = $this->requestCurl($url, 'GET');

        // Jika request gagal
        if (!$res['success'] || $res['httpCode'] !== 200) {
            return [];
        }

        // Decode JSON body → hasil harus berupa ARRAY USERS
        $users = json_decode($res['body'], true);

        if (!is_array($users)) {
            return [];
        }

        return $users;
    }


    /** --- GET USER BY ID --- **/
    public function getUserById($id = null)
    {
        if (!$id) return $this->_badRequest('Missing id');
        $url = 'https://connect.traxroot.com/api/Users/' . intval($id);
        $res = $this->requestCurl($url, 'GET');
        return $this->_respond($res);
    }

    /** --- SAVE USER --- **/
    public function saveUser($input = null)
    {
        $raw = $input ?: (file_get_contents('php://input') ?: json_encode($this->CI->input->post()));
        $decodedInput = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON input: ' . json_last_error_msg());
        }

        // Hilangkan "id" jika 0
        if (isset($decodedInput['id']) && intval($decodedInput['id']) === 0) {
            unset($decodedInput['id']);
        }

        if (isset($decodedInput['id']) && $decodedInput['id'] > 0) {
            $url = 'https://connect.traxroot.com/api/Users/' . $decodedInput['id'];
            $res = $this->requestCurl($url, 'PUT', $decodedInput);
        } else {
            $url = 'https://connect.traxroot.com/api/Users';
            $res = $this->requestCurl($url, 'POST', $decodedInput);
        }

        return $this->_respond($res);
    }

    /** --- DELETE USER --- **/
    public function deleteUser($id = null)
    {
        if (!$id) return $this->_badRequest('Missing ID');
        $url = 'https://connect.traxroot.com/api/Users/' . intval($id);
        $res = $this->requestCurl($url, 'DELETE');
        return $this->_respond($res);
    }

    /** --- SELF REGISTER --- **/
    public function selfRegister($input = null)
    {
        $raw = $input ?: file_get_contents('php://input');
        if (!$raw) return $this->_badRequest('Empty request body');

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->_badRequest('Invalid JSON: ' . json_last_error_msg());
        }

        return $this->registerInternal($decoded);
    }

    /** --- REGISTER INTERNAL --- **/
    public function registerInternal($data, $internal = true)
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
            "postAddress",
            "isDisabled",
            "isEmailConfirmed",
            "loginDate",
            "mask",
            "interfacePermissions",
            "flags",
            "objectsCount",
            "geozonesCount"
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        // Remove empty/null
        foreach ($filtered as $k => $v) {
            if ($v === null || $v === "") unset($filtered[$k]);
        }

        // Basic validation
        if (empty($filtered['email'])) return $this->_errorResponse('Email is required', $internal);
        if (empty($filtered['password']) || empty($filtered['passwordConfirmation'])) {
            return $this->_errorResponse('Password is required', $internal);
        }
        if ($filtered['password'] !== $filtered['passwordConfirmation']) {
            return $this->_errorResponse('Passwords do not match', $internal);
        }

        $token = $this->getApiToken();

        if (!$token) {
            return $this->_errorResponse('Login to Traxroot failed', $internal);
        }

        $url = 'https://connect.traxroot.com/api/Users/SelfRegister';
        // Tambahkan header khusus untuk json-patch
        $res = $this->requestCurl($url, 'POST', $filtered, ['Content-Type: application/json']);

        if ($res['httpCode'] != 200) {
            return [
                'status' => $res['httpCode'],
                'message' => $res['body'] ?: $res['error']
            ];
        }

        return [
            'status' => 200,
            'body' => json_decode($res['body'], true)
        ];
    }


    /** --- SYNC USERS TO DB --- **/
    public function syncUsersToDb()
    {
        $logs = [];

        $users = $this->getUsers(); // sekarang pasti pakai token valid
        if (empty($users) || !is_array($users)) {
            $logs[] = '❌ No users fetched from Traxroot API.';
            return $logs;
        }

        $this->CI->load->database();
        $logs[] = 'ℹ️ Users fetched: ' . count($users);

        foreach ($users as $user) {
            $userLoginID = $user['id'] ?? null;
            $email       = $user['email'] ?? null;

            if (!$email) {
                $logs[] = '⚠️ Skip user with missing email: ' . json_encode($user);
                continue;
            }

            $existing = null;
            // if ($userLoginID) {
            //     $existing = $this->CI->db->get_where('ListCompany', ['UserLoginID' => 0])->row_array();
            // }
            if (!$existing) {
                $existing = $this->CI->db->get_where('ListCompany', ['CompanyEmail' => $email])->row_array();
            }

            $apiData = [
                'CompanyName'  => $user['name'] ?? '',
                'CompanyEmail' => $email,
                'CompanyPhone' => $user['phoneNumber'] ?? '',
                'OrgName'      => $user['orgName'] ?? '',
                'TimeZone'     => $user['timeZone'] ?? '',
                'Geocoder'     => $user['geocoder'] ?? '',
                'Language'     => $user['language'] ?? '',
                'updated_at'   => date('Y-m-d H:i:s')
            ];

            if (empty($existing['CompanyLogo'])) {
                $apiData['CompanyLogo'] = base_url('assets/dist/img/company_logo/default.png');
            }

            if ($existing) {

                // buat akses login

                // if ($userLoginID) $apiData['UserLoginID'] = 0;


                

                $this->CI->db->where('ListCompanyID', $existing['ListCompanyID']);
                $res = $this->CI->db->update('ListCompany', $apiData);
                if ($res) $logs[] = "✅ Updated: {$email}";
                else $logs[] = "❌ Failed update: {$email} | Query: ".$this->CI->db->last_query();
            } else {
                if ($userLoginID) $apiData['UserLoginID'] = $userLoginID;
                $apiData['created_at']  = date('Y-m-d H:i:s');
                $emailPrefix = explode('@', $email)[0] ?? 'CMP';
                $apiData['CompanyCode'] = strtoupper(substr($emailPrefix, 0, 3)) . rand(10000, 99999);
                $apiData['CompanyLogo']      = $apiData['CompanyLogo'] ?? base_url('assets/dist/img/company_logo/default.png');
                $apiData['CompanySubscribe'] = 0;

                $res = $this->CI->db->insert('ListCompany', $apiData);
                if ($res) $logs[] = "✅ Inserted: {$email}";
                else $logs[] = "❌ Failed insert: {$email} | Query: ".$this->CI->db->last_query();
            }
        }

        $logs[] = "ℹ️ Traxroot Users sync completed. Total users: " . count($users);

        return $logs;
    }

    // SYNC USERS COMPANY LOGIN TO DB
    public function syncTraxrootDataUsersCompanyLogin()
    {
        $logs = [];

        $users = $this->getUsers(); 
        
        // pastikan pakai token valid
        if (empty($users) || !is_array($users)) {
            $logs[] = '❌ No users fetched from Traxroot API.';
            return $logs;
        }

        $this->CI->load->database();
        $logs[] = 'ℹ️ Users fetched: ' . count($users);

        foreach ($users as $user) {
            
            $email       = $user['email'] ?? null;

            if (!$email) {
                $logs[] = '⚠️ Skip user with missing email: ' . json_encode($user);
                continue;
            }

            $fullName = $user['name'] ?? explode("@", $email)[0];

            // SINKRON USER LOGIN
            $userLogin = $this->CI->db
                ->get_where('UserLogin', ['Email' => $email])
                ->row_array();

            if ($userLogin) {

                // Update user login (jika perlu)
                $this->CI->db->where('UserLoginID', $userLogin['UserLoginID']);
                $this->CI->db->update('UserLogin', [
                    'Fullname' => $fullName,
                ]);

                $userLoginID = $userLogin['UserLoginID'];

                $logs[] = "🔄 Updated UserLogin: {$email}";
            
            } else {

                // Insert baru user login
                $this->CI->db->insert('UserLogin', [
                    'Fullname' => $fullName,
                    'Email'    => $email,
                    'Password' => password_hash("12345", PASSWORD_DEFAULT),
                    'Role'     => 3, // Admin User
                ]);

                $userLoginID = $this->CI->db->insert_id();
                $logs[] = "🟢 Created new UserLogin: {$email}";
            }

            // SINKRON LIST COMPANY
            $company = $this->CI->db
                ->get_where('ListCompany', ['CompanyEmail' => $email])
                ->row_array();

            $companyData = [
                'CompanyName'  => $fullName,
                'CompanyEmail' => $email,
                'CompanyPhone' => $user['phoneNumber'] ?? '',
                'OrgName'      => $user['orgName'] ?? '',
                'TimeZone'     => $user['timeZone'] ?? '',
                'Geocoder'     => $user['geocoder'] ?? '',
                'Language'     => $user['language'] ?? 'en-us',
                'UserLoginID'  => $userLoginID,
                'CompanySubscribe' => 2,
                'updated_at'   => date('Y-m-d H:i:s')
            ];

            if ($company) {

                // Update company
                $this->CI->db->where('ListCompanyID', $company['ListCompanyID']);
                $this->CI->db->update('ListCompany', $companyData);

                $logs[] = "🔄 Updated Company: {$email}";
            
            } else {

                // Insert new company
                $companyData['created_at'] = date('Y-m-d H:i:s');
                $companyData['CompanyCode'] = strtoupper(substr($fullName, 0, 3)) . rand(10000,99999);
                $companyData['CompanySubscribe'] = 0;
                $companyData['CompanyLogo'] = base_url('assets/dist/img/company_logo/default.png');

                $this->CI->db->insert('ListCompany', $companyData);
                $logs[] = "🟢 Inserted Company: {$email}";
            }
        }

        $logs[] = "ℹ️ Traxroot Users login sync completed. Total users: " . count($users);
        return $logs;
    }



    /** --- HELPER METHODS --- **/
    private function _respond($res)
    {
        if (!isset($res['success']) || $res['success'] === false) {
            return json_encode(['error' => $res['error'] ?? 'Request failed']);
        }

        $decoded = json_decode($res['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_encode(['error' => 'Invalid JSON from upstream']);
        }

        return json_encode($decoded);
    }

    private function _badRequest($msg)
    {
        return json_encode(['error' => $msg]);
    }

    private function _errorResponse($msg, $internal)
    {
        if ($internal) {
            return ['status' => 400, 'message' => $msg];
        }
        return json_encode(['status' => 400, 'message' => $msg]);
    }

    public function testLogin()
    {
        $token = $this->getApiToken();

        echo "<pre>";
        if ($token) {
            echo "✅ Token berhasil didapatkan: " . $token;
        } else {
            echo "❌ Gagal mendapatkan token dari Traxroot API.";
        }
        echo "</pre>";
        die;
    }


}
