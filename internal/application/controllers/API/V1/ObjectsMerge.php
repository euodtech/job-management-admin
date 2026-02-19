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

        $TTL_SHORT  = 60;   // Objects, Drivers: 60 seconds
        $TTL_MEDIUM = 300;  // Geozones, Icons, Profile: 5 minutes

        // Cacheable resources with their TTLs
        $cacheable = array(
            'objects'  => array('url' => 'https://connect.traxroot.com/api/Objects',       'ttl' => $TTL_SHORT),
            'drivers'  => array('url' => 'https://connect.traxroot.com/api/Drivers',       'ttl' => $TTL_SHORT),
            'geozones' => array('url' => 'https://connect.traxroot.com/api/Geozones',      'ttl' => $TTL_MEDIUM),
            'icons'    => array('url' => 'https://connect.traxroot.com/api/Objects/Icons',  'ttl' => $TTL_MEDIUM),
            'profile'  => array('url' => 'https://connect.traxroot.com/api/Profile',        'ttl' => $TTL_MEDIUM),
        );

        $bodies = array();
        $urlsToFetch = array();

        // Check cache for each resource
        foreach ($cacheable as $key => $cfg) {
            $cacheFile = $this->_getCacheDir() . $key . '.json';
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cfg['ttl']) {
                $bodies[$key] = file_get_contents($cacheFile);
            } else {
                $urlsToFetch[$key] = $cfg['url'];
            }
        }

        // Always fetch ObjectsStatus live
        $urlsToFetch['status'] = 'https://connect.traxroot.com/api/ObjectsStatus';

        // Fetch all needed URLs in parallel
        if (!empty($urlsToFetch)) {
            $responses = $this->requestCurlMulti($urlsToFetch);
            foreach ($responses as $key => $resp) {
                $bodies[$key] = $resp['body'];
                // Write to cache (but NOT status)
                if ($key !== 'status' && $resp['success'] && $resp['body']) {
                    $cacheFile = $this->_getCacheDir() . $key . '.json';
                    $tmpFile = $cacheFile . '.tmp';
                    file_put_contents($tmpFile, $resp['body']);
                    // Windows rename() fails if destination exists; delete first
                    if (file_exists($cacheFile)) {
                        @unlink($cacheFile);
                    }
                    @rename($tmpFile, $cacheFile);
                }
            }
        }

        // Fall back to stale cache for any failed fetches
        foreach ($cacheable as $key => $cfg) {
            if (empty($bodies[$key])) {
                $cacheFile = $this->_getCacheDir() . $key . '.json';
                if (file_exists($cacheFile)) {
                    $bodies[$key] = file_get_contents($cacheFile);
                }
            }
        }

        // Decode all JSON
        $objectsJson  = json_decode($bodies['objects'] ?? '', true)  ?? [];
        $statusJson   = json_decode($bodies['status'] ?? '', true);
        $geozonesJson = json_decode($bodies['geozones'] ?? '', true) ?? [];
        $driversJson  = json_decode($bodies['drivers'] ?? '', true)  ?? [];
        $iconsJson    = json_decode($bodies['icons'] ?? '', true)    ?? [];
        $profileJson  = json_decode($bodies['profile'] ?? '', true)  ?? [];

        // Handle double-encoded strings
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

        return $this->_respondRaw(json_encode($merged));
    }

    private function _getCacheDir()
    {
        $username = $this->session->userdata('traxroot_username') ?: 'default';
        $safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
        $dir = APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'traxroot' . DIRECTORY_SEPARATOR . $safeUser . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
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
