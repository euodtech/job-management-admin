<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends MY_Controller
{
    

    public function __construct()
    {
        parent::__construct();
    
        $this->load->library('form_validation');
        $this->load->model('M_Global');
        $this->load->library('curl');
    }

    private function getDataToken()
    {
        $url_get_token = "https://connect.traxroot.com/api/Token";

        $data_body = [
            'userName'   => 'euodoo',
            'password'   => 'euodoo360',
            'subUserId'  => 0,
            'language'   => 'ing'
        ];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $result_token = $this->hitApi($url_get_token, $data_body, 'post', $headers);

        return $result_token['accessToken'];
    }

    public function index()
    {

        // get token first
        $token = $this->getDataToken();

        // URL API
        $url = 'https://connect.traxroot.com/api/Profile';

        // Panggil fungsi hitApi dengan header Authorization
        $headers = [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $result = $this->hitApi($url, null, 'get', $headers);

        $result = json_decode($result, true);

        echo json_encode($result);
    }

    public function getUsersData()
    {
        $this->db->trans_begin(); // ⬅️ mulai transaksi

        try {
            $url = 'http://quetraverse.pro/efms/internal/v1/api/traxroot/getDrivers';
            $result = $this->hitApi($url);

            if (empty($result) || !is_array($result)) {
                throw new Exception("Response dari API kosong atau tidak valid.");
            }

            $data_company = [];

            foreach ($result as $val) {

                // Validasi data penting
                // if (empty($val['name']) || empty($val['email'])) {
                //     throw new Exception("Data tidak lengkap untuk salah satu user.");
                // }

                $email_for_login = "example_" . str_replace(' ', '', $val['name']) . "@gmail.com";

                // 1️⃣ Insert ke tabel UserLogin
                $data_akses_login = [
                    "Fullname" => $val['name'],
                    "Email" => $email_for_login,
                    "Password" => password_hash("12345", PASSWORD_DEFAULT),
                    "Role" => 2
                ];

                $userLoginID = $this->M_Global->insertid($data_akses_login, "UserLogin");

                if (!$userLoginID) {
                    throw new Exception("Gagal insert UserLogin untuk email: {$val['email']}");
                }

                // 2️⃣ Generate kode perusahaan unik
                $company_email = $val['email'];
                $email_prefix = explode('@', $company_email)[0];
                $three_letters = strtoupper(substr($email_prefix, 0, 3));
                $random_number = rand(10000, 99999);
                $output_code = $three_letters . $random_number;

                // 3️⃣ Tambahkan ke array untuk bulk insert
                $data_company[] = [
                    "ListCompanyID" => "5",
                    "Fullname" => $val['name'],
                    "Email" => $email_for_login,
                    "PhoneNumber" => $val['phone'] ?? '',
                    "StatusActive" => 0,
                    "UserLoginID" => $userLoginID,
                    "Category" => $val['category'],
                    "Rank" => $val['rank'],
                    "License" => $val['license'],
                    "LicenseValidUntil" => $val['licenseValidTill'],
                    "InternalID" => $val['internalId'],
                    "created_at" => date('Y-m-d H:i:s')
                ];
            }

            // 4️⃣ Bulk insert ke tabel ListCompany
            $data = $this->M_Global->bulkinsert($data_company, "ListUser");

            if ($data !== "success") {
                throw new Exception("Gagal insert ke tabel ListCompany.");
            }

            // ✅ Commit transaksi jika semua sukses
            $this->db->trans_commit();
            echo "success";

        } catch (Exception $e) {
            // ❌ Rollback kalau ada error
            $this->db->trans_rollback();

            echo "<pre style='color:red;'>Terjadi kesalahan: " . $e->getMessage() . "</pre>";
        }
    }


    public function hitApi($src, $data = null, $tipe = 'get', $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $src);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Set header jika ada
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Handle POST
        if (strtolower($tipe) == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);

            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        // Eksekusi
        $response = curl_exec($ch);

        if ($response === FALSE) {
            $error = curl_error($ch);
            $response = json_encode([
                'status' => 'error',
                'message' => $error
            ]);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

}