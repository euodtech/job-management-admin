<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company extends MY_Controller
{
        public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        // Check login session
        if ($this->session->userdata('status') != "kusam") {
            redirect(base_url('auth'));
        }

        // Load required libraries and models
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

        // Check if there are any errors in the sync log
        $hasError = false;
        foreach ($sync as $log) {
            if (strpos($log, '❌') !== false) {
                $hasError = true;
                break;
            }
        }

        // Set flashdata for user feedback
        $this->session->set_flashdata('swal', [
            'title' => $hasError ? 'Sync Failed' : 'Sync Completed',
            'text'  => $hasError ? 'Failed to sync Traxroot Users to database.' : 'Traxroot Users synchronized to database successfully.',
            'icon'  => $hasError ? 'error' : 'success'
        ]);

        // Log the sync results for debugging
        log_message('info', 'Traxroot Users Sync: ' . ($hasError ? 'Failed' : 'Success'));
        foreach ($sync as $log) {
            log_message('info', 'Sync Log: ' . $log);
        }

        redirect('company-list'); // Adjust redirect URL as needed
    }

    public function syncTraxrootUsersCompanyLogin()
    {
        $this->load->library('UsersLib');

        $this->userslib->syncTraxrootDataUsersCompanyLogin();

        // Set flashdata for SweetAlert
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

        // Use Query Builder for safer DB access
        $data['company'] = $this->db
            ->select('*')
            ->from('ListCompany')
            ->order_by('CompanyName', 'ASC')
            ->get()
            ->result_array();

        $this->render_page('main/company/page_company', $data); 
    }


    public function create()
    {
        $this->db->trans_begin();

        try {
            // Ambil dan validasi input
            $company_name  = trim($this->input->post('company_name', true));
            $company_phone = trim($this->input->post('company_phone', true));
            $company_email = trim($this->input->post('company_email', true));
            $company_pass  = $this->input->post('pass'); // Don't use XSS clean for passwords
            $package       = $this->input->post('package', true);

            // Validasi input tidak kosong
            if (empty($company_name) || empty($company_phone) || empty($company_email) || empty($company_pass) || empty($package)) {
                throw new Exception('All fields are required!');
            }

            // Validasi email format
            if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format!');
            }

            // Validasi panjang password minimum
            if (strlen($company_pass) < 8) {
                throw new Exception('Password must be at least 8 characters long!');
            }

            // Format Phone
            $formatPhoneNumber = "+63" . preg_replace('/[^0-9]/', '', $company_phone);

            // Generate Company Code
            $email_prefix = explode('@', $company_email)[0];
            $three_letters = strtoupper(substr($email_prefix, 0, 3));
            $random_number = rand(10000, 99999);
            $output_code = $three_letters . $random_number;

            // Cek email sudah terdaftar atau belum (menggunakan parameterized query)
            $this->db->where('CompanyEmail', $company_email);
            $cek_available_email = $this->db->get('ListCompany')->result_array();

            if (count($cek_available_email) > 0) {
                throw new Exception('Failed to create Company, Email is already used!');
            }

            // Konfigurasi upload
            $config['upload_path']   = "./assets/dist/img/company_logo/";
            $config['allowed_types'] = 'jpeg|jpg|png|gif|bmp';
            $config['max_size']      = 2048; // 2MB max
            $config['encrypt_name']  = TRUE;

            // Pastikan folder upload exists dan writable
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            // Upload file
            if (!$this->upload->do_upload('company_logo')) {
                $error = $this->upload->display_errors('', '');
                throw new Exception('Upload logo failed: ' . $error);
            }

            $data = $this->upload->data();
            $img = $data['file_name'];

            // Hash password menggunakan bcrypt
            $hashed_password = password_hash($company_pass, PASSWORD_BCRYPT);

            // Data untuk insert ke ListCompany
            $data_create = [
                "CompanyName"       => $company_name,
                "CompanyCode"       => $output_code,
                "CompanyPhone"      => $formatPhoneNumber,
                "CompanyEmail"      => $company_email,
                "CompanySubscribe"  => $package,
                "CompanyLogo"       => base_url('assets/dist/img/company_logo/') . $img,
                "created_at"        => date('Y-m-d H:i:s')
            ];

            // Insert company
            $company_id = $this->M_Global->insertid($data_create, "ListCompany");

            if (!$company_id) {
                throw new Exception('Failed to create Company!');
            }

            // Data untuk insert ke UserLogin dengan password yang sudah di-hash
            $data_create_akses_login = [
                "Fullname" => $company_name,
                "Email"    => $company_email,
                "Password" => $hashed_password, // Gunakan password yang sudah di-hash
                "Role"     => 3
            ];

            $user_login_id = $this->M_Global->insertid($data_create_akses_login, "UserLogin");

            if (!$user_login_id) {
                throw new Exception('Failed to create user login!');
            }

            // Update ListCompany dengan UserLoginID
            $update_data = ['UserLoginID' => $user_login_id];
            $this->db->where('ListCompanyID', $company_id);
            $update_result = $this->db->update('ListCompany', $update_data);

            if (!$update_result) {
                throw new Exception('Failed to link user login to company!');
            }

            // Commit transaction
            $this->db->trans_commit();
            $this->session->set_flashdata('message', 
                '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Company created successfully</div>'
            );

        } catch (Exception $e) {
            // Rollback transaction
            $this->db->trans_rollback();

            // Hapus file yang sudah diupload jika ada error
            if (isset($img)) {
                $file_path = FCPATH . "assets/dist/img/company_logo/" . $img;
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }

            // Set error message
            $this->session->set_flashdata('message', 
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> ' . $e->getMessage() . '</div>'
            );
        }

        redirect(base_url('company-list'));
    }

public function update()
{
    // Enforce POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        show_error('Invalid request method', 405);
    }

    $this->db->trans_begin(); // start transaction

    try {
        // -----------------------------
        // 1. Get and sanitize input
        // -----------------------------
        $company_id    = (int) $this->input->post('company_id');
        $user_login_id = (int) $this->input->post('user_login_id');
        $company_name  = trim($this->input->post('company_name', true));
        $company_phone = trim($this->input->post('company_phone', true));
        $company_email = trim($this->input->post('company_email', true));
        $password      = $this->input->post('pass'); // do not XSS-clean passwords
        $package       = $this->input->post('package', true);

        // -----------------------------
        // 2. Validate required fields
        // -----------------------------
        if ($company_id <= 0 || $user_login_id <= 0) {
            throw new Exception('Missing required data!');
        }

        if (empty($company_name) || empty($company_phone) || empty($company_email) || empty($package)) {
            throw new Exception('All fields except password are required!');
        }

        if (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format!');
        }

        if (!empty($password) && strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters!');
        }

        // -----------------------------
        // 3. Fetch existing company
        // -----------------------------
        $existing_company = $this->db
            ->where('ListCompanyID', $company_id)
            ->get('ListCompany')
            ->row_array();

        if (!$existing_company) {
            throw new Exception('Company not found!');
        }

        // -----------------------------
        // 4. Check email uniqueness
        // -----------------------------
        $email_exists = $this->db
            ->where('CompanyEmail', $company_email)
            ->where('ListCompanyID !=', $company_id)
            ->get('ListCompany')
            ->row_array();

        if ($email_exists) {
            throw new Exception('Email is already used by another company!');
        }

        // -----------------------------
        // 5. Format phone number
        // -----------------------------
        $formatPhoneNumber = "+63" . preg_replace('/[^0-9]/', '', $company_phone);

        // -----------------------------
        // 6. Handle logo upload
        // -----------------------------
        $img = '';
        $old_logo_path = '';

        if (!empty($_FILES['company_logo']['name'])) {
            $config['upload_path']   = "./assets/dist/img/company_logo/";
            $config['allowed_types'] = 'jpeg|jpg|png|gif|bmp';
            $config['max_size']      = 2048;
            $config['encrypt_name']  = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('company_logo')) {
                $error = $this->upload->display_errors('', '');
                throw new Exception('Logo upload failed: ' . $error);
            }

            $data = $this->upload->data();
            $img = $data['file_name'];

            // Store old logo path for deletion
            if (!empty($existing_company['CompanyLogo'])) {
                $old_logo_path = FCPATH . 'assets/dist/img/company_logo/' . basename($existing_company['CompanyLogo']);
            }
        }

        // -----------------------------
        // 7. Prepare company data
        // -----------------------------
        $data_company = [
            "CompanyName"      => $company_name,
            "CompanyPhone"     => $formatPhoneNumber,
            "CompanyEmail"     => $company_email,
            "CompanySubscribe" => $package,
            "updated_at"       => date('Y-m-d H:i:s')
        ];

        if (!empty($img)) {
            $data_company["CompanyLogo"] = base_url('assets/dist/img/company_logo/') . $img;
        }

        // -----------------------------
        // 8. Update company
        // -----------------------------
        $this->db->where('ListCompanyID', $company_id);
        if (!$this->db->update('ListCompany', $data_company)) {
            throw new Exception('Failed to update company data!');
        }

        // -----------------------------
        // 9. Prepare UserLogin update
        // -----------------------------
        $akses_login = [
            "Fullname" => $company_name,
            "Email"    => $company_email
        ];

        if (!empty($password)) {
            $akses_login["Password"] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->where('UserLoginID', $user_login_id);
        if (!$this->db->update('UserLogin', $akses_login)) {
            throw new Exception('Failed to update login credentials!');
        }

        // -----------------------------
        // 10. Commit transaction
        // -----------------------------
        $this->db->trans_commit();

        // Delete old logo AFTER commit
        if (!empty($old_logo_path) && file_exists($old_logo_path)) {
            @unlink($old_logo_path);
        }

        $this->session->set_flashdata('message', 
            '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Company updated successfully</div>'
        );

    } catch (Exception $e) {
        $this->db->trans_rollback();

        // Delete newly uploaded file if transaction failed
        if (!empty($img)) {
            $file_path = FCPATH . 'assets/dist/img/company_logo/' . $img;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
        }

        // Log error
        log_message('error', 'Company update failed: ' . $e->getMessage());

        $this->session->set_flashdata('message', 
            '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> ' . $e->getMessage() . '</div>'
        );
    }

    redirect(base_url('company-list'));
}


    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_error('Invalid request method', 405);
        }

        $company_id    = (int) $this->input->post('company_id', true);
        $user_login_id = (int) $this->input->post('user_login_id', true);

        if ($company_id <= 0 || $user_login_id <= 0) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">Invalid request123.</div>'
            );
            redirect(base_url('company-list'));
            return;
        }

        $this->db->trans_begin();

        $delete = $this->M_Global->delete(
            'ListCompany',
            ['ListCompanyID' => $company_id]
        );

        $this->M_Global->delete(
            'UserLogin',
            ['UserLoginID' => $user_login_id]
        );

        if ($this->db->trans_status() === false || $delete !== 'success') {
            $this->db->trans_rollback();
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> Failed to delete Company!
                </div>'
            );
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> Company deleted successfully
                </div>'
            );
        }

        redirect(base_url('company-list'));
    }



    public function getCompanyDetail()
    {
        // Enforce POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_error('Invalid request method', 405);
        }

        // Sanitize & validate input
        $companyID = (int) $this->input->post('companyID', true);

        if ($companyID <= 0) {
            echo json_encode(null);
            return;
        }

        // Use Query Builder (prevents SQL injection)
        $dataReturn = $this->db
            ->select('ListCompany.*, UserLogin.Password')
            ->from('ListCompany')
            ->join('UserLogin', 'ListCompany.UserLoginID = UserLogin.UserLoginID', 'left')
            ->where('ListCompanyID', $companyID)
            ->get()
            ->row_array();

        // Force JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($dataReturn));
    }


    // Kian: To improve
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