<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('status') != "kusam") {
            redirect(base_url('auth'));
        }

        $this->load->library('form_validation');
        $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->helper('idr_helper');
        
    }

    private function getDataListDriverByCompany($from_get, $company_select = null)
    {
        // Sanitize input
        $from_get = (int) $from_get;
        if ($company_select !== null && $company_select !== 'all') {
            $company_select = (int) $company_select;
        } else {
            $company_select = null;
        }

        // Start building query
        $this->db->select('ListUser.*, ListCompany.CompanyName');
        $this->db->from('ListUser');
        $this->db->join('ListCompany', 'ListUser.ListCompanyID = ListCompany.ListCompanyID', 'left');
        $this->db->where('ListUser.StatusActive', 0);

        // Superuser filter
        if ($from_get === 1 && $company_select !== null) {
            $this->db->where('ListUser.ListCompanyID', $company_select);
        }

        // Normal user filter
        if ($from_get !== 1) {
            $this->db->where('ListUser.ListCompanyID', $from_get);
        }

        $dataUser = $this->db->get()->result_array();

        return $dataUser;
    }


    public function index()
    {
        $data['title'] = "Efms | User";

        // Get and sanitize session data
        $dataRole  = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');

        // Superuser view
        if ($dataRole === 1) {
            $company_select = (int) $this->input->post('company_select'); // sanitize input
            $data_user = $this->getDataListDriverByCompany($dataRole, $company_select);
        } else {
            $data_user = $this->getDataListDriverByCompany($companyID);
        }

        $data['user'] = $data_user;

        // Fetch companies safely using Query Builder
        $data['list_company'] = $this->db->get('ListCompany')->result_array();

        $this->render_page('main/user/page_user', $data);
    }



    public function submit_new_password()
    {
        // Get and sanitize input
        $password       = $this->input->post('confirm_password');
        $user_login_id  = (int) $this->input->post('user_login_id');

        // Validate input
        if ($user_login_id <= 0 || empty($password)) {
            $this->session->set_flashdata('message', 
                '<div class="alert alert-danger">Invalid request or empty password!</div>'
            );
            redirect(base_url('forgot-password'));
            return;
        }

        // Optional: validate password length
        if (strlen($password) < 8) {
            $this->session->set_flashdata('message', 
                '<div class="alert alert-danger">Password must be at least 8 characters long!</div>'
            );
            redirect(base_url('forgot-password'));
            return;
        }

        // Prepare data
        $data_update = [
            "Password"          => password_hash($password, PASSWORD_BCRYPT),
            "key_resetpassword" => null
        ];

        // Update safely using Query Builder
        $this->db->where('UserLoginID', $user_login_id);
        if (!$this->db->update('UserLogin', $data_update)) {
            $this->session->set_flashdata('message', 
                '<div class="alert alert-danger">Failed to update password. Please try again.</div>'
            );
            redirect(base_url('forgot-password'));
            return;
        }

        // Success redirect
        redirect(base_url('forgot-password?token=success_update_password'));
    }


    public function import_excel()
    {
        $this->load->library('Excel');

        try {
            if (empty($_FILES['import_excel']['tmp_name'])) {
                echo json_encode(['status' => 'error', 'message' => 'Tidak ada file yang diupload.']);
                return;
            }

            $filePath = $_FILES['import_excel']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            $totalInsertData = 1;
            $totalData = 1;

            $email_ready = [];
            $email_invalid = [];

            foreach ($sheetData as $key => $row) {
                if ($key == 1) continue; // HEADER

                if (empty($row['A']) && empty($row['B']) && empty($row['C']) && empty($row['D'])) {
                    continue;
                }

                $companyID = $this->session->userdata('CompanyID');
                $email = trim($row['B']);

                // VALIDASI EMAIL FORMAT
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_invalid[] = $email;
                    continue;
                }

                // CEK EMAIL SUDAH ADA DI DB
                $queryCheck = "SELECT * FROM ListUser 
                    WHERE ListCompanyID = '$companyID' AND Email = '$email'";
                $dataCheck = $this->M_Global->globalquery($queryCheck)->result_array();

                if (count($dataCheck) == 0) {

                    // INSERT LOGIN DULU
                    $insert_data_login = [
                        'Fullname' => $row['A'],
                        'Email'    => $email,
                        "Password" => password_hash("12345", PASSWORD_BCRYPT),
                        "Role"     => 2,
                    ];

                    $user_login_id = $this->M_Global->insertid($insert_data_login, "UserLogin");

                    // INSERT LIST USER
                    $insertData = [
                        'Fullname'          => $row['A'],
                        'ListCompanyID'     => $companyID,
                        'Email'             => $email,
                        'PhoneNumber'       => $row['C'],
                        'StatusActive'      => 0,
                        'Category'          => $row['D'],
                        'Rank'              => $row['E'],
                        'License'           => $row['F'],
                        'LicenseValidUntil' => $row['G'],
                        "UserLoginID"       => $user_login_id,
                        'created_at'        => date('Y-m-d H:i:s')
                    ];

                    $this->M_Global->insert($insertData, "ListUser");
                    $totalInsertData++;

                } else {
                    // EMAIL DUPLIKAT
                    $email_ready[] = $email;
                }

                $totalData++;
            }

            // ===========================
            //   HANDLE RESPONSE
            // ===========================

            if (empty($email_ready) && empty($email_invalid)) {
                echo json_encode([
                    'status' => true,
                    'label' => 'success',
                    'message' => 'Success Insert All Data'
                ]);
            } else {

                $messages = [];

                if (!empty($email_ready)) {
                    $messages[] =
                        "Already in use: <br><strong>" .
                        implode("</strong>, <strong>", $email_ready) . "</strong>";
                }

                if (!empty($email_invalid)) {
                    $messages[] =
                        "Invalid format: <br><strong>" .
                        implode("</strong>, <strong>", $email_invalid) . "</strong>";
                }

                echo json_encode([
                    'status' => true,
                    'label' => 'warning',
                    'message' => implode("<br><br>", $messages)
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Gagal membaca file Excel: ' . $e->getMessage()
            ]);
        }
    }


    public function create() 
    {

        // --- Authorization ---
        if ($this->session->userdata('Role') != 1) {
            show_error('Unauthorized', 403);
        }

        // --- Input ---
        $email      = strtolower(trim($this->input->post('email', true)));
        $phoneRaw   = $this->input->post('phone', true);
        $password   = $this->input->post('pass', true);
        $fullname   = trim($this->input->post('fullname', true));
        $companyID  = $this->input->post('company_selected');

        // --- Basic validation ---
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid email address</div>');
            redirect('user-list');
        }

        if (empty($password)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Password is required</div>');
            redirect('user-list');
        }

        $phoneRaw = $this->input->post('phone', true);
        $digits   = preg_replace('/\D/', '', $phoneRaw);

        // PH mobile normalization
        if (strlen($digits) === 11 && substr($digits, 0, 2) === '09') {
            // 09123456789 → +639123456789
            $phone = '+63' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && substr($digits, 0, 1) === '9') {
            // 9123456789 → +639123456789
            $phone = '+63' . $digits;
        } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            // 639123456789 → +639123456789
            $phone = '+' . $digits;
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">Invalid Philippine mobile number</div>'
            );
            redirect('user-list');
        }

        // --- Subscription-based role enforcement ---
        $companyInfo = $this->M_Global->globalquery(
            "SELECT CompanySubscribe FROM ListCompany WHERE ListCompanyID = ?",
            [$companyID]
        )->row_array();

        $companySubscribe = $companyInfo['CompanySubscribe'] ?? 1;
        $postedRole       = $this->input->post('user_role', true);

        if ($companySubscribe == 1) { // Basic
            $user_role = 'monitor';
        } else {
            $user_role = in_array($postedRole, ['monitor', 'field']) ? $postedRole : 'monitor';
        }

        // --- Check duplicate email ---
        $exists = $this->M_Global->globalquery(
            "SELECT 1 FROM ListUser WHERE Email = ? AND ListCompanyID = ? AND deleted_at IS NULL",
            [$email, $companyID]
        )->row_array();

        if ($exists) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">Email is already in use</div>'
            );
            redirect('user-list');
        }

        // --- Transaction ---
        $this->db->trans_start();

        // Create user
        $this->M_Global->insert([
            'Fullname'      => $fullname,
            'ListCompanyID' => $companyID,
            'Email'         => $email,
            'PhoneNumber'   => $phone,
            'UserRole'      => $user_role,
            'StatusActive'  => 0,
            'created_at'    => date('Y-m-d H:i:s')
        ], 'ListUser');

        // Create login
        $loginID = $this->M_Global->insertid([
            'Fullname' => $fullname,
            'Email'    => $email,
            'Password' => password_hash($password, PASSWORD_BCRYPT),
            'Role'     => 2
        ], 'UserLogin');

        // Link login to user
        $this->M_Global->globalquery(
            "UPDATE ListUser SET UserLoginID = ? WHERE Email = ? AND ListCompanyID = ?",
            [$loginID, $email, $companyID]
        );

        $this->db->trans_complete();

        // --- Result ---
        if ($this->db->trans_status() === FALSE || empty($loginID)) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger">Failed to create user</div>'
            );
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success">User created successfully</div>'
            );
        }

        redirect('user-list');
    }


    public function update()
    {
        if (!in_array((int)$this->session->userdata('Role'), [1, 3], true)) {
            show_error('Unauthorized access', 403);
        }

        $pass = $this->input->post('pass');
        
        $userID = (int) $this->input->post('user_id', true);
        $email  = filter_var($this->input->post('email', true), FILTER_VALIDATE_EMAIL);
        $phone  = preg_replace('/[^0-9]/', '', $this->input->post('phone'));

        if (!$userID || !$email) {
            show_error('Invalid input', 400);
        }

        $companyID = $this->session->userdata('CompanyID');
        $selected_company = ($this->session->userdata("Role") != 1) ? $companyID : $this->input->post('company_selected');

        // 1. SECURE QUERY: Using bindings to prevent SQL Injection
        $sqlUser = "SELECT UserLoginID FROM ListUser WHERE UserID = ?";
        $userRow = $this->db->query($sqlUser, [$userID])->row_array();
        
        if (!$userRow) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">User not found.</div>');
            redirect(base_url('user-list'));
            return;
        }

        $userLoginID = $userRow['UserLoginID'];

        // 2. SECURE QUERY: Get company subscription info
        $sqlCompany = "SELECT CompanySubscribe FROM ListCompany WHERE ListCompanyID = ?";
        // $companyInfo = $this->db->query($sqlCompany, [$selected_company])->row_array();
        $companyInfo = $this->M_Global->globalquery(
            "SELECT CompanySubscribe FROM ListCompany WHERE ListCompanyID = ?",
            [$selected_company]
        )->row_array();

        
        $companySubscribe = isset($companyInfo['CompanySubscribe']) ? $companyInfo['CompanySubscribe'] : null;
        $user_role_post = $this->input->post('user_role');

        if ($companySubscribe == 1) { 
            $user_role = 'monitor';
        } else {
            $user_role = in_array($user_role_post, ['monitor','field']) ? $user_role_post : 'monitor';
        }

        // Prepare data for ListUser table
        $data_update = [
            "Fullname"      => $this->input->post('fullname'),
            "Email"         => $email,
            "ListCompanyID" => $selected_company,
            "PhoneNumber"   => $phone,
            "UserRole"      => $user_role,
            "updated_at"    => date('Y-m-d H:i:s') // Updated column name
        ];

        $whereUser = ["UserID" => $userID];
        $userStatus = $this->M_Global->update_data($whereUser, $data_update, "ListUser");

        if($userStatus == "success") {

            // Prepare data for UserLogin table
            $akses_login = [
                "Fullname" => $data_update['Fullname'],
                "Email"    => $data_update['Email'],
            ];

            // 3. LOGIC FIX: Only hash if a new password was typed
            if (!empty($pass)) {
                $akses_login["Password"] = password_hash($pass, PASSWORD_BCRYPT);
            }

            $whereLogin = ["UserLoginID" => $userLoginID];
            $create_akses = $this->M_Global->update_data($whereLogin, $akses_login, "UserLogin");

            if ($create_akses == "success") {
                $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> User updated successfully</div>');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to update Login Access!</div>');
            }

        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to update User details!</div>');
        }

        redirect(base_url('user-list'));
    }
    
    public function get_data_user_for_delete($userID)
    {
        // Sanitize input
        $userID = (int) $userID;

        if ($userID <= 0) {
            // Return empty if invalid
            echo json_encode([]);
            return;
        }

        // Fetch jobs for the user with Status = 1
        $this->db->from('ListJob');
        $this->db->where('UserID', $userID);
        $this->db->where('Status', 1);
        $data_detail_job = $this->db->get()->row_array();

        echo json_encode($data_detail_job ?: []);
    }


    public function delete()
    {
        // ===== AUTHORIZATION CHECK =====
        if (!in_array((int) $this->session->userdata('Role'), [1, 3], true)) {
            show_error('Unauthorized access', 403);
        }


        // ===== INPUT VALIDATION =====
        $userID      = (int) $this->input->post('user_id', true);
        $current_job = (int) $this->input->post('current_job', true);

        if (!$userID) {
            show_error('Invalid User ID', 400);
        }

        // ===== START TRANSACTION =====
        $this->db->trans_start();

        // ===== JOB UPDATE =====
        if ($current_job > 0) {
            $this->db->where('JobID', $current_job)
                    ->update('ListJob', [
                        'UserID'     => null,
                        'Status'     => null,
                        'AssignWhen' => null
                    ]);
        }

        // ===== GET USER EMAIL (SAFE QUERY) =====
        $emailUser = $this->db->select('Email')
                            ->from('ListUser')
                            ->where('UserID', $userID)
                            ->get()
                            ->row_array();

        if (!$emailUser) {
            $this->db->trans_rollback();
            show_error('User not found', 404);
        }

        // ===== SOFT DELETE USER =====
        $this->db->where('UserID', $userID)
                ->update('ListUser', [
                    'StatusActive' => 1,
                    'deleted_at'   => date('Y-m-d H:i:s'),
                ]);

        // ===== DELETE LOGIN ACCESS =====
        $this->db->where('Email', $emailUser['Email'])
                ->delete('UserLogin');

        // ===== TRANSACTION END =====
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('message',
                '<div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> Failed to delete user
                </div>'
            );
        } else {
            $this->session->set_flashdata('message',
                '<div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> User deleted successfully
                </div>'
            );
        }

        redirect(base_url('user-list'));
    }


    public function getUser()
    {
        // Get and sanitize input
        $userID = (int) $this->input->post('userID'); // cast to int to prevent injection

        if ($userID <= 0) {
            // Return empty response if invalid
            echo json_encode([]);
            return;
        }

        // Fetch user data safely using Query Builder
        $this->db->select('ListUser.*, UserLogin.UserLoginID'); // don't select Password
        $this->db->from('ListUser');
        $this->db->join('UserLogin', 'ListUser.UserLoginID = UserLogin.UserLoginID', 'left');
        $this->db->where('UserID', $userID);
        $dataReturn = $this->db->get()->row_array();

        // Return JSON
        echo json_encode($dataReturn ?: []);
    }


}