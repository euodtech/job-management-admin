<?php
defined('BASEPATH') or exit('No direct script access allowed');

class APITraxrootConnect extends MY_Controller
{
    

    public function __construct()
    {
        parent::__construct();
    
        $this->load->library('form_validation');
        $this->load->model('M_Global');
        $this->load->library('curl');
    }

    public function loginToApi()
    {
        $url = 'https://connect.traxroot.com/api/Token'; 
        
        $data = array(
            'userName'  => 'euodoo',
            'password'  => 'euodoo360',
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
            // Simpan token ke session
            $this->session->set_userdata('api_access_token', $result['accessToken']);

            echo "Login berhasil! Token disimpan.";
        } else {
            echo "Login gagal. Response: ";
            print_r($result);
        }
    }



    // public function getDrivers()
    // {
    //     $token = $this->session->userdata('api_access_token');

    //     if (!$token) {
    //         echo "Token tidak tersedia. Silakan login dulu.";
    //         return;
    //     }

    //     $url = 'https://connect.traxroot.com/api/Drivers'; 
        
    //     $ch = curl_init($url);

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Authorization: Bearer ' . $token,
    //         'Accept: application/json'
    //     ));

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $result = json_decode($response, true);

    //     echo "<pre>";
    //     print_r($result);
    //     echo "</pre>";
    // }

}

