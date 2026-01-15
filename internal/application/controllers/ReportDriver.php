<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportDriver extends MY_Controller
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
        $data['title'] = "Efms | Report Rider";

        // Mengirim data ke view
        $this->render_page('main/report/reportDriver', $data);
    }


    public function UserLoginActivityReport() {
        // User Login Activity Report
        // Laporan aktivitas login user (terakhir login)
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        $fromDate = $request['from_UserLoginActivityReport'] ?? '';
        $untilDate = $request['until_UserLoginActivityReport'] ?? '';

        $columns = [
            0 => 'UserID',
            1 => 'Fullname',
            2 => 'Email',
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 1; // default kolom 1
        $orderDir = $request['order'][0]['dir'] ?? 'ASC';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

        $sql = "
            SELECT * FROM ListUser
        ";

        $where = []; // buat nampung kondisi WHERE

        if (empty($fromDate)) {
            $where_job = " AND DATE(JobDate) = '" . $this->db->escape_str($dnow) . "'";
            $where_job_cancel = " AND DATE(created_at) = '" . $this->db->escape_str($dnow) . "'";
        }

        if (!empty($fromDate) && !empty($untilDate)) {
            $where_job = " AND DATE(JobDate) >= '" . $this->db->escape_str($fromDate) . "' AND DATE(JobDate) <= '" . $this->db->escape_str($untilDate) . "' ";

            $where_job_cancel = " AND DATE(created_at) >= '" . $this->db->escape_str($fromDate) . "' AND DATE(created_at) <= '" . $this->db->escape_str($untilDate) . "' ";
        }

        if($role != 1) {
            $where[] = "ListUser.ListCompanyID = " . $companyID;
        }
        
        // if (!empty($searchValue)) {
        //     $searchValueEscaped = $this->db->escape_like_str($searchValue);
        //     $sql .= " AND (
        //         sub.Fullname LIKE '%$searchValueEscaped%' OR
        //         sub.Email LIKE '%$searchValueEscaped%'
        //     )";
        // }

        // if (!empty($fromDate)) {
        //     $sql .= " AND sub.LastLogin >= '$fromDate 00:00:00'";  // Memastikan waktu mulai
        // }
        // if (!empty($untilDate)) {
        //     $sql .= " AND sub.LastLogin <= '$untilDate 23:59:59'";  // Memastikan waktu akhir
        // }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $driverID = $row['UserID'];

            $totalJobComplete = $this->M_Global->globalquery("SELECT COUNT(JobID) as total_job FROM ListJob WHERE UserID = '$driverID' $where_job ")->row_array();

            $complete_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as complete_job FROM ListJob WHERE UserID = '$driverID' AND Status = 2 $where_job ")->row_array();

            $ongoing_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as ongoing_job FROM ListJob WHERE UserID = '$driverID' AND Status = 1 $where_job ")->row_array();

            $cancel_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as cancel_job, JobID FROM HistoryCancelJob WHERE UserBefore = '$driverID' $where_job_cancel GROUP BY JobID ")->result_array();

            $data[] = [
                "no" => $no++,
                "UserID" => $row['UserID'],
                "Fullname" => $row['Fullname'],
                "Email" => $row['Email'],
                "TotalJob" => $totalJobComplete['total_job'],
                "CompleteJob" => $complete_job['complete_job'],
                "OngoingJob" => $ongoing_job['ongoing_job'],
                "FromDate" => $fromDate,
                "UntilDate" => $untilDate,
                "CancelJob" =>$cancel_job
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function detail_job($user_id, $type_job, $from_date, $until_date)
    {
        
        $data['job'] = $this->M_Global->globalquery("
        SELECT * FROM ListJob
        LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID
        LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID
        WHERE ListJob.UserID = '$user_id' AND Status = '$type_job' AND DATE(ListJob.JobDate) >= '$from_date' AND DATE(ListJob.JobDate) <= '$until_date' ORDER By ListJob.JobDate DESC
        ")->result_array();

        $data['type_job'] = $type_job;

        $this->load->view('main/report/detail_driver', $data);
    }

    public function detail_job_cancel()
    {

        $job_id = '(' . $_GET['job_id'] . ')';

        $data['job'] = $this->M_Global->globalquery("
        SELECT * FROM HistoryCancelJob
        LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID
        LEFT JOIN ListJob ON HistoryCancelJob.JobID = ListJob.JobID
        WHERE HistoryCancelJob.JobID IN $job_id ORDER By HistoryCancelJob.created_at DESC
        ")->result_array();

        $this->load->view('main/report/detail_driver_cancel', $data);
    }



}