<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/API/V1/ApiToken.php');

class Vehicle extends ApiToken
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('status') != "kusam") {
            redirect(base_url('auth'));
        }

        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->helper('idr_helper');
    }

    public function index()
    {
        $data['title'] = "Efms | Vehicle";
        
        $this->render_page('main/vehicle/vehicle', $data);
    }

    public function traxrootVehicle()
    {   
        $compCode = $this->session->userdata('CompanyCode');

        $token = $this->getApiToken();
        if (!$token) {
            return $this->_badRequest('Tidak ada API token, silakan login Auth dulu.');
        }

        // Ambil dari API eksternal (Traxroot)
        $objects = $this->requestCurl('https://connect.traxroot.com/api/Objects', 'GET');
        $status  = $this->requestCurl('https://connect.traxroot.com/api/ObjectsStatus', 'GET');

        // Decode JSON
        $objectsJson = json_decode($objects['body'], true) ?? [];
        $statusJson  = json_decode($status['body'], true) ?? [];
        if (is_string($statusJson)) {
            $statusJson = json_decode($statusJson, true);
        } else {
            $statusJson = $statusJson;
        }
        $statusPoints = $statusJson["points"] ?? [];

        // Map trackerid => point terakhir
        $statusMap = [];
        foreach ($statusPoints as $point) {
            $trackerId = $point['trackerid'];
            $statusMap[$trackerId] = $point;
        }

        // Siapkan data DataTables
        $dataTableArray = [];
        foreach ($objectsJson as $obj) {
            $trackerid = $obj["main"]["id"];

            $point = $statusMap[$trackerid] ?? [];

            // $lat = $point["lat"] ?? 0;
            // $lng = $point["lng"] ?? 0;

            // $cek = $this->db->where('VehicleName', $obj['main']['name'])
            //     ->where('TraxrootID', $trackerid)
            //     ->where('CompanyCode', $compCode)
            //     ->get('MasterVehicle')
            //     ->row();

            // if (!$cek) 
            // {
            //     $this->db->insert('MasterVehicle', [
            //         'VehicleName'        => $obj['main']['name'],
            //         'TraxrootID'              => $trackerid,
            //         'CompanyCode'        => $compCode
            //     ]);
            // } else {
            //     continue;
            // }

            $dataTableArray[] = [
                'name'      => $obj['main']['name'] ?? '',
                'model'     => $obj['main']['model'] ?? '',
                'latitude'  => $point["lat"] ?? 0,
                'longitude' => $point["lng"] ?? 0,
                // 'address'   => $this->getAddressFromLatLng($lat, $lng),
                // 'address'   => '',
                'speed'     => $point["speed"] ?? 0,
                'sat'       => $point["sat"] ?? 0,
                'comment'   => $obj['main']['comment'] ?? '',
            ];
        }

        // Server-side params dari DataTables
        $draw   = intval($this->input->get('draw'));
        $start  = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $search = $this->input->get('search')['value'] ?? '';

        // Filter search
        if ($search !== '') {
            $dataTableArray = array_filter($dataTableArray, function ($row) use ($search) {
                return stripos($row['name'], $search) !== false
                    || stripos($row['comment'], $search) !== false;
            });
            $dataTableArray = array_values($dataTableArray);
        }

        // Pagination slice
        $dataPage = array_slice($dataTableArray, $start, $length);

        // Format JSON untuk DataTables
        $output = [
            "draw" => $draw,
            "recordsTotal" => count($objectsJson),
            "recordsFiltered" => count($dataTableArray),
            "data" => $dataPage
        ];

        $this->_respondRaw(json_encode($output), $objects['success'] ? 200 : 500);
    }

    private function _respondRaw($jsonString, $httpCode = 200)
    {
        $this->output
            ->set_status_header($httpCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output($jsonString);
    }

    private function _badRequest($msg)
    {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['error' => $msg]));
    }

    private function getAddressFromLatLng($lat, $lng) {
        if (!$lat || !$lng) return '';

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'TraxrootApp/1.0'); // wajib ada user-agent
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['display_name'] ?? '';
    }

}
