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
        $data['title'] = "User";

        // Get and sanitize session data
        $dataRole  = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');

        // Superuser view
        if ($dataRole === 1) {
            $company_select = $this->input->post('company_select');
            $data_user = $this->getDataListDriverByCompany($dataRole, $company_select);
        } else {
            $data_user = $this->getDataListDriverByCompany($companyID);
        }

        $data['user'] = $data_user;

        // Fetch companies safely using Query Builder
        $data['list_company'] = $this->db->get('ListCompany')->result_array();

        $this->render_page('main/user/page_user', $data);
    }





    public function import_excel()
    {
        $this->load->library('Excel');

        try {
            if (empty($_FILES['import_excel']['tmp_name'])) {
                echo json_encode(['status' => false, 'message' => 'No file was uploaded.']);
                return;
            }

            $filePath = $_FILES['import_excel']['tmp_name'];
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            // Company & subscription (fetched once, outside loop)
            $companyID = $this->session->userdata('CompanyID');

            $companyInfo = $this->M_Global->globalquery(
                "SELECT CompanySubscribe FROM ListCompany WHERE ListCompanyID = ?",
                [$companyID]
            )->row_array();
            $companySubscribe = $companyInfo['CompanySubscribe'] ?? 1;

            $totalInserted  = 0;
            $email_ready    = [];
            $email_invalid  = [];
            $role_invalid   = [];
            $phone_invalid  = [];

            $this->db->trans_start();

            foreach ($sheetData as $key => $row) {
                if ($key == 1) continue; // Skip header row

                // Skip empty rows
                if (empty($row['A']) && empty($row['B']) && empty($row['C']) && empty($row['D'])) {
                    continue;
                }

                // Column mapping: A=Full Name, B=User Role, C=Email, D=Phone Number
                $fullname = trim($row['A']);
                $userRole = strtolower(trim($row['B']));
                $email    = strtolower(trim($row['C']));
                $phoneRaw = trim($row['D']);

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_invalid[] = $email ?: "(Row $key: empty)";
                    continue;
                }

                // Validate User Role
                if (!in_array($userRole, ['monitor', 'field'], true)) {
                    $role_invalid[] = "Row $key: \"" . htmlspecialchars($row['B']) . "\"";
                    continue;
                }

                // Enforce subscription-based role (Basic = monitor only)
                if ($companySubscribe == 1) {
                    $userRole = 'monitor';
                }

                // Normalize PH phone number (matching create())
                $digits = preg_replace('/\D/', '', $phoneRaw);
                $phone  = null;

                if (strlen($digits) === 11 && substr($digits, 0, 2) === '09') {
                    $phone = '+63' . substr($digits, 1);
                } elseif (strlen($digits) === 10 && substr($digits, 0, 1) === '9') {
                    $phone = '+63' . $digits;
                } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
                    $phone = '+' . $digits;
                }

                if ($phone === null) {
                    $phone_invalid[] = "Row $key: \"" . htmlspecialchars($phoneRaw) . "\"";
                    continue;
                }

                // Check duplicate email (matching create() — with deleted_at filter)
                $exists = $this->M_Global->globalquery(
                    "SELECT 1 FROM ListUser WHERE Email = ? AND ListCompanyID = ? AND deleted_at IS NULL",
                    [$email, $companyID]
                )->row_array();

                if ($exists) {
                    $email_ready[] = $email;
                    continue;
                }

                // Insert ListUser (matching create())
                $this->M_Global->insert([
                    'Fullname'      => $fullname,
                    'ListCompanyID' => $companyID,
                    'Email'         => $email,
                    'PhoneNumber'   => $phone,
                    'UserRole'      => $userRole,
                    'StatusActive'  => 0,
                    'created_at'    => date('Y-m-d H:i:s')
                ], 'ListUser');

                // Insert UserLogin (matching create())
                $loginID = $this->M_Global->insertid([
                    'Fullname' => $fullname,
                    'Email'    => $email,
                    'Password' => password_hash('12345678', PASSWORD_BCRYPT),
                    'Role'     => 2
                ], 'UserLogin');

                // Link login to user (matching create())
                $this->M_Global->globalquery(
                    "UPDATE ListUser SET UserLoginID = ? WHERE Email = ? AND ListCompanyID = ?",
                    [$loginID, $email, $companyID]
                );

                $totalInserted++;
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Database transaction failed. No riders were imported.'
                ]);
                return;
            }

            // Build response
            $hasErrors = !empty($email_ready) || !empty($email_invalid) || !empty($role_invalid) || !empty($phone_invalid);

            if (!$hasErrors) {
                echo json_encode([
                    'status'  => true,
                    'label'   => 'success',
                    'message' => "Successfully imported $totalInserted rider(s)."
                ]);
            } else {
                $messages = [];

                if ($totalInserted > 0) {
                    array_unshift($messages, "Successfully imported <strong>$totalInserted</strong> rider(s).");
                }

                if (!empty($email_ready)) {
                    $messages[] =
                        "Email already in use: <br><strong>" .
                        implode("</strong>, <strong>", $email_ready) . "</strong>";
                }

                if (!empty($email_invalid)) {
                    $messages[] =
                        "Invalid email format: <br><strong>" .
                        implode("</strong>, <strong>", $email_invalid) . "</strong>";
                }

                if (!empty($role_invalid)) {
                    $messages[] =
                        "Invalid User Role (must be 'monitor' or 'field'): <br><strong>" .
                        implode("</strong>, <strong>", $role_invalid) . "</strong>";
                }

                if (!empty($phone_invalid)) {
                    $messages[] =
                        "Invalid phone number: <br><strong>" .
                        implode("</strong>, <strong>", $phone_invalid) . "</strong>";
                }

                echo json_encode([
                    'status'  => true,
                    'label'   => ($totalInserted > 0) ? 'warning' : 'error',
                    'message' => implode("<br><br>", $messages)
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Failed to read Excel file: ' . $e->getMessage()
            ]);
        }
    }


    public function create() 
    {

        // --- Authorization ---
        if (!in_array((int) $this->session->userdata('Role'), [1, 3], true)) {
            show_error('Unauthorized', 403);
        }

        // --- Input ---
        $email      = strtolower(trim($this->input->post('email', true)));
        $phoneRaw   = $this->input->post('phone', true);
        $password   = $this->input->post('pass', true);
        $fullname   = trim($this->input->post('fullname', true));
        $companyID  = ((int) $this->session->userdata('Role') === 1)
            ? $this->input->post('company_selected')
            : $this->session->userdata('CompanyID');

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
            $this->audit_log('user.create', ['email' => $email, 'fullname' => $fullname]);
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
                $this->audit_log('user.update', ['user_id' => $userID]);
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
        $companyID = $this->session->userdata('CompanyID');
        $role = (int) $this->session->userdata('Role');

        if ($userID <= 0) {
            // Return empty if invalid
            echo json_encode([]);
            return;
        }

        // Fetch jobs for the user with Status = 1
        $this->db->from('ListJob');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID');
        $this->db->where('ListJob.UserID', $userID);
        $this->db->where('ListJob.Status', 1);
        if ($role !== 1) {
            $this->db->where('ListUser.ListCompanyID', $companyID);
        }
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
            $this->audit_log('user.delete', ['user_id' => $userID]);
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


    public function generate_rider_template()
    {
        $this->load->library('Excel');

        $spreadsheet = new Excel();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rider Template');

        // Headers
        $headers = ['Full Name', 'User Role', 'Email', 'Phone Number'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '333333']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'],
            ],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // User Role dropdown validation (column B)
        $validation = $sheet->getDataValidation('B2:B1000');
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setFormula1('"monitor,field"');
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Invalid Role');
        $validation->setError('User Role must be "monitor" or "field".');

        // Sample data
        $sampleData = [
            ['Juan Dela Cruz', 'monitor', 'juan.delacruz@email.com', '09171234567'],
            ['Maria Santos', 'field', 'maria.santos@email.com', '09189876543'],
        ];

        foreach ($sampleData as $rowIndex => $row) {
            foreach ($row as $col => $value) {
                $cell = chr(65 + $col) . ($rowIndex + 2);
                $sheet->setCellValue($cell, $value);
            }
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to static file
        $savePath = FCPATH . 'assets/dist/Example Excel Upload Rider.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($savePath);

        echo json_encode(['status' => true, 'message' => 'Template generated successfully.']);
    }


}