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
        //$data['title_login'] = "Ombe Kofie | Administrator";
     
        if ($this->form_validation->run() == false) {           
       
            $this->render_page_login('auth/login/page_login', $data);
       
        } else {
            //validation success
            echo "qwe";
            exit();
            $this->login();
        }
        
    }

    public function login()
    {

        $email      = $this->input->post('email');
        $password   = $this->input->post('password');
 
        // $user       = $this->M_Global->globalquery("SELECT * FROM UserLogin LEFT JOIN ListCompany ON UserLogin.UserLoginID = ListCompany.UserLoginID Where Email = '$email' ")->row_array();
        $user = $this->M_Global->globalquery(
            "SELECT *
            FROM UserLogin
            LEFT JOIN ListCompany ON UserLogin.UserLoginID = ListCompany.UserLoginID
            WHERE Email = ?",
            [$email]
        )->row_array();


        if ($user) {

            if ($password == $user['Password']) {
                
                $data = [
                    'AdminID' => $user['UserLoginID'],
                    'CompanyID' => $user['ListCompanyID'],
                    'CompanySubscribe' => $user['CompanySubscribe'],
                    'CompanyCode' => $user['CompanyCode'],
                    'Fullname'   => $user['Fullname'],
                    'Role'   => $user['Role'],
                    'status' => 'kusam'
                ];

                $this->session->set_userdata($data);

                $this->session->set_userdata('traxroot_username', $user['username_traxroot']);
                $this->session->set_userdata('traxroot_password', $user['password_traxroot']);

                $date = date('Y-m-d H:i:s');
                $this->M_Global->update("UserLogin",
                                        "LastLogin = '$date' WHERE Email = '$email' ");

                // echo json_encode($data);
                // exit();


                redirect(base_url('home'));
              
                

            }else{

                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password!</div>');
                redirect('auth');
            
            }

        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-sm alert-danger" role="alert">Incorect Email & Password!</div>');
            redirect('auth');
        }

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}