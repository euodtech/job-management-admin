<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller
{
    private $defaultDay = 6;
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


    public function index()
    {
        $data['title'] = "Efms | Dashboard";
        $from_date = $this->input->post('from_date');
        $until_date = $this->input->post('until_date');

        if(isset($from_date)) {
            $where_driver_on_job = " AND DATE(AssignWhen) >= '$from_date' AND DATE(AssignWhen) <= '$until_date' ";

            $where_job = " WHERE DATE(ListJob.JobDate) >= '$from_date' AND DATE(ListJob.JobDate) <= '$until_date' ";

            if($this->session->userdata("Role") != 1) {

                $where_job .= " AND ListJob.CompanyID = " . $this->session->userdata('CompanyID');

            }

        
        } else {
            $where_driver_on_job = " AND DATE(AssignWhen) = CURRENT_DATE() ";

            $where_job = " WHERE DATE(ListJob.JobDate) = CURRENT_DATE() ";

            if($this->session->userdata("Role") != 1) {

                $where_job .= " AND ListJob.CompanyID = " . $this->session->userdata('CompanyID');

            }
        }

        if($this->session->userdata('Role') != 1) {

            $where_company = " AND ListCompanyID = " . $this->session->userdata('CompanyID');

        }

        

        $total_drivers = $this->M_Global->globalquery("SELECT * FROM ListUser WHERE StatusActive =  0 $where_company ")->result_array();

        $total_customer = $this->M_Global->globalquery("SELECT *  FROM Customer")->result_array();

        $total_jobs = $this->M_Global->globalquery("SELECT ListJob.*, ListUser.*, Customer.CustomerName, Customer.PhoneNumber as PhoneCustomer FROM ListJob LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID $where_job")->result_array();

        $driver_on_duty = 0;
        $driver_on_duty_detail = [];
        $driver_off_duty = 0;
        $driver_off_duty_detail = [];
        
        $finished_job = 0;
        $finished_job_detail = [];

        $detail_jobs = [];
        $total_job_on_duty = 0;
        $detail_job_on_duty = [];
        $total_job_off_duty = 0;
        $detail_job_off_duty = [];

        foreach($total_drivers as $val) {

            $driver_id = $val['UserID'];
            $driver_name = $val['Fullname'];
            $driver_email = $val['Email'];
            $driver_phone = $val['PhoneNumber'];

            $cek_driver = $this->M_Global->globalquery("SELECT UserID FROM ListJob WHERE UserID = '$driver_id' $where_driver_on_job ")->result_array();

            if(count($cek_driver) > 0) {
                // Driver sedang bertugas
                $driver_on_duty++;
                $driver_on_duty_detail[] = [
                    "Fullname"    => $driver_name,
                    "Email"       => $driver_email,
                    "PhoneNumber" => $driver_phone
                ];
            } else {
                // Driver belum bertugas
                $driver_off_duty++;
                $driver_off_duty_detail[] = [
                    "Fullname"    => $driver_name,
                    "Email"       => $driver_email,
                    "PhoneNumber" => $driver_phone
                ];
            }
        }

        foreach($total_jobs as $val) {


            if($val['UserID'] != null) {

                if($val['Status'] == "2") {
                    $finished_job++;
                    $finished_job_detail[] = [
                        "JobName"    => $val['JobName'],
                        "NameDriver"       => $val['Fullname'],
                        "PhoneDriver"       => $val['PhoneNumber'],
                        "CustomerName" => $val['CustomerName'],
                        "CustomerPhone" => $val['PhoneCustomer'],
                        "JobDate" => $val['JobDate']
                    ];
                } else {
                    $total_job_on_duty++;

                    $detail_job_on_duty[] = [
                        "JobName"    => $val['JobName'],
                        "NameDriver"       => $val['Fullname'],
                        "PhoneDriver"       => $val['PhoneNumber'],
                        "CustomerName" => $val['CustomerName'],
                        "CustomerPhone" => $val['PhoneCustomer'],
                        "JobDate" => $val['JobDate']
                    ];
                }

                

            } else {
                $total_job_off_duty++;

                $detail_job_off_duty[] = [
                    "JobName"    => $val['JobName'],
                    "NameDriver"       => $val['Fullname'],
                    "PhoneDriver"       => $val['PhoneNumber'],
                    "CustomerName" => $val['CustomerName'],
                    "CustomerPhone" => $val['PhoneCustomer'],
                    "JobDate" => $val['JobDate']
                ];
            }

            $detail_jobs[] = [
                "JobName"    => $val['JobName'],
                "NameDriver"       => $val['Fullname'],
                "PhoneDriver"       => $val['PhoneNumber'],
                "CustomerName" => $val['CustomerName'],
                "CustomerPhone" => $val['PhoneCustomer'],
                "JobDate" => $val['JobDate']
            ];
        }

        $return = [
            "total_drivers"         => count($total_drivers),
            "drivers_on_duty"       => $driver_on_duty,
            "drivers_on_duty_detail"=> $driver_on_duty_detail,
            "drivers_off_duty"      => $driver_off_duty,
            "drivers_off_duty_detail"=> $driver_off_duty_detail,
            "total_job" => count($total_jobs),
            "detail_job_today" => $detail_jobs,
            "total_job_on_duty" => $total_job_on_duty,
            "total_finished_job" => $finished_job,
            "finished_job_detail" => $finished_job_detail,
            "detail_job_on_duty" => $detail_job_on_duty,
            "total_job_off_duty" => $total_job_off_duty,
            "detail_job_off_duty" => $detail_job_off_duty
        ];

        $data['return'] = $return;

        // echo json_encode($return);
        // die;


        $this->render_page('main/home/page_home',$data);
    }

    private function getSalesSummary($fromDate=null,$untilDate=null,$month=null,$type = null)
    {
        if(!$type)
            $type = $this->input->get('time')?:"range_date";
        if($type == "range_date"){
            if(!$fromDate)
                $fromDate = $this->input->get('from_date');
            if(!$untilDate)
                $untilDate = $this->input->get('until_date') ?: date('Y-m-d');
            if ($fromDate && $untilDate) {
                $where = " WHERE Transaction.TransactionDatetime >= '".$fromDate." 00:00:00' AND Transaction.TransactionDatetime <= '".$untilDate." 23:59:59'";
            } else {
                $diff = $this->defaultDay;
                $fromDateRes = new DateTime(date('Y-m-d'));
                $fromDateRes->modify("-$diff days");
                $fromDateRes = $fromDateRes->format("Y-m-d");

                $where = " WHERE Transaction.TransactionDatetime >= '".$fromDateRes." 00:00:00' AND Transaction.TransactionDatetime <= '".date('Y-m-d')." 23:59:59'";
            }
        }else{
            if (!$month)
                $month = $this->input->get("month");
            $diffMonth = 0;
            if($month == 1){
                $month = "01";
            }else if($month > 9){
                $diffMonth = $month + 1;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
            }else{
                $month = "0".$month;
                $diffMonth = "0".($month + 1);
            }
            $fromDataRes = date("Y-$month-01"); 
            $toDataRes = date("Y-$diffMonth-01");
            if($diffMonth > 12){
                $yearNext = date("Y") + 1;
                $diffMonth = $diffMonth - 12;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
                $toDataRes = date("$yearNext-$diffMonth-01");
            }else if($diffMonth == 0){
                $yearPass = date("Y") - 1;
                $toDataRes = date("$yearPass-12-01");
            }
            $where = " WHERE Transaction.TransactionDatetime >= '" . $fromDataRes . " 00:00:00' AND Transaction.TransactionDatetime < '" . $toDataRes . " 00:00:00'";
        }
        $where .= "AND StatusTransID != 0 ";

        $res =  $this->M_Global->globalquery("SELECT DATE(TransactionDatetime) as date, sum(TotalTransaction) total_transaction FROM Transaction $where GROUP BY DATE(TransactionDatetime)")->result();
    
        $res2 = [];
        foreach ($res as $key => $value) {
            $res2[$value->date] = $value->total_transaction;
        }
        $resDate = [];
        $resValue = [];
        foreach ($res2 as $key => $value) {
            $dateFormat = new DateTime($key );
            $dateFormat = $dateFormat->format("d M Y");

            $resDate[] = $dateFormat;
            $resValue[] = (int) $value;
        }
        return [
            "date" => json_encode($resDate),
            "value" => json_encode($resValue),
        ];
    }

    private function getTopProduct($fromDate=null,$untilDate=null,$month=null,$type = null)
    {
        if(!$type)
            $type = $this->input->get('time')?:"range_date";
        if($type == "range_date"){
            if(!$fromDate)
                $fromDate = $this->input->get('from_date');
            if(!$untilDate)
                $untilDate = $this->input->get('until_date') ?: date('Y-m-d');
            if ($fromDate && $untilDate) {
                $where = " WHERE date(td.created_at) >= '".$fromDate." 00:00:00' AND date(td.created_at) <= '".$untilDate." 23:59:59'";
            } else {
                $diff = $this->defaultDay;
                $fromDateRes = new DateTime(date('Y-m-d'));
                $fromDateRes->modify("-$diff days");
                $fromDateRes = $fromDateRes->format("Y-m-d");

                $where = " WHERE date(td.created_at) >= '".$fromDateRes." 00:00:00' AND date(td.created_at) <= '".date('Y-m-d')." 23:59:59'";
            }
        }else{
            if (!$month)
                $month = $this->input->get("month");
            $diffMonth = 0;
            if($month == 1){
                $month = "01";
            }else if($month > 9){
                $diffMonth = $month + 1;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
            }else{
                $month = "0".$month;
                $diffMonth = "0".($month + 1);
            }
            $fromDataRes = date("Y-$month-01"); 
            $toDataRes = date("Y-$diffMonth-01");
            if($diffMonth > 12){
                $yearNext = date("Y") + 1;
                $diffMonth = $diffMonth - 12;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
                $toDataRes = date("$yearNext-$diffMonth-01");
            }else if($diffMonth == 0){
                $yearPass = date("Y") - 1;
                $toDataRes = date("$yearPass-12-01");
            }
            $where = " WHERE date(td.created_at) >= '" . $fromDataRes . " 00:00:00' AND date(td.created_at) < '" . $toDataRes . " 00:00:00'";
        }
        $where .= "AND tr.StatusTransaction != 0 ";

        $res =  $this->M_Global->globalquery("SELECT td.ProductName, SUM(td.qty) as total_qty FROM TransactionDetail td JOIN Transaction tr ON tr.TransactionID = td.TransactionID GROUP BY td.ProductID ORDER BY SUM(td.qty) DESC LIMIT 5")->result();
        $resDate = [];
        $resValue = [];
        foreach ($res as $value) {
            $resDate[] = $value->ProductName;
            $resValue[] = (int) $value->total_qty;
        }
        return [
            "product_name" => json_encode($resDate),
            "total_qty" => json_encode($resValue),
        ];
    }

    public function get_total_job_ajax()
    {

        $dataReturn = [
            "CountAllType" => get_total_job(),
            "CountLine" => get_total_job(1),
            "CountShort" => get_total_job(3),
            "CountDc" => get_total_job(4),
            "CountReconnect" => get_total_job(2) ,
            "CountReschedule" => get_total_reschedule()
        ];

        echo json_encode($dataReturn);
    }

    private function getTopMember($fromDate=null,$untilDate=null,$month=null,$type = null)
    {
        if(!$type)
            $type = $this->input->get('time')?:"range_date";
        if($type == "range_date"){
            if(!$fromDate)
                $fromDate = $this->input->get('from_date');
            if(!$untilDate)
                $untilDate = $this->input->get('until_date') ?: date('Y-m-d');
            if ($fromDate && $untilDate) {
                $where = " WHERE date(td.created_at) >= '".$fromDate." 00:00:00' AND date(td.created_at) <= '".$untilDate." 23:59:59'";
            } else {
                $diff = $this->defaultDay;
                $fromDateRes = new DateTime(date('Y-m-d'));
                $fromDateRes->modify("-$diff days");
                $fromDateRes = $fromDateRes->format("Y-m-d");

                $where = " WHERE date(td.created_at) >= '".$fromDateRes." 00:00:00' AND date(td.created_at) <= '".date('Y-m-d')." 23:59:59'";
            }
        }else{
            if (!$month)
                $month = $this->input->get("month");
            $diffMonth = 0;
            if($month == 1){
                $month = "01";
            }else if($month > 9){
                $diffMonth = $month + 1;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
            }else{
                $month = "0".$month;
                $diffMonth = "0".($month + 1);
            }
            $fromDataRes = date("Y-$month-01"); 
            $toDataRes = date("Y-$diffMonth-01");
            if($diffMonth > 12){
                $yearNext = date("Y") + 1;
                $diffMonth = $diffMonth - 12;
                if($diffMonth < 10)
                    $diffMonth = "0".$diffMonth;
                $toDataRes = date("$yearNext-$diffMonth-01");
            }else if($diffMonth == 0){
                $yearPass = date("Y") - 1;
                $toDataRes = date("$yearPass-12-01");
            }
            $where = " WHERE date(td.created_at) >= '" . $fromDataRes . " 00:00:00' AND date(td.created_at) < '" . $toDataRes . " 00:00:00'";
        }
        $where .= "AND tr.StatusTransaction != 0 ";

        if($outletID = $this->input->get("outlet"))
            $where .= "AND tr.PoolID = $outletID";

        $res =  $this->M_Global->globalquery("SELECT tr.CustomerName, SUM(td.qty) as total_qty 
        FROM TransactionDetail td 
        JOIN Transaction tr ON tr.TransactionID = td.TransactionID 
        GROUP BY tr.CustomerID ORDER BY SUM(td.qty) DESC LIMIT 5")->result();
        $resDate = [];
        $resValue = [];
        foreach ($res as $value) {
            $resDate[] = $value->CustomerName;
            $resValue[] = (int) $value->total_qty;
        }
        return [
            "member" => json_encode($resDate),
            "total_qty" => json_encode($resValue),
        ];
    }

    public function forgot_password()
    {
        if(empty($_GET['token'])){
            $this->load->view('main/expired_forgot_password');
        } else {
            $token = $_GET['token'];

            $data = $this->M_Global->globalquery("SELECT * FROM UserLogin WHERE key_resetpassword = '$token' ")->row_array();

            if($data == null AND $token != "success_update_password") {

                $this->load->view('main/expired_forgot_password');
            } else {
                if($token == "success_update_password") {

                    $this->load->view('main/success_forgot_password');
                }  else {
                    $data['user_id'] = $data['UserLoginID'];
                    $this->load->view('main/forgot_password', $data);
                }
            }
        }
    
    }
}