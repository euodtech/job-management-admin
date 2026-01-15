<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends MY_Controller
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
        $this->load->helper('date_format_helper');
    }


    public function index()
    {
        $data['title'] = "Efms | Customer";

        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $additional_where_customer = " ";

        if($role != 1) {

            $additional_where_customer = " WHERE Customer.ListCompanyID = " . $companyID;


        }

        $data['customer'] = $this->M_Global->globalquery("SELECT * FROM  Customer LEFT JOIN ListCompany ON Customer.ListCompanyID = ListCompany.ListCompanyID $additional_where_customer 
            ORDER BY CustomerID DESC")->result_array();

        $data['list_company'] = $this->M_Global->globalquery("SELECT * FROM ListCompany")->result_array();

        $this->render_page('main/customer/page_customer',$data);
    }

    public function import_excel()
    {
        $this->load->library('Excel');

        if (empty($_FILES['import_excel']['tmp_name'])) {
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
            return;
        }

        $filePath = $_FILES['import_excel']['tmp_name'];

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $companyID = $this->session->userdata('CompanyID');
            $totalInsertData = 0;
            $totalData = 0;

            $email_ready   = []; // email sudah ada
            $email_invalid = []; // format salah

            for ($row = 3; $row <= $highestRow; $row++) {

                $customerName  = trim($sheet->getCell("A$row")->getFormattedValue());
                $customerEmail = strtolower(trim($sheet->getCell("B$row")->getValue()));
                $phoneNumber   = trim($sheet->getCell("C$row")->getFormattedValue());
                $address       = trim($sheet->getCell("D$row")->getFormattedValue());
                $latitude      = trim((string)$sheet->getCell("E$row")->getFormattedValue());
                $longitude     = trim((string)$sheet->getCell("F$row")->getFormattedValue());

                // Skip kosong
                if (empty($customerName) && empty($customerEmail) && empty($phoneNumber)) {
                    continue;
                }

                // ❌ Validasi format email
                if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $email_invalid[] = $customerEmail;
                    continue;
                }

                // ❌ Cek email sudah ada
                $checkQuery = "
                    SELECT * FROM Customer 
                    WHERE ListCompanyID='$companyID' AND CustomerEmail='$customerEmail'
                ";
                $checkEmail = $this->M_Global->globalquery($checkQuery)->result_array();

                if (count($checkEmail) > 0) {
                    $email_ready[] = $customerEmail;
                    continue;
                }

                // Insert data baru
                $insertData = [
                    'CustomerName'  => $customerName,
                    'ListCompanyID' => $companyID,
                    'CustomerEmail' => $customerEmail,
                    'PhoneNumber'   => $phoneNumber,
                    'Address'       => $address,
                    'Latitude'      => $latitude,
                    'Longitude'     => $longitude,
                    'created_at'    => date('Y-m-d H:i:s')
                ];

                $createCustomer = $this->M_Global->insert($insertData, "Customer");

                if ($createCustomer == "success") {
                    $totalInsertData++;
                }

                $totalData++;
            }

            // Response
            if ($totalInsertData > 0 && empty($email_ready) && empty($email_invalid)) {
                echo json_encode([
                    'status' => true,
                    'label' => "success",
                    'message' => "Successfully inserted $totalInsertData records."
                ]);
            } else {

                $msg = "Inserted: <strong>$totalInsertData</strong><br>";

                if (!empty($email_ready)) {
                    $msg .= "Already in use: <br><strong>" . implode("</strong>, <strong>", $email_ready) . "</strong><br>";
                }

                if (!empty($email_invalid)) {
                    $msg .= "Invalid email format: <br><strong>" . implode("</strong>, <strong>", $email_invalid) . "</strong>";
                }

                echo json_encode([
                    'status' => true,
                    'label' => "warning",
                    'message' => $msg
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'label' => 'error',
                'message' => 'Error reading Excel file: ' . $e->getMessage()
            ]);
        }
    }



    public function create()
    {
        
        $formatPhoneNumber = "+63" . $this->input->post('phone_number');

        $customerEmail =$this->input->post('customer_email');

        $companyID = $this->session->userdata('CompanyID');

        $selected_company = ($this->session->userdata("Role") != 1) ? $companyID : $this->input->post('company_selected');

        $cek_email_available = $this->M_Global->globalquery("SELECT * FROM Customer WHERE CustomerEmail = '$customerEmail' AND ListCompanyID = '$companyID' ")->result_array();

        if(count($cek_email_available) > 0) {

            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Email already in use. Please use another email.!</div>');

            redirect(base_url('customer-list'));
        }

        $data_create = [
            "CustomerName" => $this->input->post('customer_name'),
            "CustomerEmail" => $this->input->post('customer_email'),
            "AccountNumber" => $this->input->post('account_number'),
            "Latitude" => $this->input->post('latitude'),
            "ListCompanyID" => $selected_company,
            "Longitude" => $this->input->post('longitude'),
            "PhoneNumber" => $formatPhoneNumber,
            "Address" => $this->input->post('address'),
            "created_at" => date('Y-m-d H:i:s')
        ];

        // create user
        $customer = $this->M_Global->insert($data_create, "Customer");

        if ($customer == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Customer created successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Customer!</div>');
        }

        redirect(base_url('customer-list'));
    }
    public function update()
    {
        $customer_id = $this->input->post('customer_id');
        $customer_email = $this->input->post('customer_email');
        
        $formatPhoneNumber = "+63" . $this->input->post('phone_number');

        $companyID = $this->session->userdata('CompanyID');

        $selected_company = ($this->session->userdata("Role") != 1) ? $companyID : $this->input->post('company_selected');

        $cek_email_before = $this->M_Global->globalquery("SELECT * FROM Customer WHERE CustomerID = '$customer_id' ")->row_array();

        if($customer_email != $cek_email_before['CustomerEmail']) {

            $cek_available_email = $this->M_Global->globalquery("SELECT * FROM Customer WHERE CustomerEmail = '$customer_email' ")->result_array();

            if(count($cek_available_email) > 0) {

                $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Email already in use. Please use another email.!</div>');

                redirect(base_url('customer-list'));

            }
        }
        
        $dara_update = [
            "CustomerName" => $this->input->post('customer_name'),
            "CustomerEmail" => $this->input->post('customer_email'),
            "AccountNumber" => $this->input->post('account_number'),
            "PhoneNumber" => $formatPhoneNumber,
            "Latitude" => $this->input->post('latitude'),
            "ListCompanyID" => $selected_company,
            "Longitude" => $this->input->post('longitude'),
            "Address" => $this->input->post('address'),
            "created_at" => date('Y-m-d H:i:s')
        ];

        $where = " CustomerID =  '$customer_id' ";
        // create user
        $job = $this->M_Global->update_data($where, $dara_update, "Customer");

        if ($job == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Customer updated successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to updated Customer!</div>');
        }

        redirect(base_url('customer-list'));
    }

    public function delete()
    {
        $customer_id = $this->input->post('customer_id');

        $delete = $this->M_Global->delete('Customer' , "CustomerID = '$customer_id' ");

        if ($delete == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Customer delete successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to delete Customer!</div>');
        }

        redirect(base_url('customer-list'));
    }

    public function getCustomerDetail()
    {
        $customerID = $this->input->post('customerID');

        $dataReturn = $this->M_Global->globalquery("SELECT * FROM Customer WHERE CustomerID  = '$customerID' ")->row_array();

        echo json_encode($dataReturn);
    }

}