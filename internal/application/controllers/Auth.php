<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    

    public function __construct()
    {
        parent::__construct();
    
        $this->load->library('form_validation');
        $this->load->model('M_Global');
        $this->load->library('curl');
    }

    public function index()
{
    // Redirect if already logged in
    if ($this->session->userdata('status') === 'kusam') {
        redirect(base_url('home'));
        return;
    }

    $data['title'] = "Login"; // define $data

    // Check if form submitted
    if ($this->input->server('REQUEST_METHOD') === 'POST') {

        // Set validation rules if not already set
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Validation failed, show login page
            $this->render_page_login('auth/login/page_login', $data);
            return;
        }

        // Validation success
        $this->login(); // call your login method

    } else {
        // GET request → just show login page
        $this->render_page_login('auth/login/page_login', $data);
    }
}


    public function login()
    {
        // Get input with XSS filtering
        $email    = trim($this->input->post('email', true));
        $password = $this->input->post('password'); // Don't use XSS clean for passwords

        // Validate input
        if (empty($email) || empty($password)) {
            $this->session->set_flashdata('message', 
                '<div class="alert alert-sm alert-danger" role="alert">Email and password are required!</div>'
            );
            redirect('auth');
            return;
        }

        // Use parameterized query to prevent SQL injection
        $user = $this->M_Global->globalquery(
            "SELECT *
            FROM UserLogin
            LEFT JOIN ListCompany ON UserLogin.UserLoginID = ListCompany.UserLoginID
            WHERE Email = ?",
            [$email]
        )->row_array();

        if ($user) {
            
            $stored_password = $user['Password'];
            $is_valid_password = false;

            // Check if password is bcrypt hashed (starts with $2y$ and is 60 chars)
            if (substr($stored_password, 0, 4) === '$2y$' && strlen($stored_password) === 60) {
                // Password is already hashed - verify using password_verify
                $is_valid_password = password_verify($password, $stored_password);
            } else {
                // Password is still plain text - compare directly
                if ($password == $stored_password) {
                    $is_valid_password = true;
                    
                    // AUTO-MIGRATE: Hash the password for future logins
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $this->db->where('UserLoginID', $user['UserLoginID']);
                    $this->db->update('UserLogin', ['Password' => $hashed_password]);
                }
            }

            if ($is_valid_password) {
                
                // Set session data - maintain original logic
                $data = [
                    'AdminID'           => $user['UserLoginID'],
                    'CompanyID'         => $user['ListCompanyID'],
                    'CompanySubscribe'  => $user['CompanySubscribe'],
                    'CompanyCode'       => $user['CompanyCode'],
                    'CompanyLogo'       => $user['CompanyLogo'],
                    'Fullname'          => $user['Fullname'],
                    'Role'              => $user['Role'],
                    'status'            => 'kusam'
                ];

                $this->session->set_userdata($data);

                // Set traxroot credentials if they exist (decrypt from DB)
                if (!empty($user['username_traxroot'])) {
                    $encKey = $this->config->item('encryption_key');
                    $data = base64_decode($user['username_traxroot'], true);
                    if ($data !== false && strlen($data) >= 17) {
                        $decrypted_user = openssl_decrypt(substr($data, 16), 'AES-256-CBC', $encKey, OPENSSL_RAW_DATA, substr($data, 0, 16));
                    } else {
                        $decrypted_user = false;
                    }
                    $this->session->set_userdata('traxroot_username', $decrypted_user !== false ? $decrypted_user : $user['username_traxroot']);
                }
                if (!empty($user['password_traxroot'])) {
                    $data = base64_decode($user['password_traxroot'], true);
                    if ($data !== false && strlen($data) >= 17) {
                        $decrypted_pass = openssl_decrypt(substr($data, 16), 'AES-256-CBC', $encKey, OPENSSL_RAW_DATA, substr($data, 0, 16));
                    } else {
                        $decrypted_pass = false;
                    }
                    $this->session->set_userdata('traxroot_password', $decrypted_pass !== false ? $decrypted_pass : $user['password_traxroot']);
                }

                // Update last login - use Query Builder for security
                $date = date('Y-m-d H:i:s');
                $this->db->where('Email', $email);
                $this->db->update('UserLogin', ['LastLogin' => $date]);

                // Audit log: successful login
                $this->audit_log('login.success', ['email' => $email]);

                // Redirect to home
                redirect(base_url('home'));

            } else {
                // Audit log: failed login (wrong password)
                $this->audit_log('login.failed', ['email' => $email, 'reason' => 'wrong_password']);

                // Wrong password
                $this->session->set_flashdata('message',
                    '<div class="alert alert-danger" role="alert">Wrong password!</div>'
                );
                redirect('auth');
            }

        } else {
            // Audit log: failed login (user not found)
            $this->audit_log('login.failed', ['email' => $email, 'reason' => 'user_not_found']);

            // User not found
            $this->session->set_flashdata('message',
                '<div class="alert alert-sm alert-danger" role="alert">Incorect Email & Password!</div>'
            );
            redirect('auth');
        }
    }

    public function keepalive()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('status') !== 'kusam') {
            echo json_encode(['alive' => false]);
            return;
        }

        // Touch the session to reset its expiration timer
        $this->session->set_userdata('last_activity', time());

        echo json_encode(['alive' => true, 'expires_in' => config_item('sess_expiration')]);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    public function forgot_password()
    {
        // Success redirect via flashdata (replaces ?token=success_update_password hack)
        if ($this->session->flashdata('reset_success')) {
            $this->load->view('main/success_forgot_password');
            return;
        }

        // Token from URL (initial click) or flashdata (validation failure redirect)
        $token = $this->input->get('token', TRUE);
        if (empty($token)) {
            $token = $this->session->flashdata('reset_token');
        }

        if (empty($token)) {
            $this->load->view('main/expired_forgot_password');
            return;
        }

        // Validate token exists in DB
        $user = $this->M_Global->globalquery(
            "SELECT UserLoginID FROM UserLogin WHERE key_resetpassword = ?",
            [$token]
        )->row_array();

        if (!$user) {
            $this->load->view('main/expired_forgot_password');
            return;
        }

        $data = [
            'token' => $token,
            'error' => $this->session->flashdata('reset_error'),
        ];

        $this->load->view('main/forgot_password', $data);
    }

    public function submit_new_password()
    {
        $new_password    = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        $token           = $this->input->post('reset_token');

        // Token is required — reject tampered requests
        if (empty($token)) {
            $this->load->view('main/expired_forgot_password');
            return;
        }

        // Validate passwords are not empty
        if (empty($new_password) || empty($confirm_password)) {
            $this->session->set_flashdata('reset_error', 'Please fill in both password fields.');
            $this->session->set_flashdata('reset_token', $token);
            redirect(base_url('forgot-password'));
            return;
        }

        // Validate password length
        if (strlen($new_password) < 8) {
            $this->session->set_flashdata('reset_error', 'Password must be at least 8 characters long.');
            $this->session->set_flashdata('reset_token', $token);
            redirect(base_url('forgot-password'));
            return;
        }

        // Validate passwords match
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('reset_error', 'Passwords do not match.');
            $this->session->set_flashdata('reset_token', $token);
            redirect(base_url('forgot-password'));
            return;
        }

        // Re-validate token against DB (security: prevents user_login_id tampering)
        $user = $this->M_Global->globalquery(
            "SELECT UserLoginID FROM UserLogin WHERE key_resetpassword = ?",
            [$token]
        )->row_array();

        if (!$user) {
            $this->load->view('main/expired_forgot_password');
            return;
        }

        // Update password and clear token
        $data_update = [
            "Password"          => password_hash($new_password, PASSWORD_BCRYPT),
            "key_resetpassword" => null
        ];

        $this->db->where('UserLoginID', $user['UserLoginID']);
        if (!$this->db->update('UserLogin', $data_update)) {
            $this->session->set_flashdata('reset_error', 'Failed to update password. Please try again.');
            $this->session->set_flashdata('reset_token', $token);
            redirect(base_url('forgot-password'));
            return;
        }

        // Success
        $this->session->set_flashdata('reset_success', true);
        redirect(base_url('forgot-password'));
    }

    // ─── Profile Management ──────────────────────────────────────────

    public function get_profile()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('status') !== 'kusam') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $adminID = (int) $this->session->userdata('AdminID');

        $profile = $this->M_Global->globalquery(
            "SELECT ul.UserLoginID, ul.Fullname, ul.Email, ul.LastLogin, ul.Role,
                    lc.CompanyName, lc.CompanyCode, lc.CompanySubscribe
             FROM UserLogin ul
             LEFT JOIN ListCompany lc ON ul.UserLoginID = lc.UserLoginID
             WHERE ul.UserLoginID = ?",
            [$adminID]
        )->row_array();

        if (!$profile) {
            echo json_encode(['status' => 'error', 'message' => 'Profile not found']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'Fullname'         => $profile['Fullname'],
                'Email'            => $profile['Email'],
                'LastLogin'        => $profile['LastLogin'],
                'Role'             => $profile['Role'],
                'CompanyName'      => $profile['CompanyName'] ?? '-',
                'CompanyCode'      => $profile['CompanyCode'] ?? '-',
                'CompanySubscribe' => $profile['CompanySubscribe'] ?? 1,
            ]
        ]);
    }

    public function update_profile()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('status') !== 'kusam') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $adminID  = (int) $this->session->userdata('AdminID');
        $fullname = trim($this->input->post('fullname', true));
        $email    = strtolower(trim($this->input->post('email', true)));

        $errors = [];

        if (empty($fullname)) {
            $errors['fullname'] = 'Full name is required.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }

        // Check email uniqueness (exclude self)
        if (empty($errors['email'])) {
            $existing = $this->M_Global->globalquery(
                "SELECT UserLoginID FROM UserLogin WHERE Email = ? AND UserLoginID != ?",
                [$email, $adminID]
            )->row_array();

            if ($existing) {
                $errors['email'] = 'This email is already in use by another account.';
            }
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'validation_error', 'errors' => $errors]);
            return;
        }

        $this->db->trans_start();

        $this->db->where('UserLoginID', $adminID);
        $this->db->update('UserLogin', [
            'Fullname' => $fullname,
            'Email'    => $email,
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
            return;
        }

        // Refresh session
        $this->session->set_userdata('Fullname', $fullname);

        $this->audit_log('profile.update', ['admin_id' => $adminID, 'email' => $email]);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Profile updated successfully.',
            'data'    => ['Fullname' => $fullname, 'Email' => $email]
        ]);
    }

    public function change_password()
    {
        header('Content-Type: application/json');

        if ($this->session->userdata('status') !== 'kusam') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $adminID         = (int) $this->session->userdata('AdminID');
        $currentPassword = $this->input->post('current_password');
        $newPassword     = $this->input->post('new_password');
        $confirmPassword = $this->input->post('confirm_password');

        $errors = [];

        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required.';
        }
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            echo json_encode(['status' => 'validation_error', 'errors' => $errors]);
            return;
        }

        // Verify current password
        $user = $this->M_Global->globalquery(
            "SELECT Password FROM UserLogin WHERE UserLoginID = ?",
            [$adminID]
        )->row_array();

        if (!$user || !password_verify($currentPassword, $user['Password'])) {
            echo json_encode(['status' => 'validation_error', 'errors' => [
                'current_password' => 'Current password is incorrect.'
            ]]);
            return;
        }

        $this->db->where('UserLoginID', $adminID);
        $this->db->update('UserLogin', [
            'Password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        $this->audit_log('profile.password_change', ['admin_id' => $adminID]);

        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
    }
}