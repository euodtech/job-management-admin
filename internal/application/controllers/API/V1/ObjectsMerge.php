<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class ObjectsMerge extends ApiToken
{
    // private $apiBase;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        // $this->apiBase = base_url('v1/api/traxroot/');
    }

    public function getObjectMerge()
    {
        $token = $this->getApiToken();
        if (!$token) {
            return $this->_badRequest('Tidak ada API token, silakan login Auth dulu.');
        }

        // Ambil dari API eksternal
        $objects  = $this->requestCurl('https://connect.traxroot.com/api/Objects', 'GET');
        $status   = $this->requestCurl('https://connect.traxroot.com/api/ObjectsStatus', 'GET');
        $geozones = $this->requestCurl('https://connect.traxroot.com/api/Geozones', 'GET');
        $drivers  = $this->requestCurl('https://connect.traxroot.com/api/Drivers', 'GET');
        $icons    = $this->requestCurl('https://connect.traxroot.com/api/Objects/Icons', 'GET');
        $profile  = $this->requestCurl('https://connect.traxroot.com/api/Profile', 'GET');

        // Decode semua JSON
        $objectsJson  = json_decode($objects['body'], true)  ?? [];
        $statusJson   = json_decode($status['body'], true);
        $geozonesJson = json_decode($geozones['body'], true) ?? [];
        $driversJson  = json_decode($drivers['body'], true)  ?? [];
        $iconsJson    = json_decode($icons['body'], true)    ?? [];
        $profileJson  = json_decode($profile['body'], true)  ?? [];

        // Special case: Status dari Traxroot terkadang double-encoded
        if (is_string($statusJson)) {
            $statusJson = json_decode($statusJson, true) ?? [];
        }

        if (is_string($profileJson)) {
            $profileJson = json_decode($profileJson, true) ?? [];
        }

        $merged = [
            'objects'  => $objectsJson,
            'status'   => $statusJson,
            'geozones' => $geozonesJson,
            'drivers'  => $driversJson,
            'icons'    => $iconsJson,
            'profile'  => $profileJson
        ];

        // Output JSON valid
        return $this->_respondRaw(json_encode($merged));

        // // Ambil semua data dari route internal
        // $objects  = $this->_curlRequest($this->apiBase . 'objects');
        // $status   = $this->_curlRequest($this->apiBase . 'objectsStatus');
        // $geozones = $this->_curlRequest($this->apiBase . 'geozones');
        // $drivers  = $this->_curlRequest($this->apiBase . 'getDrivers');
        // $icons    = $this->_curlRequest($this->apiBase . 'objects/icons');
        // $profile  = $this->_curlRequest($this->apiBase . 'profile');

        // $mergedJson = '{'
        //     . '"objects":'  . $this->_safeBody($objects['body'], '[]')   . ','
        //     . '"status":'   . $this->_safeBody($status['body'], '{}')    . ','
        //     . '"geozones":' . $this->_safeBody($geozones['body'], '[]')  . ','
        //     . '"drivers":'  . $this->_safeBody($drivers['body'], '[]')   . ','
        //     . '"icons":'    . $this->_safeBody($icons['body'], '[]')     . ','
        //     . '"profile":'  . $this->_safeBody($profile['body'], '{}')
        //     . '}';


        // // Encode ulang biar valid
        // $this->_respondRaw($mergedJson);
    }

    private function _curlRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => !$err && $body,
            'body'    => $body,
            'httpCode'=> $code,
            'error'   => $err
        ];
    }

    private function _safeBody($body, $default = '{}')
    {
        if (!$body) return $default;

        $body = trim($body);

        // 🛠 Fix: hapus koma ganda (`, ,`)
        $body = preg_replace('/,\s*,+/', ',', $body);

        // 🛠 Fix: hapus koma di akhir object/array
        $body = preg_replace('/,(\s*[}\]])/', '$1', $body);

        return $body;
    }

    private function _respondRaw($jsonString, $httpCode = 200)
    {
        $this->output
            ->set_status_header($httpCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output($jsonString);
    }

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

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output
                ->set_status_header(502)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['error' => 'Invalid JSON from upstream']));
            return;
        }

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
