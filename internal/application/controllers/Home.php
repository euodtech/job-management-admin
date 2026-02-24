<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends MY_Controller
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


    public function index()
    {
        $data['title'] = "Dashboard";

        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        // --- Driver online/offline (based on LastActivity within 10 minutes) ---
        $where_company = "";
        $binds_drivers = [];
        if ($role != 1) {
            $where_company = " AND ListUser.ListCompanyID = ?";
            $binds_drivers[] = $companyID;
        }

        $drivers = $this->M_Global->globalquery(
            "SELECT ListUser.Fullname, ListUser.Email, ListUser.PhoneNumber, UserLogin.LastActivity
             FROM ListUser
             LEFT JOIN UserLogin ON ListUser.UserLoginID = UserLogin.UserLoginID
             WHERE ListUser.StatusActive = 0 $where_company",
            $binds_drivers
        )->result_array();

        $drivers_online = 0;
        $drivers_online_detail = [];
        $drivers_offline = 0;
        $drivers_offline_detail = [];

        $threshold = gmdate('Y-m-d H:i:s', strtotime('-1 hour'));

        foreach ($drivers as $val) {
            $driver_info = [
                "Fullname"     => $val['Fullname'],
                "Email"        => $val['Email'],
                "PhoneNumber"  => $val['PhoneNumber'],
                "LastActivity" => $val['LastActivity']
            ];

            if ($val['LastActivity'] && $val['LastActivity'] >= $threshold) {
                $drivers_online++;
                $drivers_online_detail[] = $driver_info;
            } else {
                $drivers_offline++;
                $drivers_offline_detail[] = $driver_info;
            }
        }

        // --- Jobs today ---
        $where_job = " WHERE DATE(ListJob.JobDate) = CURRENT_DATE()";
        $binds_jobs = [];
        if ($role != 1) {
            $where_job .= " AND ListJob.CompanyID = ?";
            $binds_jobs[] = $companyID;
        }

        $total_jobs = $this->M_Global->globalquery(
            "SELECT ListJob.*, ListUser.Fullname, ListUser.PhoneNumber,
                    Customer.CustomerName, Customer.PhoneNumber as PhoneCustomer
             FROM ListJob
             LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID
             LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID
             $where_job",
            $binds_jobs
        )->result_array();

        $finished_job = 0;
        $finished_job_detail = [];
        $total_job_on_duty = 0;
        $detail_job_on_duty = [];
        $total_job_off_duty = 0;
        $detail_job_off_duty = [];

        foreach ($total_jobs as $val) {
            $job_info = [
                "JobName"       => $val['JobName'],
                "NameDriver"    => $val['Fullname'],
                "PhoneDriver"   => $val['PhoneNumber'],
                "CustomerName"  => $val['CustomerName'],
                "CustomerPhone" => $val['PhoneCustomer'],
                "JobDate"       => $val['JobDate']
            ];

            if ($val['UserID'] != null) {
                if ($val['Status'] == "2") {
                    $finished_job++;
                    $finished_job_detail[] = $job_info;
                } else {
                    $total_job_on_duty++;
                    $detail_job_on_duty[] = $job_info;
                }
            } else {
                $total_job_off_duty++;
                $detail_job_off_duty[] = $job_info;
            }
        }

        $return = [
            "total_drivers"          => count($drivers),
            "drivers_online"         => $drivers_online,
            "drivers_online_detail"  => $drivers_online_detail,
            "drivers_offline"        => $drivers_offline,
            "drivers_offline_detail" => $drivers_offline_detail,
            "total_job"              => count($total_jobs),
            "total_job_on_duty"      => $total_job_on_duty,
            "total_finished_job"     => $finished_job,
            "finished_job_detail"    => $finished_job_detail,
            "detail_job_on_duty"     => $detail_job_on_duty,
            "total_job_off_duty"     => $total_job_off_duty,
            "detail_job_off_duty"    => $detail_job_off_duty
        ];

        $data['return'] = $return;

        $this->render_page('main/home/page_home', $data);
    }

    public function get_driver_status_ajax()
    {
        header('Content-Type: application/json');

        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $where_company = "";
        $binds = [];
        if ($role != 1) {
            $where_company = " AND ListUser.ListCompanyID = ?";
            $binds[] = $companyID;
        }

        $drivers = $this->M_Global->globalquery(
            "SELECT ListUser.Fullname, ListUser.Email, ListUser.PhoneNumber, UserLogin.LastActivity
             FROM ListUser
             LEFT JOIN UserLogin ON ListUser.UserLoginID = UserLogin.UserLoginID
             WHERE ListUser.StatusActive = 0 $where_company",
            $binds
        )->result_array();

        $drivers_online = 0;
        $drivers_online_detail = [];
        $drivers_offline = 0;
        $drivers_offline_detail = [];

        $threshold = gmdate('Y-m-d H:i:s', strtotime('-1 hour'));

        foreach ($drivers as $val) {
            $driver_info = [
                "Fullname"     => $val['Fullname'],
                "PhoneNumber"  => $val['PhoneNumber'],
                "LastActivity" => $val['LastActivity']
            ];

            if ($val['LastActivity'] && $val['LastActivity'] >= $threshold) {
                $drivers_online++;
                $drivers_online_detail[] = $driver_info;
            } else {
                $drivers_offline++;
                $drivers_offline_detail[] = $driver_info;
            }
        }

        echo json_encode([
            "total_drivers"          => count($drivers),
            "drivers_online"         => $drivers_online,
            "drivers_online_detail"  => $drivers_online_detail,
            "drivers_offline"        => $drivers_offline,
            "drivers_offline_detail" => $drivers_offline_detail
        ]);
    }

    public function get_total_job_ajax()
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $sql = "SELECT
                    COUNT(*) AS CountAllType,
                    SUM(CASE WHEN TypeJob = 1 THEN 1 ELSE 0 END) AS CountLine,
                    SUM(CASE WHEN TypeJob = 2 THEN 1 ELSE 0 END) AS CountReconnect,
                    SUM(CASE WHEN TypeJob = 3 THEN 1 ELSE 0 END) AS CountShort,
                    SUM(CASE WHEN TypeJob = 4 THEN 1 ELSE 0 END) AS CountDc
                FROM ListJob
                WHERE (Status IS NULL OR Status = 1 OR Status = 3)";
        $binds = array();

        if ($role != '1') {
            $sql .= " AND CompanyID = ?";
            $binds[] = $companyID;
        }

        $counts = $this->M_Global->globalquery($sql, $binds)->row();

        $dataReturn = [
            "CountAllType"   => (int) $counts->CountAllType,
            "CountLine"      => (int) $counts->CountLine,
            "CountShort"     => (int) $counts->CountShort,
            "CountDc"        => (int) $counts->CountDc,
            "CountReconnect" => (int) $counts->CountReconnect,
            "CountReschedule" => get_total_reschedule()
        ];

        echo json_encode($dataReturn);
    }

}