<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportCustomer extends MY_Controller
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
        $this->load->library('session');
    }

    public function index()
    {
        $data['title'] = "Customer Report";

        // Mengirim data ke view
        $this->render_page('main/report/reportCustomer', $data);

    }


    // Ajax datatables
    public function CustomerRetentionReport() {
        // Customer Retention Report
        // Pelanggan paling aktif & loyal

        
        // Get Session
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');
        
        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start   = intval($request['start'] ?? 0);
        $length  = intval($request['length'] ?? 10);
        $search  = $request['search']['value'] ?? '';
        
        // FILTER 
        $customerIDCustomerRetentionReport = $request['customerIDCustomerRetentionReport'] ?? null;
        $totalJobCustomerRetentionReport      = intval($request['totalJobCustomerRetentionReport'] ?? 0);
        $fromCustomerRetentionReport   = $request['fromCustomerRetentionReport'] ?? null;
        $untilCustomerRetentionReport  = $request['untilCustomerRetentionReport'] ?? null;
        $statusCustomerRetentionReport = $request['statusCustomerRetentionReport'] ?? null;
        $retentionDaysCustomerRetentionReport = intval($request['retentionDaysCustomerRetentionReport'] ?? 0);

        $where = [];
        if (!empty($fromCustomerRetentionReport) && !empty($untilCustomerRetentionReport)) {
            $where[] = "lj.JobDate BETWEEN " . $this->db->escape($fromCustomerRetentionReport) . " AND " . $this->db->escape($untilCustomerRetentionReport);
        } elseif (!empty($fromCustomerRetentionReport)) {
            $where[] = "lj.JobDate >= " . $this->db->escape($fromCustomerRetentionReport);
        } elseif (!empty($untilCustomerRetentionReport)) {
            $where[] = "lj.JobDate <= " . $this->db->escape($untilCustomerRetentionReport);
        }

        if (!empty($customerIDCustomerRetentionReport)) {
            $where[] = "c.CustomerID = " . intval($customerIDCustomerRetentionReport);
        }

        // Role-based filter
        if ($role != 1 && !empty($companyID)) {
            $where[] = "lj.CompanyID = " . intval($companyID);
        }

        // Search global
        if (!empty($search)) {
            $searchEscaped = $this->db->escape_like_str($search);
            $where[] = "(c.CustomerName LIKE '%{$searchEscaped}%' OR lc.CompanyName LIKE '%{$searchEscaped}%')";
        }

        $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $columnMap = [
            0 => "No",           
            1 => "CustomerName", 
            2 => "CompanyName", 
            3 => "TotalJob", 
            4 => "FirstJob",
            5 => "LastJob",
            6 => "RetentionDays",
            7 => "StatusCustomer"  
        ];

        $orderBy = "TotalJob DESC"; // Default order by TotalJob

        if (isset($request['order']) && is_array($request['order'])) {
            $orders = [];
            foreach ($request['order'] as $ord) {
                $colIndex = intval($ord['column']);
                $dir = strtolower($ord['dir']) === 'asc' ? 'ASC' : 'DESC';
                
                // Cek apakah kolom bisa diurutkan
                if (isset($columnMap[$colIndex]) && $columnMap[$colIndex] !== null) {
                    if ($columnMap[$colIndex] == "No") {
                        // Kolom "No" hanya untuk urutan row, tidak bisa disort
                        $orders[] = "ROW_NUMBER() OVER() " . $dir;
                    } else {
                        // Kolom yang bisa disort seperti CustomerName, CompanyName
                        $orders[] = $columnMap[$colIndex] . " " . $dir;
                    }
                }
            }
            if (!empty($orders)) {
                $orderBy = implode(", ", $orders);
            }
        }


        // BASE QUERY 
        $baseQuery =  "
            SELECT 
                c.CustomerID,
                c.CustomerName,
                lc.CompanyName,
                COUNT(DISTINCT lj.JobID) AS TotalJob,
                COALESCE(MIN(lj.JobDate), '-') AS FirstJob,
                COALESCE(MAX(lj.JobDate), '-') AS LastJob,
                TIMESTAMPDIFF(DAY, MIN(lj.JobDate), CURDATE()) AS RetentionDays,
                CASE 
                    WHEN MAX(lj.JobDate) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) THEN 'Active' 
                    ELSE 'Inactive' 
                END AS StatusCustomer,
                (SELECT lu2.Fullname FROM ListUser lu2 
                    WHERE lu2.UserID = (
                        SELECT lj2.UserID 
                        FROM ListJob lj2 
                        WHERE lj2.CustomerID = c.CustomerID 
                        ORDER BY lj2.JobDate DESC LIMIT 1
                    )
                    LIMIT 1
                ) AS LastHandledBy,
                (SELECT h2.Reason FROM HistoryCancelJob h2 
                    WHERE h2.JobID = (
                        SELECT lj3.JobID 
                        FROM ListJob lj3 
                        WHERE lj3.CustomerID = c.CustomerID 
                        ORDER BY lj3.JobDate DESC LIMIT 1
                    )
                    LIMIT 1
                ) AS LastCancelReason
            FROM Customer AS c
            LEFT JOIN ListJob AS lj ON c.CustomerID = lj.CustomerID
            LEFT JOIN (
                SELECT ListCompanyID, CompanyName
                FROM ListCompany
                GROUP BY ListCompanyID
            ) AS lc ON lj.CompanyID = lc.ListCompanyID
            {$whereSQL}
            GROUP BY c.CustomerID, c.CustomerName
        ";

        // Having Filters
        $having = [];
        if ($totalJobCustomerRetentionReport > 0) {
            $having[] = "TotalJob >= {$totalJobCustomerRetentionReport}";
        }
        if ($retentionDaysCustomerRetentionReport > 0) {
            $having[] = "RetentionDays >= {$retentionDaysCustomerRetentionReport}";
        }
        if (!empty($having)) {
            $baseQuery .= " HAVING " . implode(' AND ', $having);
        }

        // Status filter
        $statusFilter = "";
        if (!empty($statusCustomerRetentionReport)) {
            $statusFilter = " AND x.StatusCustomer = " . $this->db->escape($statusCustomerRetentionReport);
        }

        // 🔹 Final Query (Pagination + Order)
        $finalQuery = "
            SELECT * FROM ({$baseQuery}) x
            WHERE 1=1 {$statusFilter}
            ORDER BY {$orderBy}
            LIMIT {$start}, {$length}
        ";

        $result = $this->M_Global->globalquery($finalQuery)->result_array();

        // ==============================
        // 🔹 Tambahkan kolom No dan Action
        // ==============================
        $no = $start + 1;
        foreach ($result as &$row) {
            $row['No'] = $no++;
            $row['Action'] = '<button class="btn-tw-primary btn-detail"'
                . ' data-id="' . $row['CustomerID'] . '"'
                . ' data-name="' . htmlspecialchars($row['CustomerName'] ?? '-', ENT_QUOTES) . '"'
                . ' data-company="' . htmlspecialchars($row['CompanyName'] ?? '-', ENT_QUOTES) . '"'
                . ' data-totaljob="' . ($row['TotalJob'] ?? 0) . '"'
                . ' data-firstjob="' . ($row['FirstJob'] ?? '-') . '"'
                . ' data-lastjob="' . ($row['LastJob'] ?? '-') . '"'
                . ' data-retention="' . ($row['RetentionDays'] ?? '-') . '"'
                . ' data-status="' . ($row['StatusCustomer'] ?? '-') . '"'
                . ' data-handledby="' . htmlspecialchars($row['LastHandledBy'] ?? '-', ENT_QUOTES) . '"'
                . ' data-cancelreason="' . htmlspecialchars($row['LastCancelReason'] ?? '-', ENT_QUOTES) . '"'
                . '><i class="fas fa-eye mr-1"></i>Detail</button>';
        }

        // ==============================
        // 🔹 Total & Filtered count
        // ==============================
         $totalQuery = "
            SELECT COUNT(*) AS total FROM (
                SELECT c.CustomerID
                FROM Customer c
                LEFT JOIN ListJob lj ON c.CustomerID = lj.CustomerID
                " . ($role != 1 ? "WHERE lj.CompanyID = {$companyID}" : "") . "
                GROUP BY c.CustomerID
            ) xx
        ";
        $totalData = intval($this->M_Global->globalquery($totalQuery)->row()->total ?? 0);


        $filteredQuery = "
            SELECT COUNT(*) AS total FROM ({$baseQuery}) x
            WHERE 1=1 {$statusFilter}
        ";
        $filteredData = intval($this->M_Global->globalquery($filteredQuery)->row()->total ?? 0);

        // ==============================
        // 🔹 Return JSON untuk DataTables
        // ==============================
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalData,
            "recordsFiltered" => $filteredData,
            "data" => $result
        ]);

    }


    public function CustomerEngagementReport() {
        // Customer Engagement Report
        // Jumlah job per customer (engagement level)

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        $customerID = $request['customerID'] ?? '';
        $totalJob = $request['totalJob'] ?? '';

        $columns = [
            0 => 'no', 
            1 => 'CustomerName',
            2 => 'TotalJob'
        ];

        $orderColIndex = $request['order'][0]['column'] ?? 1; 
        $orderDir = $request['order'][0]['dir'] ?? 'desc';
        
        $orderByColumn = $columns[$orderColIndex] ?? 'TotalJob';

        if ($orderByColumn == 'TotalJob') {
            $orderBy = "sub.TotalJob " . $orderDir; 
        } else {
            $orderBy = "sub.CustomerName " . $orderDir;
        }

        $baseQuery = "
            SELECT 
                c.CustomerName,
                COUNT(j.JobID) as TotalJob
            FROM Customer c
            LEFT JOIN ListJob j ON c.CustomerID = j.CustomerID
            WHERE 1=1
        ";

        if (!empty($customerID)) {
            $baseQuery .= " AND c.CustomerID = " . intval($customerID);
        }

        $baseQuery .= " GROUP BY c.CustomerID, c.CustomerName";

        if (!empty($totalJob)) {
            $baseQuery .= " HAVING TotalJob >= " . intval($totalJob);
        } else {
            $baseQuery .= " HAVING TotalJob >= 0";
        }


        $sql = "SELECT * FROM ($baseQuery) AS sub WHERE 1=1";

        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $sql .= " AND (
                sub.CustomerName LIKE '%$searchValueEscaped%' OR
                sub.TotalJob LIKE '%$searchValueEscaped%'
            )";
        }

        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        $totalRecordsQuery = "SELECT COUNT(*) as cnt FROM Customer";
        $totalRecords = $this->M_Global->globalquery($totalRecordsQuery)->row()->cnt;

        $sql .= " ORDER BY $orderBy LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {
            $data[] = [
                "no" => $no++,
                "CustomerName" => $row['CustomerName'],
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

    public function getCustomers() {
        $customers = $this->M_Global->globalquery("SELECT CustomerID, CustomerName FROM Customer")->result_array();
        echo json_encode($customers);
    }

    public function getCustomersSession()
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $this->db->select('CustomerID, CustomerName');
        $this->db->from('Customer');

        // Role-based filter
        if ($role != 1) { // 1 = Superuser
            $this->db->where('ListCompanyID', $companyID);
        }

        $query = $this->db->get();
        $customers = $query->result_array();

        echo json_encode($customers);
    }


    public function getCustomerDetail($customerID = null) 
    {
        $customerID = $customerID ?? $this->input->get('CustomerID');
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        if (empty($customerID)) {
            echo json_encode(["error" => "CustomerID required"]);
            return;
        }

        $sql = "
            SELECT 
                lj.JobID,
                lj.JobName,
                lj.JobDate,
                lj.TypeJob,
                lj.Status AS JobStatus,
                lj.Notes,
                lu.Fullname AS HandledBy,
                lc.CompanyName,
                h.Reason AS CancelReason
            FROM ListJob lj
            LEFT JOIN ListUser lu ON lj.UserID = lu.UserID
            LEFT JOIN ListCompany lc ON lj.CompanyID = lc.ListCompanyID  -- ✅ perbaikan sesuai struktur tabel
            LEFT JOIN (
                SELECT JobID, MAX(Reason) AS Reason
                FROM HistoryCancelJob
                GROUP BY JobID
            ) h ON lj.JobID = h.JobID
            WHERE lj.CustomerID = ?
        ";

        $params = [$customerID];

        // Role-based filter: jika bukan superuser
        if ($role != 1 && !empty($companyID)) {
            $sql .= " AND lj.CompanyID = ?";
            $params[] = $companyID;
        }

        $sql .= "
            GROUP BY lj.JobID, lj.JobName, lj.JobDate, lj.TypeJob, lj.Status, lj.Notes, 
                    lu.Fullname, lc.CompanyName, h.Reason
            ORDER BY lj.JobDate DESC
        ";

        // Jalankan query
        $query = $this->db->query($sql, $params);
        if (!$query) {
            $error = $this->db->error();
            log_message('error', 'getCustomerDetail SQL error: ' . print_r($error, true));
            echo json_encode(["error" => "Database error: " . $error['message']]);
            return;
        }

        $data = $query->result_array();

        $typeJobMap = [
            1 => 'Line Interrupt',
            2 => 'Reconnection',
            3 => 'Short Circuit'
        ];

        $statusMap = [
            1 => 'Ongoing',
            2 => 'Finished Job'
        ];

        foreach ($data as &$job) {
            // Ubah TypeJob dari angka ke teks
            $job['TypeJob'] = isset($typeJobMap[$job['TypeJob']]) ? $typeJobMap[$job['TypeJob']] : $job['TypeJob'];
            
            // Ubah JobStatus dari angka ke teks
            $job['JobStatus'] = isset($statusMap[$job['JobStatus']]) ? $statusMap[$job['JobStatus']] : $job['JobStatus'];
        }


        $customer = $this->db->get_where('Customer', ['CustomerID' => $customerID])->row();

        // Return JSON
        echo json_encode([
            "CustomerID"   => $customerID,
            "CustomerName" => $customer->CustomerName ?? '-',
            "TotalJob"     => count($data),
            "Jobs"         => $data
        ]);
    }






    public function exportCustomerRetentionExcel()
    {
        try {
            $role = $this->session->userdata('Role');
            $companyID = $this->session->userdata('CompanyID');

            // FILTER (POST-based, matching ReportJob pattern)
            $customerID    = $this->input->post('customerIDCustomerRetentionReport');
            $totalJob      = intval($this->input->post('totalJobCustomerRetentionReport'));
            $from          = $this->input->post('fromCustomerRetentionReport');
            $until         = $this->input->post('untilCustomerRetentionReport');
            $retentionDays = intval($this->input->post('retentionDaysCustomerRetentionReport'));
            $status        = $this->input->post('statusCustomerRetentionReport');

            // Build applied filters description for the report header
            $appliedFilters = [];
            if (!empty($customerID)) {
                $custRow = $this->db->get_where('Customer', ['CustomerID' => $customerID])->row();
                $appliedFilters[] = 'Customer: ' . ($custRow ? $custRow->CustomerName : $customerID);
            }
            if (!empty($from))          $appliedFilters[] = 'From: ' . date('M d, Y', strtotime($from));
            if (!empty($until))         $appliedFilters[] = 'Until: ' . date('M d, Y', strtotime($until));
            if ($totalJob > 0)          $appliedFilters[] = 'Min Total Job: ' . $totalJob;
            if ($retentionDays > 0)     $appliedFilters[] = 'Min Retention Days: ' . $retentionDays;
            if (!empty($status))        $appliedFilters[] = 'Status: ' . $status;
            if (empty($appliedFilters)) $appliedFilters[] = 'None (showing all data)';

            // === Summary Query ===
            $sql = "
                SELECT
                    c.CustomerID,
                    c.CustomerName,
                    lc.CompanyName,
                    COUNT(DISTINCT lj.JobID) AS TotalJob,
                    COALESCE(MIN(lj.JobDate), '-') AS FirstJob,
                    COALESCE(MAX(lj.JobDate), '-') AS LastJob,
                    DATEDIFF(CURDATE(), MIN(lj.JobDate)) AS RetentionDays,
                    CASE
                        WHEN MAX(lj.JobDate) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) THEN 'Active'
                        ELSE 'Inactive'
                    END AS StatusCustomer
                FROM Customer c
                LEFT JOIN ListJob lj ON c.CustomerID = lj.CustomerID
                LEFT JOIN ListCompany lc ON lj.CompanyID = lc.ListCompanyID
                WHERE 1=1
            ";

            if ($role != 1 && !empty($companyID)) {
                $sql .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }
            if (!empty($customerID)) {
                $sql .= " AND c.CustomerID = " . $this->db->escape($customerID);
            }
            if (!empty($from) && !empty($until)) {
                $sql .= " AND lj.JobDate BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);
            }

            $sql .= " GROUP BY c.CustomerID, c.CustomerName, lc.CompanyName";

            $having = [];
            if ($totalJob > 0)      $having[] = "TotalJob >= " . intval($totalJob);
            if ($retentionDays > 0) $having[] = "RetentionDays >= " . intval($retentionDays);
            if (!empty($having))    $sql .= " HAVING " . implode(" AND ", $having);

            $statusFilter = "";
            if (!empty($status)) {
                $statusFilter = " AND x.StatusCustomer = " . $this->db->escape($status);
            }

            $sql = "SELECT * FROM ({$sql}) x WHERE 1=1 {$statusFilter} ORDER BY TotalJob DESC";
            $summaryData = $this->db->query($sql)->result_array();

            if (count($summaryData) == 0) {
                throw new Exception('No customer data found matching the applied filters.');
            }

            // === Job Details Query (single batch query instead of N+1) ===
            $customerIDs = array_column($summaryData, 'CustomerID');
            $customerMap = [];
            foreach ($summaryData as $row) {
                $customerMap[$row['CustomerID']] = [
                    'CustomerName' => $row['CustomerName'],
                    'CompanyName'  => $row['CompanyName']
                ];
            }

            $escapedIDs = implode(',', array_map('intval', $customerIDs));
            $detailWhere = "WHERE lj.CustomerID IN ({$escapedIDs})";
            if ($role != 1 && !empty($companyID)) {
                $detailWhere .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }
            if (!empty($from) && !empty($until)) {
                $detailWhere .= " AND lj.JobDate BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);
            }

            $detailSQL = "
                SELECT
                    lj.CustomerID,
                    lj.JobName,
                    lj.JobDate,
                    lj.TypeJob,
                    lj.Status AS JobStatus,
                    lj.Notes,
                    lu.Fullname AS HandledBy,
                    h.Reason AS CancelReason
                FROM ListJob lj
                LEFT JOIN ListUser lu ON lj.UserID = lu.UserID
                LEFT JOIN (
                    SELECT JobID, MAX(Reason) AS Reason
                    FROM HistoryCancelJob
                    GROUP BY JobID
                ) h ON lj.JobID = h.JobID
                {$detailWhere}
                GROUP BY lj.JobID, lj.JobName, lj.JobDate, lj.TypeJob, lj.Status, lj.Notes, lu.Fullname, h.Reason
                ORDER BY lj.CustomerID, lj.JobDate DESC
            ";
            $detailData = $this->db->query($detailSQL)->result_array();

            $typeJobMap = [1 => 'Line Interrupt', 2 => 'Reconnection', 3 => 'Short Circuit'];
            $statusMap  = [1 => 'Ongoing', 2 => 'Finished Job'];

            // === Load PHPExcel & Create Workbook ===
            $this->load->library('Excel');
            $objPHPExcel = new Excel();

            $objPHPExcel->getProperties()
                ->setCreator('E-FMS System')
                ->setTitle('Customer Retention Report')
                ->setDescription('Generated on ' . date('Y-m-d H:i:s'));

            // --- Reusable Style Arrays ---
            $titleStyle = [
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 16, 'color' => ['rgb' => '1F4E79']],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ]
            ];
            $subtitleStyle = [
                'font' => ['name' => 'Arial', 'size' => 10, 'italic' => true, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT]
            ];
            $filterInfoStyle = [
                'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => '555555']],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF9E6']],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ]
            ];
            $headerStyle = [
                'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '2E75B6']],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '1A5276']]
                ]
            ];
            $dataBorderStyle = [
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'D5D8DC']]
                ],
                'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER]
            ];
            $centerAlign = [
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ];
            $activeStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '1E7E34']],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'D4EDDA']],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ];
            $inactiveStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'A71D2A']],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F8D7DA']],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ];
            $totalsStyle = [
                'font' => ['bold' => true, 'size' => 10],
                'borders' => [
                    'top' => ['style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => ['rgb' => '2E75B6']]
                ],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'EBF5FB']]
            ];

            // ============================
            // SHEET 1: Customer Summary
            // ============================
            $sheet1 = $objPHPExcel->setActiveSheetIndex(0);
            $sheet1->setTitle('Customer Summary');

            // Row 1: Report Title
            $sheet1->mergeCells('A1:H1');
            $sheet1->setCellValue('A1', 'Customer Retention Report');
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
            $headers = ['No', 'Customer Name', 'Company Name', 'Total Job', 'First Job', 'Last Job', 'Retention Days', 'Status'];
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
            $totalJobSum = 0;
            foreach ($summaryData as $row) {
                $sheet1->setCellValue('A' . $rowNum, $no);
                $sheet1->setCellValueExplicit('B' . $rowNum, $row['CustomerName'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet1->setCellValueExplicit('C' . $rowNum, $row['CompanyName'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet1->setCellValue('D' . $rowNum, intval($row['TotalJob']));
                $sheet1->setCellValue('E' . $rowNum, ($row['FirstJob'] !== '-' && $row['FirstJob'] !== null) ? date('M d, Y', strtotime($row['FirstJob'])) : '-');
                $sheet1->setCellValue('F' . $rowNum, ($row['LastJob'] !== '-' && $row['LastJob'] !== null) ? date('M d, Y', strtotime($row['LastJob'])) : '-');
                $sheet1->setCellValue('G' . $rowNum, intval($row['RetentionDays']));
                $sheet1->setCellValue('H' . $rowNum, $row['StatusCustomer']);

                // Apply borders
                $sheet1->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray($dataBorderStyle);

                // Center-align specific columns
                $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("D{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Color-code status
                if ($row['StatusCustomer'] === 'Active') {
                    $sheet1->getStyle("H{$rowNum}")->applyFromArray($activeStatusStyle);
                } else {
                    $sheet1->getStyle("H{$rowNum}")->applyFromArray($inactiveStatusStyle);
                }

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet1->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray([
                        'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

                $totalJobSum += intval($row['TotalJob']);
                $no++;
                $rowNum++;
            }

            // Totals row
            $sheet1->mergeCells("A{$rowNum}:C{$rowNum}");
            $sheet1->setCellValue("A{$rowNum}", 'Total');
            $sheet1->setCellValue("D{$rowNum}", $totalJobSum);
            $sheet1->setCellValue("E{$rowNum}", count($summaryData) . ' customer(s)');
            $sheet1->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray($totalsStyle);
            $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
            $sheet1->getStyle("D{$rowNum}")->applyFromArray($centerAlign);

            // Column widths
            $sheet1->getColumnDimension('A')->setWidth(6);
            $sheet1->getColumnDimension('B')->setAutoSize(true);
            $sheet1->getColumnDimension('C')->setAutoSize(true);
            $sheet1->getColumnDimension('D')->setWidth(12);
            $sheet1->getColumnDimension('E')->setWidth(18);
            $sheet1->getColumnDimension('F')->setWidth(18);
            $sheet1->getColumnDimension('G')->setWidth(16);
            $sheet1->getColumnDimension('H')->setWidth(14);

            // ============================
            // SHEET 2: Job Details
            // ============================
            $objPHPExcel->createSheet();
            $sheet2 = $objPHPExcel->setActiveSheetIndex(1);
            $sheet2->setTitle('Job Details');

            // Row 1: Sheet Title
            $sheet2->mergeCells('A1:J1');
            $sheet2->setCellValue('A1', 'Job Details - Customer Retention Report');
            $sheet2->getStyle('A1:J1')->applyFromArray($titleStyle);
            $sheet2->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet2->mergeCells('A2:J2');
            $sheet2->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet2->getStyle('A2:J2')->applyFromArray($subtitleStyle);

            // Row 3: blank spacer

            // Row 4: Column Headers
            $detailHeaders = ['No', 'Customer Name', 'Company', 'Job Name', 'Job Date', 'Type Job', 'Status', 'Notes', 'Handled By', 'Cancel Reason'];
            $col = 'A';
            foreach ($detailHeaders as $header) {
                $sheet2->setCellValue($col . '4', $header);
                $col++;
            }
            $sheet2->getStyle('A4:J4')->applyFromArray($headerStyle);
            $sheet2->getRowDimension(4)->setRowHeight(24);

            // Detail data rows
            $rowNum = 5;
            $no = 1;
            foreach ($detailData as $job) {
                $cid = $job['CustomerID'];
                $custInfo = isset($customerMap[$cid]) ? $customerMap[$cid] : ['CustomerName' => '-', 'CompanyName' => '-'];

                $typeJob   = isset($typeJobMap[$job['TypeJob']]) ? $typeJobMap[$job['TypeJob']] : ($job['TypeJob'] ?: '-');
                $jobStatus = isset($statusMap[$job['JobStatus']]) ? $statusMap[$job['JobStatus']] : ($job['JobStatus'] ?: '-');

                $sheet2->setCellValue('A' . $rowNum, $no);
                $sheet2->setCellValueExplicit('B' . $rowNum, $custInfo['CustomerName'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('C' . $rowNum, $custInfo['CompanyName'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('D' . $rowNum, $job['JobName'] ?: '-', PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet2->setCellValue('E' . $rowNum, !empty($job['JobDate']) ? date('M d, Y H:i', strtotime($job['JobDate'])) : '-');
                $sheet2->setCellValue('F' . $rowNum, $typeJob);
                $sheet2->setCellValue('G' . $rowNum, $jobStatus);
                $sheet2->setCellValueExplicit('H' . $rowNum, $job['Notes'] ?: '-', PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('I' . $rowNum, $job['HandledBy'] ?: '-', PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('J' . $rowNum, $job['CancelReason'] ?: '-', PHPExcel_Cell_DataType::TYPE_STRING);

                // Apply borders and alignment
                $sheet2->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray($dataBorderStyle);
                $sheet2->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet2->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray([
                        'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

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
            $sheet2->getColumnDimension('G')->setWidth(14);
            $sheet2->getColumnDimension('H')->setWidth(30);
            $sheet2->getColumnDimension('I')->setAutoSize(true);
            $sheet2->getColumnDimension('J')->setWidth(30);

            // Set Sheet 1 as active when file is opened
            $objPHPExcel->setActiveSheetIndex(0);

            // === Save file ===
            $saveDir = FCPATH . 'assets/dist/excel/';
            if (!is_dir($saveDir)) {
                if (!mkdir($saveDir, 0777, true)) {
                    throw new Exception('Failed to create directory: ' . $saveDir);
                }
            }

            $fileName = 'Customer_Retention_Report_' . date('Ymd_His') . '.xlsx';
            $filePath = $saveDir . $fileName;

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
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