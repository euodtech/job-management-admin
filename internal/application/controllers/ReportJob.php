<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportJob extends MY_Controller
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

    public function detail_job($jobID)
    {

        $data['jobID'] = $jobID;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');

        $detailJobBinds = [(int)$jobID];
        $companyFilter = "";
        if ($role != 1) {
            $companyFilter = " AND ListJob.CompanyID = ?";
            $detailJobBinds[] = $companyID;
        }

        $dataJobHead = $this->M_Global->globalquery("SELECT ListJob.*, ListUser.Fullname, ListUser.Email, ListUser.PhoneNumber, Customer.CustomerName, Customer.Address, Customer.PhoneNumber FROM ListJob
        LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID
        LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID
        WHERE ListJob.JobID = ? " . $companyFilter, $detailJobBinds)->result_array();

        $dataJobHead = array_map(function($job) {

            $jobID = $job['JobID'];

            $statusCancelJob = $this->M_Global->globalquery("SELECT UserBefore, HistoryCancelJob.created_at, Fullname, Reason FROM HistoryCancelJob LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID WHERE JobID = ? ORDER BY HistoryCancelJob.created_at ASC ", [(int)$jobID])->result_array();

            $assets_job = $this->M_Global->globalquery("SELECT * FROM ListJobDetail WHERE ListJobID = ? ", [(int)$jobID])->result_array();

            if($job['TypeJob'] == 1) {
                $job['TypeJob'] = "Line Interrupt";
            } elseif($job['TypeJob'] == 2) {
                $job['TypeJob'] = "Reconnection";
            } elseif($job['TypeJob'] == 3) {
                $job['TypeJob'] = "Short Circuit";
            }

            // $job['query'] = "SELECT UserBefore, HistoryCancelJob.created_at, Fullname, Reason FROM HistoryCancelJob LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC";

            $job['StatusCancelJob'] = $statusCancelJob;
            $job['AssetsJob'] = $assets_job;
            
            return $job;
        }, $dataJobHead);

        $data['detail'] = $dataJobHead;


        $this->load->view('main/report/detail_job', $data);
    }

    public function index()
    {

        $dnow = date("Y-m-d");

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');

        $dfrom = date("Y-m-d", strtotime("-6 days"));
        $indexBinds = [$dfrom, $dnow];
        $companyFilter = "";
        if ($role != 1) {
            $companyFilter = " AND CompanyID = ?";
            $indexBinds[] = $companyID;
        }

        $data['title'] = "Job Report";
        $data['jobs'] = $this->M_Global->globalquery("SELECT * FROM ListJob WHERE DATE(JobDate) >= ? AND DATE(JobDate) <= ? " . $companyFilter, $indexBinds)->result_array();

        // Mengirim data ke view
        $this->render_page('main/report/reportJob', $data);
    }


    public function JobPerCustomerReport() {

        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');
        // Laporan Job per Pelanggan (Job per Customer Report)
        // Tujuan: Untuk melihat riwayat dan status semua pekerjaan yang terhubung dengan pelanggan tertentu.
        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        // Filter berdasarkan customer jika ada
        $filterFromDate = $request['filterFromDate'] ?? '';
        $filterUntilDate = $request['filterUntilDate'] ?? '';
        $filterStatusJob = $request['filterStatusJob'] ?? '';
        // $filterJob = $request['filterJob'] ?? '';


        $columns = [
            0 => 'JobID',
            1 => 'JobDate',
            2 => 'JobName',
            3 => 'CustomerName',
            4 => 'Fullname'
        ];

        // Ambil parameter order dari DataTables
        $orderColIndex = $request['order'][0]['column'] ?? 3; // default kolom 1
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
        $orderCol = isset($columns[$orderColIndex]) ? $columns[$orderColIndex] : 'CustomerName';
        $orderBy = $orderCol . " " . $orderDir;

        $dnow = date('Y-m-d');

       $sql = "
            SELECT * FROM ListJob lj 
            LEFT JOIN ListUser lu ON lj.UserID = lu.UserID 
            LEFT JOIN Customer c ON lj.CustomerID = c.CustomerID
        ";

        $where = []; // buat nampung kondisi WHERE
        $binds = [];

        if (empty($filterFromDate)) {
            $dfrom = date("Y-m-d", strtotime("-6 days"));
            $where[] = "DATE(lj.JobDate) >= ?";
            $binds[] = $dfrom;
            $where[] = "DATE(lj.JobDate) <= ?";
            $binds[] = $dnow;
        }

        if (!empty($filterFromDate) && !empty($filterUntilDate)) {
            $where[] = "DATE(lj.JobDate) >= ?";
            $binds[] = $filterFromDate;
            $where[] = "DATE(lj.JobDate) <= ?";
            $binds[] = $filterUntilDate;
        }

        if($role != 1) {
            $where[] = "lj.CompanyID = ?";
            $binds[] = (int)$companyID;
        }

        if($filterStatusJob != "all_status") {

            if($filterStatusJob == "awaiting_job") {

                $where[] = "lj.Status IS NULL"; 

            } elseif($filterStatusJob == "ongoing_job") {

                $where[] = "lj.Status = 1"; 

            } elseif($filterStatusJob == "finished") {

                $where[] = "lj.Status = 2"; 
            }
            
        }

        // gabungkan kondisi WHERE (kalau ada)
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }



        // // --- filter job ---
        // if (!empty($filterJob)) {
        //     $sql .= " AND j.JobName = " . $this->db->escape($filterJob);
        // }



        // --- search filter ---
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $sql .= " AND (
                lj.JobName LIKE '%" . $searchValueEscaped . "%' OR c.CustomerName LIKE '%" . $searchValueEscaped . "%' OR lu.Fullname LIKE '%" . $searchValueEscaped . "%'
            )";
        }

        // total sebelum limit
        $totalQuery = $this->M_Global->globalquery($sql, $binds)->result_array();
        $recordsTotal = count($totalQuery);

        // Ambil data dengan LIMIT (untuk paging)
        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql, $binds)->result_array();


        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $data[] = [
                "no" => $no++,
                "CustomerName" => $row['CustomerName'],
                "DriverName" => $row['Fullname'],
                "JobName" => $row['JobName'],
                "JobDate" => $row['JobDate'],
                "StatusJob" => $row['Status'],
                "JobID" => $row['JobID']
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal, 
            "data" => $data
        ]);
    }

    public function JobComplianceReport() {
        // Laporan Kepatuhan & Dokumentasi Pekerjaan (Job Compliance Report)
        // Memastikan setiap pekerjaan didokumentasikan dengan baik (foto, waktu selesai, dsb.)
        
        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';
        $jobName = $this->input->get('jobName');
        $typeJob = $this->input->get('typeJob');
        $jobDate = $this->input->get('jobDate');
        $totalDokumentasi = $this->input->get('totalDokumentasi');
        $status = $this->input->get('statusJobComplianceReport');

        $columns = [
            0 => 'sub.JobID',
            1 => 'sub.JobName',
            2 => 'sub.TypeJob',
            3 => 'sub.JobDate',
            4 => 'sub.TotalDokumentasi',
            5 => 'sub.StatusDokumentasi',
            6 => 'sub.Dokumentasi'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 3;
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
        $orderCol = isset($columns[$orderColIndex]) ? $columns[$orderColIndex] : 'sub.JobDate';
        $orderBy = $orderCol . " " . $orderDir;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');
        $companyWhere = ($role != 1) ? "WHERE j.CompanyID = " . $companyID : "";

        $baseQuery = "
            SELECT
                j.JobID,
                j.JobName,
                CASE
                    WHEN j.TypeJob = 1 THEN 'Line Interrupt'
                    WHEN j.TypeJob = 2 THEN 'Reconnection'
                    WHEN j.TypeJob = 3 THEN 'Short Circuit'
                    ELSE 'Unknown'
                END AS TypeJob,
                j.JobDate,
                COUNT(d.ListDetailID) AS TotalDokumentasi,
                CONCAT('[',
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            'photo', d.Photo,
                            'caption', DATE_FORMAT(d.created_at, '%Y-%m-%d %H:%i:%s')
                        )
                        ORDER BY d.ListDetailID ASC SEPARATOR ','
                    ),
                ']') AS Dokumentasi,
                CASE
                    WHEN COUNT(d.ListDetailID) > 0 THEN 'Finished'
                    ELSE 'No documentation yet'
                END AS StatusDokumentasi
            FROM ListJob j
            LEFT JOIN ListJobDetail d ON j.JobID = d.ListJobID
            $companyWhere
            GROUP BY j.JobID, j.JobName, j.TypeJob, j.JobDate
        ";

        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        if (!empty($jobName)) {
            $jobNameEscaped = $this->db->escape_like_str($jobName);
            $sql .= " AND sub.JobName LIKE '%$jobNameEscaped%'";
        }

        $searchValueEscaped = $this->db->escape_like_str($searchValue);
        $statusEscaped = $this->db->escape($status);
        $typeJobEscaped = $this->db->escape($typeJob);
        $jobDateEscaped = $this->db->escape($jobDate);
        $totalDokumentasiEscaped = $this->db->escape($totalDokumentasi);

        // search filter
        if (!empty($searchValue)) {
            $sql .= " AND (
                sub.JobName LIKE '%$searchValueEscaped%' OR 
                sub.TypeJob LIKE '%$searchValueEscaped%' OR 
                sub.JobDate LIKE '%$searchValueEscaped%' OR 
                sub.TotalDokumentasi LIKE '%$searchValueEscaped%' OR 
                sub.StatusDokumentasi LIKE '%$searchValueEscaped%'
            )";
        }

        // Filter Status
        if (!empty($status)) {
            $sql .= " AND sub.StatusDokumentasi = $statusEscaped";
        }

        // Filter TypeJob
        if (!empty($typeJob)) {
            $sql .= " AND sub.TypeJob = $typeJobEscaped";
        }

        // Filter JobDate
        if (!empty($jobDate)) {
            $sql .= " AND sub.JobDate = $jobDateEscaped";
        }

        // Filter TotalDokumentasi
        if (!empty($totalDokumentasi)) {
            $sql .= " AND sub.TotalDokumentasi >= $totalDokumentasiEscaped";
        }

        // Hitung recordsFiltered
        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        // Hitung recordsTotal tanpa filter (optional)
        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob" . (($role != 1) ? " WHERE CompanyID = " . $companyID : "");
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // tambah order + limit
        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $data[] = [
                "no" => $no++,
                "JobName" => $row['JobName'],
                "TypeJob" => $row['TypeJob'],
                "JobDate" => date('Y-m-d', strtotime($row['JobDate'])),
                "TotalDokumentasi" => $row['TotalDokumentasi'],
                "StatusDokumentasi" => $row['StatusDokumentasi'],
                "Dokumentasi" => $row['Dokumentasi']
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);

    }


    
    public function JobAssignmentEfficiencyReport() {
        // Job Assignment Efficiency Report
        // Laporan Efisiensi Penugasan Job
        // Mengukur durasi dari job dibuat sampai diassign

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        $customer = $this->input->get('customerName'); 
        $jobName = $this->input->get('jobName');
        $fromDate = $this->input->get('from_date');
        $toDate   = $this->input->get('to_date');

        $fromAssignAt = $this->input->get('fromAssignAt');
        $toAssignAt   = $this->input->get('toAssignAt');

        $columns = [
            0 => null,               // "No" - not sortable
            1 => 'JobName',
            2 => 'CustomerName',
            3 => 'created_at',
            4 => 'AssignWhen',
            5 => 'DurationMinutes'
        ];

        $orderColIndex = intval($request['order'][0]['column'] ?? 3);
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
        $orderCol = (isset($columns[$orderColIndex]) && $columns[$orderColIndex] !== null) ? $columns[$orderColIndex] : 'created_at';
        $orderBy = $orderCol . " " . $orderDir;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');
        $companyWhere = ($role != 1) ? "WHERE j.CompanyID = " . $companyID : "";

        $baseQuery = "
            SELECT
                j.JobID,
                j.JobName,
                c.CustomerName,
                j.created_at,
                j.AssignWhen,
                TIMESTAMPDIFF(MINUTE, j.created_at, j.AssignWhen) AS DurationMinutes
            FROM ListJob j
            JOIN Customer c ON j.CustomerID = c.CustomerID
            $companyWhere
        ";

        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        if (!empty($jobName)) {
            $sql .= " AND sub.JobName = " . $this->db->escape($jobName);
        }

        if (!empty($customer)) {
            $sql .= " AND sub.CustomerName = " . $this->db->escape($customer);
        }

        // filter date range
        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND DATE(sub.created_at) BETWEEN " . $this->db->escape($fromDate) . " AND " . $this->db->escape($toDate);
        } elseif (!empty($fromDate)) {
            $sql .= " AND DATE(sub.created_at) >= " . $this->db->escape($fromDate);
        } elseif (!empty($toDate)) {
            $sql .= " AND DATE(sub.created_at) <= " . $this->db->escape($toDate);
        }

        if (!empty($fromAssignAt) && !empty($toAssignAt)) {
            $sql .= " AND DATE(sub.AssignWhen) BETWEEN " . $this->db->escape($fromAssignAt) . " AND " . $this->db->escape($toAssignAt);
        } elseif (!empty($fromAssignAt)) {
            $sql .= " AND DATE(sub.AssignWhen) >= " . $this->db->escape($fromAssignAt);
        } elseif (!empty($toAssignAt)) {
            $sql .= " AND DATE(sub.AssignWhen) <= " . $this->db->escape($toAssignAt);
        }

        // search filter
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $sql .= " AND (
                sub.JobName LIKE '%$searchValueEscaped%' OR 
                sub.CustomerName LIKE '%$searchValueEscaped%' OR 
                sub.DurationMinutes LIKE '%$searchValueEscaped%'
            )";
        }

        // total filtered
        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        // total semua record
        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob" . (($role != 1) ? " WHERE CompanyID = " . $companyID : "");
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // order + limit
        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $data[] = [
                "no" => $no++,
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "CreatedAt" => date('Y-m-d H:i:s', strtotime($row['created_at'])),
                "AssignWhen" => $row['AssignWhen'] ? date('Y-m-d H:i:s', strtotime($row['AssignWhen'])) : '-',
                "DurationMinutes" => $row['DurationMinutes'] ?? 0
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function getCustomers() {
        $sql = "SELECT CustomerID, CustomerName FROM Customer ORDER BY CustomerName ASC";
        $result = $this->M_Global->globalquery($sql)->result_array();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }


    public function JobCompletionStatusReport() {
        // Job Completion & Status Report
        // Laporan jumlah job per status (Selesai, Pending, Gagal, dll)

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';
        $jobName = $request['jobName'];
        $customerName = $request['customerName'];
        $status = $request['status'];
        $jobDateFrom = $request['jobDateFrom'] ?? ''; 
        $jobDateUntil = $request['jobDateUntil'] ?? '';
        
        $columns = [
            0 => null,              // "No" - not sortable
            1 => 'JobName',
            2 => 'CustomerName',
            3 => 'Status',
            4 => 'JobDate'
        ];

        $orderColIndex = intval($request['order'][0]['column'] ?? 4);
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
        $orderCol = (isset($columns[$orderColIndex]) && $columns[$orderColIndex] !== null) ? $columns[$orderColIndex] : 'JobDate';
        $orderBy = $orderCol . " " . $orderDir;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');
        $companyWhere = ($role != 1) ? "WHERE j.CompanyID = " . $companyID : "";

        $baseQuery = "
            SELECT
                j.JobID,
                j.JobName,
                c.CustomerName,
                j.Status,
                j.JobDate
            FROM ListJob j
            JOIN Customer c ON j.CustomerID = c.CustomerID
            $companyWhere
        ";

        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $sql .= " AND (
                sub.JobName LIKE '%$searchValueEscaped%' OR 
                sub.CustomerName LIKE '%$searchValueEscaped%' OR 
                sub.Status LIKE '%$searchValueEscaped%'
            )";
        }

        if (!empty($jobName)) {
            $sql .= " AND sub.JobName LIKE '%" . $this->db->escape_like_str($jobName) . "%'";
        }

        if (!empty($customerName)) {
            $sql .= " AND sub.CustomerName LIKE '%" . $this->db->escape_like_str($customerName) . "%'";
        }

        if (!empty($status)) {
            if ($status == 5) {
                $sql .= " AND sub.Status IS NULL";
            } else {
                $sql .= " AND sub.Status = " . $this->db->escape($status);
            }
        }

        if (!empty($jobDateFrom)) {
            $sql .= " AND sub.JobDate >= " . $this->db->escape($jobDateFrom);
        }

        if (!empty($jobDateUntil)) {
            $sql .= " AND sub.JobDate <= " . $this->db->escape($jobDateUntil);
        }

        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob" . (($role != 1) ? " WHERE CompanyID = " . $companyID : "");
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $statusText = "Unknown";
            if ($row['Status'] === NULL) {
                $statusText = "-"; 
            } else if ($row['Status'] == 1) {
                $statusText = "Pending";
            } else if ($row['Status'] == 2) {
                $statusText = "In Progress";
            } else if ($row['Status'] == 3) {
                $statusText = "Completed";
            } else if ($row['Status'] == 4) {
                $statusText = "Failed";
            }

            $data[] = [
                "no" => $no++,
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "Status" => $statusText,
                "JobDate" => date('Y-m-d', strtotime($row['JobDate']))
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function JobTimelineReport() {
        // Job Timeline Report
        // Jumlah job berdasarkan tanggal (per hari)

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        $columns = [
            0 => null,              // "No" - not sortable
            1 => 'JobDate',
            2 => 'TotalJob'
        ];

        $orderColIndex = intval($request['order'][0]['column'] ?? 1);
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'DESC') ? 'DESC' : 'ASC';
        $orderCol = (isset($columns[$orderColIndex]) && $columns[$orderColIndex] !== null) ? $columns[$orderColIndex] : 'JobDate';
        $orderBy = $orderCol . " " . $orderDir;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');
        $companyWhere = ($role != 1) ? "WHERE j.CompanyID = " . $companyID : "";

        $baseQuery = "
            SELECT
                DATE(j.JobDate) as JobDate,
                COUNT(*) as TotalJob
            FROM ListJob j
            $companyWhere
            GROUP BY DATE(j.JobDate)
        ";

        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        $searchValueEscaped = $this->db->escape_like_str($searchValue);

        if (!empty($searchValue)) {
            $sql .= " AND (
                sub.JobDate LIKE '%$searchValueEscaped%'
            )";
        }

        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        $totalRecordsQuery = "SELECT COUNT(DISTINCT DATE(JobDate)) as cnt FROM ListJob" . (($role != 1) ? " WHERE CompanyID = " . $companyID : "");
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $data[] = [
                "no" => $no++,
                "JobDate" => $row['JobDate'],
                "TotalJob" => $row['TotalJob']
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function JobEvidenceReport() {
        // Job Evidence (Photo) Report
        // Laporan jumlah foto evidence per job

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';
        $jobNameFilter = $request['jobNameFilter'] ?? '';
        $customerNameFilter = $request['customerNameFilter'] ?? '';
        $totalPhotoFilter = $request['totalPhotoFilter'] ?? '';
        $fromDateFilter = $request['fromDateFilter'] ?? '';  
        $untilDateFilter = $request['untilDateFilter'] ?? '';

        $columns = [
            0 => null,              // "No" - not sortable
            1 => 'JobName',
            2 => 'CustomerName',
            3 => 'TotalPhoto',
            4 => 'LastPhotoDate',
            5 => null               // "Photos" - not sortable (JSON blob)
        ];

        $orderColIndex = intval($request['order'][0]['column'] ?? 4);
        $orderDir = (isset($request['order'][0]['dir']) && strtoupper($request['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
        $orderCol = (isset($columns[$orderColIndex]) && $columns[$orderColIndex] !== null) ? $columns[$orderColIndex] : 'LastPhotoDate';
        $orderBy = $orderCol . " " . $orderDir;

        $role = $this->session->userdata('Role');
        $companyID = (int)$this->session->userdata('CompanyID');
        $companyWhere = ($role != 1) ? "WHERE j.CompanyID = " . $companyID : "";

        $baseQuery = "
            SELECT
                j.JobID,
                j.JobName,
                c.CustomerName,
                COUNT(d.ListDetailID) AS TotalPhoto,
                MAX(d.created_at) AS LastPhotoDate,
                CONCAT('[',
                    GROUP_CONCAT(
                        JSON_OBJECT(
                            'photo', d.Photo,
                            'caption', DATE_FORMAT(d.created_at, '%Y-%m-%d %H:%i:%s')
                        )
                        ORDER BY d.ListDetailID ASC SEPARATOR ','
                    ),
                ']') AS Photos
            FROM ListJob j
            JOIN Customer c ON j.CustomerID = c.CustomerID
            LEFT JOIN ListJobDetail d ON j.JobID = d.ListJobID
            $companyWhere
            GROUP BY j.JobID, j.JobName, c.CustomerName
        ";

        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        if (!empty($jobNameFilter)) {
            $jobNameFilterEscaped = $this->db->escape_like_str($jobNameFilter);
            $sql .= " AND sub.JobName LIKE '%$jobNameFilterEscaped%'";
        }

        if (!empty($customerNameFilter)) {
            $sql .= " AND sub.CustomerName LIKE '%" . $this->db->escape_like_str($customerNameFilter) . "%'";
        }

        if (!empty($totalPhotoFilter)) {
            $sql .= " AND sub.TotalPhoto >= " . intval($totalPhotoFilter);
        }

        if (!empty($fromDateFilter)) {
            $fromDateFilter = date('Y-m-d 00:00:00', strtotime($fromDateFilter));  // Set waktu ke 00:00:00
        }
        if (!empty($untilDateFilter)) {
            $untilDateFilter = date('Y-m-d 23:59:59', strtotime($untilDateFilter));  // Set waktu ke 23:59:59
        }
        
        if (!empty($fromDateFilter)) {
            $sql .= " AND sub.LastPhotoDate >= '" . $this->db->escape_str($fromDateFilter) . "'";
        }

        if (!empty($untilDateFilter)) {
            $sql .= " AND sub.LastPhotoDate <= '" . $this->db->escape_str($untilDateFilter) . "'";
        }

        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $sql .= " AND (
                sub.JobName LIKE '%$searchValueEscaped%' OR
                sub.CustomerName LIKE '%$searchValueEscaped%'
            )";
        }

        $recordsFiltered = $this->M_Global->globalquery($sql)->num_rows();

        $totalRecordsQuery = "SELECT COUNT(DISTINCT JobID) as cnt FROM ListJob" . (($role != 1) ? " WHERE CompanyID = " . $companyID : "");
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // Ambil data dengan sorting + paging
        $start = (int)$start;
        $length = (int)$length;
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $data[] = [
                "no" => $no++,
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "TotalPhoto" => $row['TotalPhoto'],
                "LastPhotoDate" => $row['LastPhotoDate'] ? date('Y-m-d H:i:s', strtotime($row['LastPhotoDate'])) : '-',
                "Photos" => $row['Photos']
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function getJobNames() {
        $sql = "SELECT DISTINCT JobName FROM ListJob";
        $query = $this->db->query($sql)->result_array();
        echo json_encode($query);
    }

    public function getCustomerNames() {
        $sql = "SELECT DISTINCT CustomerName FROM Customer";
        $query = $this->db->query($sql)->result_array();
        echo json_encode($query);
    }

    public function getCustomersByJobName() {
        $jobName = $this->input->get('jobName');  
        
        if (!empty($jobName)) {
            $sql = "
                SELECT DISTINCT c.CustomerName
                FROM ListJob j
                JOIN Customer c ON j.CustomerID = c.CustomerID
                WHERE j.JobName = ?
            ";
            $query = $this->db->query($sql, array($jobName))->result_array();
            echo json_encode($query);
        } else {
            echo json_encode([]);
        }
    }

    public function getJobsByCustomerName()
    {
        $customerName = $this->input->get('customerName');
        
        if (!empty($customerName)) {
            $sql = "
                SELECT DISTINCT j.JobName
                FROM ListJob j
                JOIN Customer c ON j.CustomerID = c.CustomerID
                WHERE c.CustomerName = ?
            ";
            $query = $this->db->query($sql, [$customerName])->result_array();
            echo json_encode($query);
        } else {
            // Jika kosong, kembalikan semua job
            $sql = "SELECT DISTINCT JobName FROM ListJob";
            $query = $this->db->query($sql)->result_array();
            echo json_encode($query);
        }
    }

    public function export_job_report()
    {
        try {
            $role = $this->session->userdata('Role');
            $companyID = $this->session->userdata('CompanyID');

            // Filter Parameters (POST)
            $from_date  = $this->input->post('from_date');
            $until_date = $this->input->post('until_date');
            $status_job = $this->input->post('status_job');

            // Validate dates
            if (empty($from_date) || empty($until_date)) {
                throw new Exception('Date filters are required.');
            }

            // Build applied filters description
            $appliedFilters = [];
            $appliedFilters[] = 'From: ' . date('M d, Y', strtotime($from_date));
            $appliedFilters[] = 'Until: ' . date('M d, Y', strtotime($until_date));
            if (!empty($status_job) && $status_job !== 'all_status') {
                $statusLabels = [
                    'awaiting_job' => 'Awaiting Driver',
                    'ongoing_job'  => 'Ongoing',
                    'finished'     => 'Finished'
                ];
                $appliedFilters[] = 'Status: ' . (isset($statusLabels[$status_job]) ? $statusLabels[$status_job] : $status_job);
            }
            if (empty($appliedFilters)) {
                $appliedFilters[] = 'None (showing all data)';
            }

            // Build main query
            $sql = "
                SELECT lj.JobID, lj.JobName, lj.JobDate, lj.TypeJob, lj.Status, lj.Notes, lj.AssignWhen, lj.FinishWhen,
                       c.CustomerName, lu.Fullname AS DriverName, lc.CompanyName
                FROM ListJob lj
                LEFT JOIN ListUser lu ON lj.UserID = lu.UserID
                LEFT JOIN Customer c ON lj.CustomerID = c.CustomerID
                LEFT JOIN ListCompany lc ON lj.CompanyID = lc.ListCompanyID
                WHERE DATE(lj.JobDate) >= ? AND DATE(lj.JobDate) <= ?
            ";
            $binds = [$from_date, $until_date];

            // Status filter
            if (!empty($status_job) && $status_job !== 'all_status') {
                if ($status_job === 'awaiting_job') {
                    $sql .= " AND lj.Status IS NULL";
                } elseif ($status_job === 'ongoing_job') {
                    $sql .= " AND lj.Status = ?";
                    $binds[] = 1;
                } elseif ($status_job === 'finished') {
                    $sql .= " AND lj.Status = ?";
                    $binds[] = 2;
                }
            }

            // Company filter for non-superadmin
            if ($role != 1) {
                $sql .= " AND lj.CompanyID = ?";
                $binds[] = (int)$companyID;
            }

            $sql .= " ORDER BY lj.JobDate DESC";

            $jobs = $this->M_Global->globalquery($sql, $binds)->result_array();

            if (count($jobs) == 0) {
                throw new Exception('No jobs found between ' . date('M d, Y', strtotime($from_date)) . ' and ' . date('M d, Y', strtotime($until_date)) . '.');
            }

            // Domain maps
            $typeJobMap = [1 => 'Line Interrupt', 2 => 'Reconnection', 3 => 'Short Circuit'];
            $statusMap  = [NULL => 'Awaiting Driver', 1 => 'Ongoing', 2 => 'Finished'];

            // === Load PHPExcel & Create Workbook ===
            $this->load->library('Excel');
            $objPHPExcel = new Excel();

            $objPHPExcel->getProperties()
                ->setCreator('E-FMS System')
                ->setTitle('Job Report')
                ->setDescription('Generated on ' . date('Y-m-d H:i:s'));

            // --- Reusable Style Arrays (copied from Customer Report reference) ---
            $titleStyle = [
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 16, 'color' => ['rgb' => '1F4E79']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ];
            $subtitleStyle = [
                'font' => ['name' => 'Arial', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
            ];
            $filterInfoStyle = [
                'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => '555555']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF9E6']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ];
            $headerStyle = [
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '2E75B6']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allborders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '1A5276']]
                ]
            ];
            $dataBorderStyle = [
                'borders' => [
                    'allborders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D5D8DC']]
                ],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
            ];
            $centerAlign = [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            $activeStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '1E7E34']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'D4EDDA']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            $inactiveStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'A71D2A']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8D7DA']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            $totalsStyle = [
                'font' => ['bold' => true, 'size' => 10],
                'borders' => [
                    'top' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '2E75B6']]
                ],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'EBF5FB']]
            ];
            $warningStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '856404']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF3CD']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];

            // ============================
            // SHEET 1: Job Summary
            // ============================
            $sheet1 = $objPHPExcel->setActiveSheetIndex(0);
            $sheet1->setTitle('Job Summary');

            // Row 1: Report Title
            $sheet1->mergeCells('A1:H1');
            $sheet1->setCellValue('A1', 'Job Report');
            $sheet1->getStyle('A1:H1')->applyFromArray($titleStyle);
            $sheet1->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet1->mergeCells('A2:H2');
            $sheet1->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet1->getStyle('A2:H2')->applyFromArray($subtitleStyle);

            // Row 3: Applied filters
            $sheet1->mergeCells('A3:H3');
            $sheet1->setCellValue('A3', 'Filters: ' . implode(' | ', $appliedFilters));
            $sheet1->getStyle('A3:H3')->applyFromArray($filterInfoStyle);
            $sheet1->getRowDimension(3)->setRowHeight(22);

            // Row 4: blank spacer

            // Row 5: Column Headers
            $headers = ['No', 'Job Name', 'Customer', 'Driver', 'Job Date', 'Type Job', 'Status', 'Company'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet1->setCellValue($col . '5', $header);
                $col++;
            }
            $sheet1->getStyle('A5:H5')->applyFromArray($headerStyle);
            $sheet1->getRowDimension(5)->setRowHeight(24);

            // Data rows
            $rowNum = 6;
            $no = 1;
            foreach ($jobs as $row) {
                $typeJob   = isset($typeJobMap[$row['TypeJob']]) ? $typeJobMap[$row['TypeJob']] : ($row['TypeJob'] ?: '-');
                $jobStatus = array_key_exists($row['Status'], $statusMap) ? $statusMap[$row['Status']] : ($row['Status'] ?: 'Awaiting Driver');

                $sheet1->setCellValue('A' . $rowNum, $no);
                $sheet1->setCellValueExplicit('B' . $rowNum, $row['JobName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValueExplicit('C' . $rowNum, $row['CustomerName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValueExplicit('D' . $rowNum, $row['DriverName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValue('E' . $rowNum, !empty($row['JobDate']) ? date('M d, Y H:i', strtotime($row['JobDate'])) : '-');
                $sheet1->setCellValue('F' . $rowNum, $typeJob);
                $sheet1->setCellValue('G' . $rowNum, $jobStatus);
                $sheet1->setCellValueExplicit('H' . $rowNum, $row['CompanyName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                // Apply borders
                $sheet1->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray($dataBorderStyle);

                // Center-align: No (A), Type Job (F), Status (G)
                $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Color-code status
                if ($jobStatus === 'Finished') {
                    $sheet1->getStyle("G{$rowNum}")->applyFromArray($activeStatusStyle);
                } elseif ($jobStatus === 'Ongoing') {
                    $sheet1->getStyle("G{$rowNum}")->applyFromArray($warningStatusStyle);
                } elseif ($jobStatus === 'Awaiting Driver') {
                    $sheet1->getStyle("G{$rowNum}")->applyFromArray($inactiveStatusStyle);
                }

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet1->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                    $sheet1->getStyle("H{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

                $no++;
                $rowNum++;
            }

            // Totals row
            $sheet1->mergeCells("A{$rowNum}:D{$rowNum}");
            $sheet1->setCellValue("A{$rowNum}", 'Total');
            $sheet1->setCellValue("E{$rowNum}", count($jobs) . ' job(s)');
            $sheet1->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray($totalsStyle);
            $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);

            // Column widths
            $sheet1->getColumnDimension('A')->setWidth(6);
            $sheet1->getColumnDimension('B')->setAutoSize(true);
            $sheet1->getColumnDimension('C')->setAutoSize(true);
            $sheet1->getColumnDimension('D')->setAutoSize(true);
            $sheet1->getColumnDimension('E')->setWidth(20);
            $sheet1->getColumnDimension('F')->setWidth(16);
            $sheet1->getColumnDimension('G')->setWidth(16);
            $sheet1->getColumnDimension('H')->setAutoSize(true);

            // ============================
            // SHEET 2: Job Details with Photos
            // ============================
            $objPHPExcel->createSheet();
            $sheet2 = $objPHPExcel->setActiveSheetIndex(1);
            $sheet2->setTitle('Job Details with Photos');

            // Row 1: Sheet Title
            $sheet2->mergeCells('A1:K1');
            $sheet2->setCellValue('A1', 'Job Details - Job Report');
            $sheet2->getStyle('A1:K1')->applyFromArray($titleStyle);
            $sheet2->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet2->mergeCells('A2:K2');
            $sheet2->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet2->getStyle('A2:K2')->applyFromArray($subtitleStyle);

            // Row 3: blank spacer

            // Row 4: Column Headers
            $detailHeaders = ['No', 'Job Name', 'Customer', 'Driver', 'Job Date', 'Type Job', 'Status', 'Notes', 'Assign Date', 'Finish Date', 'Photo'];
            $col = 'A';
            foreach ($detailHeaders as $header) {
                $sheet2->setCellValue($col . '4', $header);
                $col++;
            }
            $sheet2->getStyle('A4:K4')->applyFromArray($headerStyle);
            $sheet2->getRowDimension(4)->setRowHeight(24);

            // Detail data rows
            $rowNum = 5;
            $no = 1;
            foreach ($jobs as $row) {
                $jobID     = $row['JobID'];
                $typeJob   = isset($typeJobMap[$row['TypeJob']]) ? $typeJobMap[$row['TypeJob']] : ($row['TypeJob'] ?: '-');
                $jobStatus = array_key_exists($row['Status'], $statusMap) ? $statusMap[$row['Status']] : ($row['Status'] ?: 'Awaiting Driver');

                $sheet2->setCellValue('A' . $rowNum, $no);
                $sheet2->setCellValueExplicit('B' . $rowNum, $row['JobName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('C' . $rowNum, $row['CustomerName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('D' . $rowNum, $row['DriverName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue('E' . $rowNum, !empty($row['JobDate']) ? date('M d, Y H:i', strtotime($row['JobDate'])) : '-');
                $sheet2->setCellValue('F' . $rowNum, $typeJob);
                $sheet2->setCellValue('G' . $rowNum, $jobStatus);
                $sheet2->setCellValueExplicit('H' . $rowNum, $row['Notes'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue('I' . $rowNum, !empty($row['AssignWhen']) ? date('M d, Y H:i', strtotime($row['AssignWhen'])) : '-');
                $sheet2->setCellValue('J' . $rowNum, !empty($row['FinishWhen']) ? date('M d, Y H:i', strtotime($row['FinishWhen'])) : '-');

                // Apply borders and alignment
                $sheet2->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray($dataBorderStyle);
                $sheet2->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Color-code status
                if ($jobStatus === 'Finished') {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($activeStatusStyle);
                } elseif ($jobStatus === 'Ongoing') {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($warningStatusStyle);
                } elseif ($jobStatus === 'Awaiting Driver') {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($inactiveStatusStyle);
                }

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet2->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                    $sheet2->getStyle("H{$rowNum}:K{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

                // Photo embedding
                $dataGambar = $this->M_Global->globalquery("SELECT * FROM ListJobDetail WHERE ListJobID = ?", [(int)$jobID])->result_array();

                $offsetY = 0;
                foreach ($dataGambar as $gambar) {
                    $imageFile = basename($gambar['Photo']);
                    $finishedJobsPath = getenv('API_FINISHED_JOBS_PATH') ?: (FCPATH . '../api/storage/app/finished_jobs/');
                    $imagePath = $finishedJobsPath . $imageFile;

                    if (file_exists($imagePath)) {
                        $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $objDrawing->setName('Job Image');
                        $objDrawing->setDescription('Job Image');
                        $objDrawing->setPath($imagePath);
                        $objDrawing->setHeight(60);
                        $objDrawing->setCoordinates('K' . $rowNum);
                        $objDrawing->setOffsetY($offsetY);
                        $objDrawing->setWorksheet($sheet2);

                        $offsetY += 65;
                    }
                    // If file doesn't exist, just skip it
                }

                // Set row height to accommodate photos
                $sheet2->getRowDimension($rowNum)->setRowHeight(max(50, count($dataGambar) * 55));

                $no++;
                $rowNum++;
            }

            // Column widths for Sheet 2
            $sheet2->getColumnDimension('A')->setWidth(6);
            $sheet2->getColumnDimension('B')->setAutoSize(true);
            $sheet2->getColumnDimension('C')->setAutoSize(true);
            $sheet2->getColumnDimension('D')->setAutoSize(true);
            $sheet2->getColumnDimension('E')->setWidth(20);
            $sheet2->getColumnDimension('F')->setWidth(16);
            $sheet2->getColumnDimension('G')->setWidth(16);
            $sheet2->getColumnDimension('H')->setWidth(30);
            $sheet2->getColumnDimension('I')->setWidth(20);
            $sheet2->getColumnDimension('J')->setWidth(20);
            $sheet2->getColumnDimension('K')->setWidth(40);

            // Set Sheet 1 as active when file is opened
            $objPHPExcel->setActiveSheetIndex(0);

            // === Save file ===
            $saveDir = FCPATH . 'assets/dist/excel/';
            if (!is_dir($saveDir)) {
                if (!mkdir($saveDir, 0777, true)) {
                    throw new Exception('Failed to create directory: ' . $saveDir);
                }
            }

            $fileName = 'Job_Report_' . date('Ymd_His') . '.xlsx';
            $filePath = $saveDir . $fileName;

            $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
            $objWriter->save($filePath);

            if (!file_exists($filePath)) {
                throw new Exception('Failed to save Excel file at: ' . $filePath);
            }

            echo json_encode([
                'status'   => true,
                'message'  => 'File generated successfully.',
                'file_url' => base_url('assets/dist/excel/' . $fileName)
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status'  => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }




}