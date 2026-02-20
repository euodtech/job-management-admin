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
        $data['title'] = "Rider Report";

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
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
        $orderCol = isset($columns[$orderColIndex]) ? $columns[$orderColIndex] : 'Fullname';
        $orderBy = $orderCol . " " . $orderDir;

        $sql = "
            SELECT * FROM ListUser
        ";

        $where = []; // buat nampung kondisi WHERE
        $binds = [];
        $where_job = "";
        $where_job_binds = [];
        $where_job_cancel = "";
        $where_job_cancel_binds = [];

        if (empty($fromDate)) {
            $dnow = date("Y-m-d");
            $where_job = " AND DATE(JobDate) = ?";
            $where_job_binds = [$dnow];
            $where_job_cancel = " AND DATE(created_at) = ?";
            $where_job_cancel_binds = [$dnow];
        }

        if (!empty($fromDate) && !empty($untilDate)) {
            $where_job = " AND DATE(JobDate) >= ? AND DATE(JobDate) <= ?";
            $where_job_binds = [$fromDate, $untilDate];
            $where_job_cancel = " AND DATE(created_at) >= ? AND DATE(created_at) <= ?";
            $where_job_cancel_binds = [$fromDate, $untilDate];
        }

        if($role != 1) {
            $where[] = "ListUser.ListCompanyID = ?";
            $binds[] = (int)$companyID;
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

        $totalQuery = $this->M_Global->globalquery($sql, $binds)->result_array();
        $recordsFiltered = count($totalQuery);

        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql, $binds)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $driverID = $row['UserID'];

            $totalJobComplete = $this->M_Global->globalquery("SELECT COUNT(JobID) as total_job FROM ListJob WHERE UserID = ? $where_job ", array_merge([$driverID], $where_job_binds))->row_array();

            $complete_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as complete_job FROM ListJob WHERE UserID = ? AND Status = 2 $where_job ", array_merge([$driverID], $where_job_binds))->row_array();

            $ongoing_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as ongoing_job FROM ListJob WHERE UserID = ? AND Status = 1 $where_job ", array_merge([$driverID], $where_job_binds))->row_array();

            $cancel_job = $this->M_Global->globalquery("SELECT COUNT(JobID) as cancel_job, JobID FROM HistoryCancelJob WHERE UserBefore = ? $where_job_cancel GROUP BY JobID ", array_merge([$driverID], $where_job_cancel_binds))->result_array();

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
        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');

        $binds = [$user_id, $type_job, $from_date, $until_date];
        $companyFilter = '';
        if ($role != 1) {
            $companyFilter = ' AND ListJob.CompanyID = ?';
            $binds[] = $companyID;
        }

        $data['job'] = $this->M_Global->globalquery("
        SELECT * FROM ListJob
        LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID
        LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID
        WHERE ListJob.UserID = ? AND Status = ? AND DATE(ListJob.JobDate) >= ? AND DATE(ListJob.JobDate) <= ?" . $companyFilter . " ORDER By ListJob.JobDate DESC
        ", $binds)->result_array();

        $data['type_job'] = $type_job;

        $this->load->view('main/report/detail_driver', $data);
    }

    public function detail_job_cancel()
    {
        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');

        $job_ids_raw = $this->input->get('job_id');
        $job_ids = array_filter(array_map('intval', explode(',', $job_ids_raw)));

        if (empty($job_ids)) {
            $data['job'] = [];
            $this->load->view('main/report/detail_driver_cancel', $data);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($job_ids), '?'));
        $binds = $job_ids;
        $companyFilter = '';
        if ($role != 1) {
            $companyFilter = ' AND ListJob.CompanyID = ?';
            $binds[] = $companyID;
        }

        $data['job'] = $this->M_Global->globalquery("
        SELECT * FROM HistoryCancelJob
        LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID
        LEFT JOIN ListJob ON HistoryCancelJob.JobID = ListJob.JobID
        WHERE HistoryCancelJob.JobID IN ($placeholders)" . $companyFilter . " ORDER By HistoryCancelJob.created_at DESC
        ", $binds)->result_array();

        $this->load->view('main/report/detail_driver_cancel', $data);
    }



}