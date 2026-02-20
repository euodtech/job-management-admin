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
}