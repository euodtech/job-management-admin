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
            $dfrom = date("Y-m-d", strtotime("-6 days"));
            $where_job = " AND DATE(JobDate) >= ? AND DATE(JobDate) <= ?";
            $where_job_binds = [$dfrom, $dnow];
            $where_job_cancel = " AND DATE(created_at) >= ? AND DATE(created_at) <= ?";
            $where_job_cancel_binds = [$dfrom, $dnow];
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
        
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $where[] = "(ListUser.Fullname LIKE '%" . $searchValueEscaped . "%' OR ListUser.Email LIKE '%" . $searchValueEscaped . "%')";
        }

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
        $user_id = (int)$this->input->get('user_id');
        $job_ids = array_filter(array_map('intval', explode(',', $job_ids_raw)));

        if (empty($job_ids) || empty($user_id)) {
            $data['job'] = [];
            $this->load->view('main/report/detail_driver_cancel', $data);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($job_ids), '?'));
        $binds = $job_ids;
        $driverFilter = ' AND HistoryCancelJob.UserBefore = ?';
        $binds[] = $user_id;
        $companyFilter = '';
        if ($role != 1) {
            $companyFilter = ' AND ListJob.CompanyID = ?';
            $binds[] = $companyID;
        }

        $data['job'] = $this->M_Global->globalquery("
        SELECT * FROM HistoryCancelJob
        LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID
        LEFT JOIN ListJob ON HistoryCancelJob.JobID = ListJob.JobID
        WHERE HistoryCancelJob.JobID IN ($placeholders)" . $driverFilter . $companyFilter . " ORDER By HistoryCancelJob.created_at DESC
        ", $binds)->result_array();

        $this->load->view('main/report/detail_driver_cancel', $data);
    }

    public function exportDriverExcel()
    {
        try {
            $role = $this->session->userdata('Role');
            $companyID = $this->session->userdata('CompanyID');

            // FILTER (POST-based)
            $from  = $this->input->post('from_date');
            $until = $this->input->post('until_date');

            if (empty($from))  $from  = date('Y-m-d', strtotime('-6 days'));
            if (empty($until)) $until = date('Y-m-d');

            // Build applied filters description for the report header
            $appliedFilters = [];
            $appliedFilters[] = 'From: ' . date('M d, Y', strtotime($from));
            $appliedFilters[] = 'Until: ' . date('M d, Y', strtotime($until));

            // === Summary Query (single aggregation instead of N+1) ===
            $sql = "
                SELECT
                    lu.UserID,
                    lu.Fullname,
                    lu.Email,
                    COUNT(DISTINCT lj.JobID) AS TotalJob,
                    COUNT(DISTINCT CASE WHEN lj.Status = 2 THEN lj.JobID END) AS CompleteJob,
                    COUNT(DISTINCT CASE WHEN lj.Status = 1 THEN lj.JobID END) AS OngoingJob
                FROM ListUser lu
                LEFT JOIN ListJob lj ON lu.UserID = lj.UserID
                    AND DATE(lj.JobDate) BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);

            if ($role != 1 && !empty($companyID)) {
                $sql .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }

            if ($role != 1 && !empty($companyID)) {
                $sql .= " WHERE lu.ListCompanyID = " . $this->db->escape($companyID);
            }

            $sql .= " GROUP BY lu.UserID, lu.Fullname, lu.Email ORDER BY TotalJob DESC";
            $summaryData = $this->db->query($sql)->result_array();

            if (count($summaryData) == 0) {
                throw new Exception('No rider data found matching the applied filters.');
            }

            // === Cancel Jobs Count Query ===
            $driverIDs = array_column($summaryData, 'UserID');
            $escapedDriverIDs = implode(',', array_map('intval', $driverIDs));

            $cancelSQL = "
                SELECT hcj.UserBefore, COUNT(DISTINCT hcj.JobID) AS CancelJob
                FROM HistoryCancelJob hcj
                LEFT JOIN ListJob lj ON hcj.JobID = lj.JobID
                WHERE DATE(hcj.created_at) BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until) . "
                AND hcj.UserBefore IN ({$escapedDriverIDs})";

            if ($role != 1 && !empty($companyID)) {
                $cancelSQL .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }

            $cancelSQL .= " GROUP BY hcj.UserBefore";
            $cancelData = $this->db->query($cancelSQL)->result_array();

            // Build cancel count map by UserID
            $cancelMap = [];
            foreach ($cancelData as $row) {
                $cancelMap[$row['UserBefore']] = intval($row['CancelJob']);
            }

            // Merge cancel counts into summary data
            foreach ($summaryData as &$row) {
                $row['CancelJob'] = isset($cancelMap[$row['UserID']]) ? $cancelMap[$row['UserID']] : 0;
            }
            unset($row);

            // Build driver map for detail sheets
            $driverMap = [];
            foreach ($summaryData as $row) {
                $driverMap[$row['UserID']] = [
                    'Fullname' => $row['Fullname'],
                    'Email'    => $row['Email']
                ];
            }

            // === Job Details Query (single batch) ===
            $detailWhere = "WHERE lu.UserID IN ({$escapedDriverIDs})
                AND DATE(lj.JobDate) BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);

            if ($role != 1 && !empty($companyID)) {
                $detailWhere .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }

            $detailSQL = "
                SELECT
                    lj.JobID, lj.JobName, lj.JobDate, lj.TypeJob, lj.Status AS JobStatus,
                    lj.Notes, lj.AssignWhen, lj.FinishWhen,
                    lu.UserID, lu.Fullname AS DriverName,
                    c.CustomerName
                FROM ListJob lj
                LEFT JOIN ListUser lu ON lj.UserID = lu.UserID
                LEFT JOIN Customer c ON lj.CustomerID = c.CustomerID
                {$detailWhere}
                ORDER BY lu.Fullname, lj.JobDate DESC
            ";
            $detailData = $this->db->query($detailSQL)->result_array();

            // === Cancelled Jobs Query (single batch) ===
            $cancelDetailSQL = "
                SELECT
                    hcj.JobID, lj.JobName, lj.JobDate, lj.TypeJob,
                    hcj.Reason, hcj.created_at AS CancelDate,
                    lu.UserID, lu.Fullname AS DriverName
                FROM HistoryCancelJob hcj
                LEFT JOIN ListJob lj ON hcj.JobID = lj.JobID
                LEFT JOIN ListUser lu ON hcj.UserBefore = lu.UserID
                WHERE hcj.UserBefore IN ({$escapedDriverIDs})
                AND DATE(hcj.created_at) BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);

            if ($role != 1 && !empty($companyID)) {
                $cancelDetailSQL .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }

            $cancelDetailSQL .= " ORDER BY lu.Fullname, hcj.created_at DESC";
            $cancelDetailData = $this->db->query($cancelDetailSQL)->result_array();

            $typeJobMap = [1 => 'Line Interrupt', 2 => 'Reconnection', 3 => 'Short Circuit'];
            $statusMap  = [1 => 'Ongoing', 2 => 'Finished'];

            // === Load PHPExcel & Create Workbook ===
            $this->load->library('Excel');
            $objPHPExcel = new Excel();

            $objPHPExcel->getProperties()
                ->setCreator('E-FMS System')
                ->setTitle('Rider Report')
                ->setDescription('Generated on ' . date('Y-m-d H:i:s'));

            // --- Reusable Style Arrays ---
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
            $warningStatusStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '856404']],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF3CD']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            $totalsStyle = [
                'font' => ['bold' => true, 'size' => 10],
                'borders' => [
                    'top' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '2E75B6']]
                ],
                'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'EBF5FB']]
            ];

            // ============================
            // SHEET 1: Rider Summary
            // ============================
            $sheet1 = $objPHPExcel->setActiveSheetIndex(0);
            $sheet1->setTitle('Rider Summary');

            // Row 1: Report Title
            $sheet1->mergeCells('A1:G1');
            $sheet1->setCellValue('A1', 'Rider Report');
            $sheet1->getStyle('A1:G1')->applyFromArray($titleStyle);
            $sheet1->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet1->mergeCells('A2:G2');
            $sheet1->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet1->getStyle('A2:G2')->applyFromArray($subtitleStyle);

            // Row 3: Applied filters
            $sheet1->mergeCells('A3:G3');
            $sheet1->setCellValue('A3', 'Filters: ' . implode(' | ', $appliedFilters));
            $sheet1->getStyle('A3:G3')->applyFromArray($filterInfoStyle);
            $sheet1->getRowDimension(3)->setRowHeight(22);

            // Row 4: blank spacer

            // Row 5: Column Headers
            $headers = ['No', 'Rider Name', 'Email', 'Total Job', 'Complete Job', 'Ongoing Job', 'Cancel Job'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet1->setCellValue($col . '5', $header);
                $col++;
            }
            $sheet1->getStyle('A5:G5')->applyFromArray($headerStyle);
            $sheet1->getRowDimension(5)->setRowHeight(24);

            // Data rows
            $rowNum = 6;
            $no = 1;
            $totalJobSum = 0;
            $completeJobSum = 0;
            $ongoingJobSum = 0;
            $cancelJobSum = 0;
            foreach ($summaryData as $row) {
                $sheet1->setCellValue('A' . $rowNum, $no);
                $sheet1->setCellValueExplicit('B' . $rowNum, $row['Fullname'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValueExplicit('C' . $rowNum, $row['Email'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet1->setCellValue('D' . $rowNum, intval($row['TotalJob']));
                $sheet1->setCellValue('E' . $rowNum, intval($row['CompleteJob']));
                $sheet1->setCellValue('F' . $rowNum, intval($row['OngoingJob']));
                $sheet1->setCellValue('G' . $rowNum, intval($row['CancelJob']));

                // Apply borders
                $sheet1->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($dataBorderStyle);

                // Center-align specific columns
                $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("D{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("E{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
                $sheet1->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet1->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

                $totalJobSum += intval($row['TotalJob']);
                $completeJobSum += intval($row['CompleteJob']);
                $ongoingJobSum += intval($row['OngoingJob']);
                $cancelJobSum += intval($row['CancelJob']);
                $no++;
                $rowNum++;
            }

            // Totals row
            $sheet1->mergeCells("A{$rowNum}:C{$rowNum}");
            $sheet1->setCellValue("A{$rowNum}", 'Total');
            $sheet1->setCellValue("C{$rowNum}", count($summaryData) . ' rider(s)');
            $sheet1->setCellValue("D{$rowNum}", $totalJobSum);
            $sheet1->setCellValue("E{$rowNum}", $completeJobSum);
            $sheet1->setCellValue("F{$rowNum}", $ongoingJobSum);
            $sheet1->setCellValue("G{$rowNum}", $cancelJobSum);
            $sheet1->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($totalsStyle);
            $sheet1->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
            $sheet1->getStyle("D{$rowNum}")->applyFromArray($centerAlign);
            $sheet1->getStyle("E{$rowNum}")->applyFromArray($centerAlign);
            $sheet1->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
            $sheet1->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

            // Column widths
            $sheet1->getColumnDimension('A')->setWidth(6);
            $sheet1->getColumnDimension('B')->setAutoSize(true);
            $sheet1->getColumnDimension('C')->setAutoSize(true);
            $sheet1->getColumnDimension('D')->setWidth(14);
            $sheet1->getColumnDimension('E')->setWidth(14);
            $sheet1->getColumnDimension('F')->setWidth(14);
            $sheet1->getColumnDimension('G')->setWidth(14);

            // ============================
            // SHEET 2: Job Details
            // ============================
            $objPHPExcel->createSheet();
            $sheet2 = $objPHPExcel->setActiveSheetIndex(1);
            $sheet2->setTitle('Job Details');

            // Row 1: Sheet Title
            $sheet2->mergeCells('A1:J1');
            $sheet2->setCellValue('A1', 'Job Details - Rider Report');
            $sheet2->getStyle('A1:J1')->applyFromArray($titleStyle);
            $sheet2->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet2->mergeCells('A2:J2');
            $sheet2->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet2->getStyle('A2:J2')->applyFromArray($subtitleStyle);

            // Row 3: blank spacer

            // Row 4: Column Headers
            $detailHeaders = ['No', 'Rider Name', 'Customer', 'Job Name', 'Job Date', 'Type Job', 'Status', 'Notes', 'Assign Date', 'Finish Date'];
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
                $typeJob   = isset($typeJobMap[$job['TypeJob']]) ? $typeJobMap[$job['TypeJob']] : ($job['TypeJob'] ?: '-');
                $jobStatus = isset($statusMap[$job['JobStatus']]) ? $statusMap[$job['JobStatus']] : 'Awaiting Driver';

                $sheet2->setCellValue('A' . $rowNum, $no);
                $sheet2->setCellValueExplicit('B' . $rowNum, $job['DriverName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('C' . $rowNum, $job['CustomerName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValueExplicit('D' . $rowNum, $job['JobName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue('E' . $rowNum, !empty($job['JobDate']) ? date('M d, Y H:i', strtotime($job['JobDate'])) : '-');
                $sheet2->setCellValue('F' . $rowNum, $typeJob);
                $sheet2->setCellValue('G' . $rowNum, $jobStatus);
                $sheet2->setCellValueExplicit('H' . $rowNum, $job['Notes'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet2->setCellValue('I' . $rowNum, !empty($job['AssignWhen']) ? date('M d, Y H:i', strtotime($job['AssignWhen'])) : '-');
                $sheet2->setCellValue('J' . $rowNum, !empty($job['FinishWhen']) ? date('M d, Y H:i', strtotime($job['FinishWhen'])) : '-');

                // Apply borders and alignment
                $sheet2->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray($dataBorderStyle);
                $sheet2->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("F{$rowNum}")->applyFromArray($centerAlign);
                $sheet2->getStyle("G{$rowNum}")->applyFromArray($centerAlign);

                // Color-code status
                if ($jobStatus === 'Finished') {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($activeStatusStyle);
                } elseif ($jobStatus === 'Ongoing') {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($warningStatusStyle);
                } else {
                    $sheet2->getStyle("G{$rowNum}")->applyFromArray($inactiveStatusStyle);
                }

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet2->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
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
            $sheet2->getColumnDimension('I')->setWidth(20);
            $sheet2->getColumnDimension('J')->setWidth(20);

            // ============================
            // SHEET 3: Cancelled Jobs
            // ============================
            $objPHPExcel->createSheet();
            $sheet3 = $objPHPExcel->setActiveSheetIndex(2);
            $sheet3->setTitle('Cancelled Jobs');

            // Row 1: Sheet Title
            $sheet3->mergeCells('A1:G1');
            $sheet3->setCellValue('A1', 'Cancelled Jobs - Rider Report');
            $sheet3->getStyle('A1:G1')->applyFromArray($titleStyle);
            $sheet3->getRowDimension(1)->setRowHeight(30);

            // Row 2: Generated timestamp
            $sheet3->mergeCells('A2:G2');
            $sheet3->setCellValue('A2', 'Generated: ' . date('F d, Y - h:i A'));
            $sheet3->getStyle('A2:G2')->applyFromArray($subtitleStyle);

            // Row 3: blank spacer

            // Row 4: Column Headers
            $cancelHeaders = ['No', 'Rider Name', 'Job Name', 'Job Date', 'Type Job', 'Reason', 'Cancel Date'];
            $col = 'A';
            foreach ($cancelHeaders as $header) {
                $sheet3->setCellValue($col . '4', $header);
                $col++;
            }
            $sheet3->getStyle('A4:G4')->applyFromArray($headerStyle);
            $sheet3->getRowDimension(4)->setRowHeight(24);

            // Cancelled jobs data rows
            $rowNum = 5;
            $no = 1;
            foreach ($cancelDetailData as $job) {
                $typeJob = isset($typeJobMap[$job['TypeJob']]) ? $typeJobMap[$job['TypeJob']] : ($job['TypeJob'] ?: '-');

                $sheet3->setCellValue('A' . $rowNum, $no);
                $sheet3->setCellValueExplicit('B' . $rowNum, $job['DriverName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet3->setCellValueExplicit('C' . $rowNum, $job['JobName'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet3->setCellValue('D' . $rowNum, !empty($job['JobDate']) ? date('M d, Y H:i', strtotime($job['JobDate'])) : '-');
                $sheet3->setCellValue('E' . $rowNum, $typeJob);
                $sheet3->setCellValueExplicit('F' . $rowNum, $job['Reason'] ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet3->setCellValue('G' . $rowNum, !empty($job['CancelDate']) ? date('M d, Y H:i', strtotime($job['CancelDate'])) : '-');

                // Apply borders and alignment
                $sheet3->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($dataBorderStyle);
                $sheet3->getStyle("A{$rowNum}")->applyFromArray($centerAlign);
                $sheet3->getStyle("E{$rowNum}")->applyFromArray($centerAlign);

                // Alternate row shading
                if ($no % 2 === 0) {
                    $sheet3->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray([
                        'fill' => ['type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
                    ]);
                }

                $no++;
                $rowNum++;
            }

            // Column widths for Sheet 3
            $sheet3->getColumnDimension('A')->setWidth(6);
            $sheet3->getColumnDimension('B')->setAutoSize(true);
            $sheet3->getColumnDimension('C')->setAutoSize(true);
            $sheet3->getColumnDimension('D')->setWidth(20);
            $sheet3->getColumnDimension('E')->setWidth(16);
            $sheet3->getColumnDimension('F')->setWidth(30);
            $sheet3->getColumnDimension('G')->setWidth(20);

            // Set Sheet 1 as active when file is opened
            $objPHPExcel->setActiveSheetIndex(0);

            // === Save file ===
            $saveDir = FCPATH . 'assets/dist/excel/';
            if (!is_dir($saveDir)) {
                if (!mkdir($saveDir, 0777, true)) {
                    throw new Exception('Failed to create directory: ' . $saveDir);
                }
            }

            $fileName = 'Rider_Report_' . date('Ymd_His') . '.xlsx';
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