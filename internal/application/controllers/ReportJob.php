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

        $dataJobHead = $this->M_Global->globalquery("SELECT ListJob.*, ListUser.Fullname, ListUser.Email, ListUser.PhoneNumber, Customer.CustomerName, Customer.Address, Customer.PhoneNumber FROM ListJob 
        LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID  
        LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID  
        WHERE ListJob.JobID = '$jobID' ")->result_array();

        $dataJobHead = array_map(function($job) {

            $jobID = $job['JobID'];

            $statusCancelJob = $this->M_Global->globalquery("SELECT UserBefore, HistoryCancelJob.created_at, Fullname, Reason FROM HistoryCancelJob LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC ")->result_array();

            $assets_job = $this->M_Global->globalquery("SELECT * FROM ListJobDetail WHERE ListJobID = '$jobID' ")->result_array();

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

        $data = $this->M_Global->globalquery("SELECT * FROM ListJob WHERE DATE(JobDate) = '$dnow' ")->result_array();

        // echo json_encode($data);
        // die;

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
        $orderDir = $request['order'][0]['dir'] ?? 'asc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

        $dnow = date('Y-m-d');

       $sql = "
            SELECT * FROM ListJob lj 
            LEFT JOIN ListUser lu ON lj.UserID = lu.UserID 
            LEFT JOIN Customer c ON lj.CustomerID = c.CustomerID
        ";

        $where = []; // buat nampung kondisi WHERE

        if (empty($filterFromDate)) {
            $where[] = "DATE(lj.JobDate) = '" . $this->db->escape_str($dnow) . "'";
        }

        if (!empty($filterFromDate) && !empty($filterUntilDate)) {
            $where[] = "DATE(lj.JobDate) >= '" . $this->db->escape_str($filterFromDate) . "'";
            $where[] = "DATE(lj.JobDate) <= '" . $this->db->escape_str($filterUntilDate) . "'";
        }

        if($role != 1) {
            $where[] = "lj.CompanyID = " . $companyID;
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
            $sql .= " AND (
                lj.JobName LIKE '%$searchValue%' OR c.CustomerName LIKE '%$searchValue%' OR lu.Fullname LIKE '%$searchValue%'
            )";
        }

        // total sebelum limit
        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsTotal = count($totalQuery);

        // Ambil data dengan LIMIT (untuk paging)
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();


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
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

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
        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // tambah order + limit
        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            // $photos = json_decode($row['Dokumentasi'], true) ?? [];
            // $photoHtml = '';

            // foreach ($photos as $p) {
            //     $photoHtml .= "<div class='job-photo'><img src='{$p['photo']}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;margin:4px'><br><small>{$p['caption']}</small></div>";
            // }

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
            0 => 'j.JobID',
            1 => 'j.JobName',
            2 => 'c.CustomerName',
            3 => 'j.created_at',
            4 => 'j.AssignWhen',
            5 => 'sub.DurationMinutes'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 3;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

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
        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // order + limit
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
            0 => 'j.JobID',
            1 => 'j.JobName',
            2 => 'c.CustomerName',
            3 => 'j.Status',
            4 => 'j.JobDate'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 4;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

        $baseQuery = "
            SELECT 
                j.JobID,
                j.JobName,
                c.CustomerName,
                j.Status,
                j.JobDate
            FROM ListJob j
            JOIN Customer c ON j.CustomerID = c.CustomerID
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

        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM ListJob";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

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
            0 => 'no',
            1 => 'JobDate',
            2 => 'TotalJob'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

        $baseQuery = "
            SELECT 
                DATE(j.JobDate) as JobDate,
                COUNT(*) as TotalJob
            FROM ListJob j
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

        $totalRecordsQuery = "SELECT COUNT(DISTINCT DATE(JobDate)) as cnt FROM ListJob";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

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
            0 => 'no',            
            1 => 'JobName',
            2 => 'CustomerName',
            3 => 'TotalPhoto',
            4 => 'LastPhotoDate',
            5 => 'Photos'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 4;
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        $orderBy = $columns[$orderColIndex] . " " . $orderDir;

        if ($columns[$orderColIndex] === 'no') {
            $orderBy = "LastPhotoDate DESC";
        }

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

        $totalRecordsQuery = "SELECT COUNT(DISTINCT JobID) as cnt FROM ListJob";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        // Ambil data dengan sorting + paging
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
            $companyID =  $this->session->userdata('CompanyID');
            
            $from_date = $this->input->post('from_date');
            $until_date = $this->input->post('until_date');

            // --- Validasi tanggal ---
            if (empty($from_date) || empty($until_date)) {
                throw new Exception('Filter tanggal belum diisi.');
            }

            if($role != 1) {

                $where_company = " AND ListJob.CompanyID = " . $companyID;

            }

            // --- Ambil data dari database ---
            $query = "
                SELECT * FROM ListJob 
                LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID 
                LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID 
                WHERE DATE(JobDate) >= '$from_date' AND DATE(JobDate) <= '$until_date' AND ListJob.Status = 2 
            " . $where_company;

            $jobs = $this->M_Global->globalquery($query)->result_array();

            // $jobs = $this->db->query($query, [$from_date, $until_date])->result_array();

            if (count($jobs) == 0) {
                throw new Exception('No data found for the given date range.');
            }

            // --- Load library Excel ---
            $this->load->library('Excel'); 
            $objPHPExcel = new Excel(); 
            $sheet = $objPHPExcel->setActiveSheetIndex(0);

            // --- Header Excel ---
            $headers = ['No', 'Customer', 'Driver', 'Job Name', 'Job Date', 'Photo'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col.'1', $header);
                $col++;
            }

            foreach (range('A', 'E') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $sheet->getColumnDimension('F')->setWidth(40);

            // --- Isi Data Excel ---
            $rowNum = 2;
            $no = 1;
            foreach ($jobs as $job) {

                $jobID = $job['JobID'];

                $sheet->setCellValue('A'.$rowNum, $no++);
                $sheet->setCellValue('B'.$rowNum, $job['CustomerName']);
                $sheet->setCellValue('C'.$rowNum, $job['Fullname']);
                $sheet->setCellValue('D'.$rowNum, $job['JobName']);
                $sheet->setCellValue('E'.$rowNum, date('d M Y H:i', strtotime($job['JobDate'])));
                // --- Tambahkan gambar per data ---

                $dataGambar = $this->M_Global->globalquery("SELECT * FROM ListJobDetail WHERE ListJobID = '$jobID' ")->result_array();

                $offsetY = 0;
                
                foreach ($dataGambar as $gambar) {
    // nama file (tanpa domain)
                    $imageFile = basename($gambar['Photo']); // misal hasilnya "job_24_1761110012_0.png"
                    
                    // path absolut di server
                    $imagePath = FCPATH . '../api/storage/app/finished_jobs/' . $imageFile;

                    if (file_exists($imagePath)) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('Job Image');
                        $objDrawing->setDescription('Job Image');
                        $objDrawing->setPath($imagePath);
                        $objDrawing->setHeight(60);
                        $objDrawing->setCoordinates('F' . $rowNum);
                        $objDrawing->setOffsetY($offsetY);
                        $objDrawing->setWorksheet($sheet);

                        $offsetY += 65; // jarak antar gambar
                    } else {
                        throw new Exception("File not found at: " . $imagePath);
                    }
                }


                // --- Atur tinggi baris agar gambar muat ---
                $sheet->getRowDimension($rowNum)->setRowHeight(max(50, count($dataGambar) * 55));
                $rowNum++;
                
            }

            // --- Folder tujuan ---
            $saveDir = FCPATH . 'assets/dist/excel/';
            if (!is_dir($saveDir)) {
                if (!mkdir($saveDir, 0777, true)) {
                    throw new Exception('Gagal membuat folder: ' . $saveDir);
                }
            }

            $fileName = 'Report_Job_' . date('Ymd_His') . '.xlsx';
            $filePath = $saveDir . $fileName;

            // --- Simpan file Excel ---
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($filePath);

            if (!file_exists($filePath)) {
                throw new Exception('Gagal menyimpan file Excel di: ' . $filePath);
            }

            // --- Jika berhasil ---
            echo json_encode([
                'status' => true,
                'message' => 'File berhasil dibuat.',
                'file_url' => base_url('assets/dist/excel/' . $fileName)
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }




}