<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        // Cek sesi login
        if ($this->session->userdata('status') != "kusam") {
            redirect(base_url('auth'));
        }

        // Load library Users
        $this->load->library('UsersLib');


        $this->load->library('form_validation');
        $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->helper('idr_helper');
        $this->load->helper('date_format_helper');

    }

    // Test Traxroot Token
    public function testTraxrootToken()
    {
        $this->load->library('userslib');
        $this->userslib->testLogin();
    }

    // Test Register Traxroot
    public function testRegister()
    {
        $this->load->library('userslib');

        $payload = [
            "password"             => "Test1234!",
            "passwordConfirmation" => "Test1234!",
            "name"                 => "Fikri Company",
            "email"                => "fikri_test@example.com",
            "contactName"          => "Fikri",
            "orgName"              => "Fikri Org",
            "phoneNumber"          => "+639171234567",
            "timeZone"             => "Asia/Manila",
            "geocoder"             => "Google",
            "language"             => "en",
            "postAddress"          => "N/A",
            "isDisabled"           => false,
            "isEmailConfirmed"     => true,
            "loginDate"             => 0,
            "mask"                  => 0,
            "interfacePermissions"  => 0,
            "flags"                 => "",
            "objectsCount"          => 0,
            "geozonesCount"         => 0
        ];

        $res = $this->userslib->registerInternal($payload);

        echo "<pre>";
        print_r($res);
        echo "</pre>";
        die;
    }

    public function debugGetTraxrootUsers()
    {
        $this->load->library('UsersLib');

        echo "<h3>🚀 Debug Traxroot Users</h3>";

        // Ambil token
        $sync = $this->userslib->getUsers();

        echo "<pre>";
        foreach ($sync as $log) {
            echo $log . "\n";
        }
        echo "</pre>";
        die;
        
    }


    // Sync Traxroot Users to DB
    public function syncTraxrootUsers()
    {
        $this->load->library('UsersLib');

        $sync = $this->userslib->syncUsersToDb();

        echo "<pre>";
        foreach ($sync as $log) {
            echo $log . "\n";
        }
        echo "</pre>";

        // Cek apakah ada log error
        $hasError = false;
        foreach ($sync as $log) {
            if (strpos($log, '❌') !== false) {
                $hasError = true;
                break;
            }
        }

        echo "\n=== STATUS ===\n";
        echo $hasError ? "❌ Gagal sync Traxroot Users" : "✅ Sync Traxroot Users ke DB berhasil!";
        echo "</pre>";


        // if ($sync) {
        //     echo "<pre>✅ Sync Traxroot Users to DB berhasil!</pre>";
        // } else {
        //     echo "<pre>❌ Gagal sync Traxroot Users. Cek log untuk detail error.</pre>";
        // }
    }

    public function syncTraxrootUsersCompanyLogin()
    {
        $this->load->library('UsersLib');

        // $sync = $this->userslib->syncTraxrootDataUsersCompanyLogin();

        // echo "<pre>";
        // foreach ($sync as $log) {
        //     echo $log . "\n";
        // }
        // echo "</pre>";

        // die;

        // $this->session->set_flashdata('message', implode('<br>', $sync));
        // redirect('company-list');

        // $logs = 
        $this->userslib->syncTraxrootDataUsersCompanyLogin();

        // Jika mau gabungkan log jadi satu string:
        // $message = implode('<br>', $logs);

        // Set flashdata untuk SweetAlert
        $this->session->set_flashdata('swal', [
            'title' => 'Sync Completed',
            'text'  => 'Traxroot Users synchronization with Company Login completed.',
            'icon'  => 'success'
        ]);

        redirect('company-list');
    }


    public function index()
    {
        $data['title'] = "Efms | Company";

        $data['company'] = $this->M_Global->globalquery("SELECT * FROM ListCompany
            ORDER BY CompanyName ASC")->result_array();

        $this->render_page('main/company/page_company',$data); 
    }



    public function create()
    {
        $this->db->trans_begin();

        // Ambil input
        $company_name  = $this->input->post('company_name');
        $company_phone = $this->input->post('company_phone');
        $company_email = $this->input->post('company_email');
        $company_pass  = $this->input->post('pass');
        $package       = $this->input->post('package');

        // Format Phone
        $formatPhoneNumber = "+63" . $company_phone;

        // Generate Company Code
        $email_prefix = explode('@', $company_email)[0];
        $three_letters = strtoupper(substr($email_prefix, 0, 3));
        $random_number = rand(10000, 99999);
        $output_code = $three_letters . $random_number;

        // Konfigurasi upload
        $config['upload_path']   = "./assets/dist/img/company_logo/";
        $config['allowed_types'] = 'jpeg|jpg|png|gif|bmp|mp4|mov|avi|wmv|webm|mkv';
        $config['encrypt_name']  = TRUE; // biar nama file unik

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        // Upload file
        if (!$this->upload->do_upload('company_logo')) {
            // $error = ['error' => $this->upload->display_errors()];
            // var_dump($error);
            // $this->db->trans_rollback();
            // die;

            $this->db->trans_rollback();
            $this->session->set_flashdata('message',
                '<div class="alert alert-danger">Upload logo failed!</div>'
            );
            redirect('company-list');
            return;
        } else {
            $data = $this->upload->data();
            $img = $data['file_name'];
        }

        $data_create = [
            "CompanyName"       => $this->input->post('company_name'),
            "CompanyCode"       => $output_code,
            "CompanyPhone"      => $formatPhoneNumber,
            "CompanyEmail"      => $this->input->post('company_email'),
            "CompanySubscribe"  => $this->input->post('package'),
            "CompanyLogo"       => base_url('assets/dist/img/company_logo/') . $img,
            "created_at"        => date('Y-m-d H:i:s')
        ];

        $cek_available_email = $this->M_Global->globalquery("SELECT *  FROM ListCompany WHERE CompanyEmail = '$company_email' ")->result_array();

        if(count($cek_available_email) == 0) {

            $company = $this->M_Global->insertid($data_create, "ListCompany");

            if ($company != false) {

                // $traxPayload = [
                //     "password"              => $company_pass,
                //     "passwordConfirmation"  => $company_pass,
                //     "name"                  => $company_name,
                //     "email"                 => $company_email,
                //     "contactName"           => $company_name,
                //     "orgName"               => $company_name,
                //     "phoneNumber"           => $formatPhoneNumber,
                //     "timeZone"              => "Asia/Manila",
                //     "geocoder"              => "Google",
                //     "language"              => "en",
                //     "postAddress"           => "N/A",
                //     "isDisabled"            => false,
                //     "isEmailConfirmed"      => true,
                //     "loginDate"             => 0,
                //     "mask"                  => 0,
                //     "interfacePermissions"  => 0,
                //     "flags"                 => "",
                //     "objectsCount"          => 0,
                //     "geozonesCount"         => 0
                // ];

                // $traxResponse = $this->userslib->registerInternal($traxPayload);

                // echo "<pre>";
                // print_r($traxResponse);
                // die;


                // Jika Traxroot gagal → rollback + delete logo + stop proses
                // if (!isset($traxResponse['status']) || $traxResponse['status'] != 200) {

                //     $this->db->trans_rollback();

                //     $file_path = FCPATH . "assets/dist/img/company_logo/" . $img;
                //     if (file_exists($file_path)) {
                //         unlink($file_path);
                //     }

                //     $this->session->set_flashdata('message',
                //         '<div class="alert alert-danger">
                //             <i class="fa-solid fa-circle-exclamation"></i>
                //             Failed to register on Traxroot API!
                //         </div>'
                //     );

                //     redirect('company-list');
                //     return;
                // }

                $data_create_akses_login = [
                    "Fullname" => $company_name,
                    "Email"    => $company_email,
                    "Password" => $company_pass,
                    "Role"     => 3
                ];

                $create_akses_login = $this->M_Global->insertid($data_create_akses_login, "UserLogin");

                if ($create_akses_login != false) {

                    $this->M_Global->update("ListCompany", "UserLoginID = '$create_akses_login' WHERE ListCompanyID = '$company' ");

                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Company created successfully</div>');
                } else {
                    // Rollback DB dan hapus file yang sudah diupload
                    $this->db->trans_rollback();
                    $file_path = FCPATH . "assets/dist/img/company_logo/" . $img;

                    if (file_exists($file_path)) {
                        unlink($file_path);
                    } 
                    $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Company!</div>');
                }
            } else {
                // Rollback DB dan hapus file yang sudah diupload
                $this->db->trans_rollback();
                $file_path = FCPATH . "assets/dist/img/company_logo/" . $img;

                if (file_exists($file_path)) {
                    unlink($file_path);
                } 
                $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Company!</div>');
            }
            
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Company, Email Is ready used!</div>');
        }
        

        redirect(base_url('company-list'));

    }

    public function update()
    {
        $company_id = $this->input->post('company_id');
        $user_login_id = $this->input->post('user_login_id');

        // echo json_encode($_FILES['company_logo']);
        // die;

        if($_FILES['company_logo']['name'] != '') {

            $config['upload_path']   = "./assets/dist/img/company_logo/";
            $config['allowed_types'] = 'jpeg|jpg|png|gif|bmp|mp4|mov|avi|wmv|webm|mkv';
            $config['encrypt_name']  = TRUE; // biar nama file unik

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            // Upload file
            if (!$this->upload->do_upload('company_logo')) {
                $error = ['error' => $this->upload->display_errors()];
                var_dump($error);
                $this->db->trans_rollback();
                die;
            } else {
                $data = $this->upload->data();
                $img = $data['file_name'];
            }
        }
        
        $formatPhoneNumber = "+63" . $this->input->post('company_phone');
        
        $data_create = [
            "CompanyName"       => $this->input->post('company_name'),
            "CompanyPhone"      => $formatPhoneNumber,
            "CompanyEmail"      => $this->input->post('company_email'),
            "CompanySubscribe"  => $this->input->post('package'),
            "updated_at"        => date('Y-m-d H:i:s')
        ];

        if($_FILES['company_logo']['name'] != '') {
            $data_create["CompanyLogo"] = base_url('assets/dist/img/company_logo/') . $img;
        }

        $where = " ListCompanyID =  '$company_id' ";
        // create user
        $job = $this->M_Global->update_data($where, $data_create, "ListCompany");

        $data_update_email_login = [
            "Email"      => $this->input->post('company_email'),
            "Fullname"       => $this->input->post('company_name'),
            "Password"       => $this->input->post('pass'),
        ];

        $where_login = " UserLoginID =  '$user_login_id' ";
        // create user
        $login = $this->M_Global->update_data($where_login, $data_update_email_login, "UserLogin");


        if ($job == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Company updated successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to updated Company!</div>');
        }

        redirect(base_url('company-list'));
    }

    public function delete()
    {
        $company_id = $this->input->post('company_id');
        $user_login_id = $this->input->post('user_login_id');

        $delete = $this->M_Global->delete('ListCompany' , "ListCompanyID = '$company_id' ");

        $delete_akses_login = $this->M_Global->delete('UserLogin' , "UserLoginID = '$user_login_id' ");

        if ($delete == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Company delete successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to delete Company!</div>');
        }

        redirect(base_url('company-list'));
    }

    public function getCompanyDetail()
    {
        $companyID = $this->input->post('companyID');

        $dataReturn = $this->M_Global->globalquery("SELECT ListCompany.*,
            Password
            FROM ListCompany
        LEFT JOIN UserLogin ON ListCompany.UserLoginID = UserLogin.UserLoginID
        WHERE ListCompanyID  = '$companyID'
        ")->row_array();

        echo json_encode($dataReturn);
    }


    // Update Profile Company (Traxroot)
    public function update_traxroot_profile()
    {
        $companyID = $this->input->post('company_id');
        $username  = $this->input->post('username_traxroot');
        $password  = $this->input->post('password_traxroot');

        $this->db->where('ListCompanyID', $companyID);
        $this->db->update('ListCompany', [
            'username_traxroot' => $username,
            'password_traxroot' => $password
        ]);

        $this->session->set_flashdata('swal', [
            'title' => 'Success',
            'text' => 'Traxroot profile updated!',
            'icon' => 'success'
        ]);

        redirect('company-list');
    }
}