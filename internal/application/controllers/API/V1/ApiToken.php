<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiToken extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function loginToApi()
    {
        
        $token = $this->getApiToken();

        if ($token) {
            echo "Token berhasil: $token";
        } else {
            echo "Gagal mendapatkan token.";
        }
    }

    protected function getApiToken()
    {
        $token = $this->session->userdata('api_access_token');

        // Jika token belum ada, login otomatis
        if (!$token) {

            $url = 'https://connect.traxroot.com/api/Token'; 
            
            $data = array(
                'userName'  => $this->session->userdata('traxroot_username'),
                'password'  => $this->session->userdata('traxroot_password'),
                // 'userName'  => $this->config->item('traxroot_username'),
                // 'password'  => $this->config->item('traxroot_password'),
                'subUserId' => 0,
                'language'  => 'en'
            );

            $payload = json_encode($data);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            if (isset($result['accessToken'])) {
                $token = $result['accessToken'];
                $this->session->set_userdata('api_access_token', $token);
            } else {
                // log_message('error', 'Gagal login ke API pihak ketiga: ' . json_encode($result));
                return false;
            }
        }

        return $token;
    }


    /**
     * Helper: melakukan request cURL ke API eksternal
     * @param string $url
     * @param string $method GET|POST|PUT|DELETE
     * @param mixed $payload array|string|null
     * @param array $headers tambahan headers
     * @return array [success(bool), httpCode(int|null), body(string), error(string|null)]
     */
    protected function requestCurl($url, $method = 'GET', $payload = null, $headers = array())
    {
        $token = $this->getApiToken();
        // var_dump($token);
        // exit;
        
        if (!$token) {
            return array('success' => false, 'httpCode' => null, 'body' => null, 'error' => 'No API token');
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $defaultHeaders = array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        );

        // Jika method membutuhkan content-type (PUT PATCH)
        if (in_array(strtoupper($method), array('POST', 'PUT', 'PATCH'))) {
            // jika payload berupa array, encode JSON
            if (is_array($payload)) {
                $payload = json_encode($payload);
            }
            $defaultHeaders[] = 'Content-Type: application/json-patch+json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            if (strtoupper($method) === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            }
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        // merge headers
        $allHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);

        $body = curl_exec($ch);
        $curlErr = null;

        if ($body === false) {
            $curlErr = curl_error($ch);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array(
            'success' => $body !== false,
            'httpCode' => $httpCode,
            'body' => $body,
            'error' => $curlErr
        );
    }
}