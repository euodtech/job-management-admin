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
        $data['title'] = "Efms | Report Customer";

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
                TIMESTAMPDIFF(DAY, MIN(lj.JobDate), MAX(lj.JobDate)) AS RetentionDays,
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
            $row['Action'] = '<button class="btn btn-sm btn-primary btn-detail" style="white-space: nowrap;" data-id="' . $row['CustomerID'] . '">
                    <i class="fas fa-eye"></i> Detail
                </button>';
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
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        // FILTER 
        $customerID = $this->input->get('customerIDCustomerRetentionReport');
        $totalJob = intval($this->input->get('totalJobCustomerRetentionReport'));
        $from = $this->input->get('fromCustomerRetentionReport');
        $until = $this->input->get('untilCustomerRetentionReport');
        $retentionDays = intval($this->input->get('retentionDaysCustomerRetentionReport'));
        $status = $this->input->get('statusCustomerRetentionReport');

        $fileName = $this->input->get('filename') ?: 'Customer_Retention_Report_' . date('m_d_Y');
        $fileName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $fileName);

        $sql = "
            SELECT 
                c.CustomerID,
                c.CustomerName,
                lc.CompanyName,
                COUNT(DISTINCT lj.JobID) AS TotalJob,
                COALESCE(MIN(lj.JobDate), '-') AS FirstJob,
                COALESCE(MAX(lj.JobDate), '-') AS LastJob,
                DATEDIFF(MAX(lj.JobDate), MIN(lj.JobDate)) AS RetentionDays,
                CASE 
                    WHEN COUNT(DISTINCT lj.JobID) > 5 THEN 'Loyal'
                    ELSE 'Normal'
                END AS Status
            FROM Customer c
            LEFT JOIN ListJob lj ON c.CustomerID = lj.CustomerID
            LEFT JOIN ListCompany lc ON lj.CompanyID = lc.ListCompanyID
            WHERE 1=1
        ";

        if ($role != 1 && !empty($companyID)) {
            $sql .= " AND lj.CompanyID = " . $this->db->escape($companyID);
        }

        // === Filter tambahan dari form ===
        if (!empty($customerID)) {
            $sql .= " AND c.CustomerID = " . $this->db->escape($customerID);
        }

        if (!empty($from) && !empty($until)) {
            $sql .= " AND lj.JobDate BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);
        }
        
        $having = [];

        if (!empty($totalJob)) {
            $having[] = "COUNT(DISTINCT lj.JobID) >= " . intval($totalJob);
        }

        if (!empty($retentionDays)) {
            $having[] = "DATEDIFF(MAX(lj.JobDate), MIN(lj.JobDate)) >= " . intval($retentionDays);
        }
        
        
        if (!empty($status)) {
            if ($status == 'Loyal') {
                $sql .= " HAVING COUNT(DISTINCT lj.JobID) > 5";
            } elseif ($status == 'Normal') {
                $sql .= " HAVING COUNT(DISTINCT lj.JobID) <= 5";
            }
        }

        $sql .= "
            GROUP BY c.CustomerID, c.CustomerName, lc.CompanyName
        ";

        if (count($having) > 0) {
            $sql .= " HAVING " . implode(" AND ", $having);
        }

        $sql .= " ORDER BY TotalJob DESC";

        $query = $this->db->query($sql);
        $data = $query->result_array();

        // === Header Excel ===
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$fileName}.xls\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // === Tabel utama ===
        echo "<table border='1' cellspacing='0' cellpadding='8' style='border-collapse:collapse; font-family:Arial; font-size:26px;'>";
        echo "<thead style='background-color:#d9ead3; font-weight:bold; font-size:28px;'>
                <tr>
                    <th>No</th>
                    <th>Customer Name</th>
                    <th>Company Name</th>
                    <th>Total Job</th>
                    <th>First Job</th>
                    <th>Last Job</th>
                    <th>Retention Days</th>
                    <th>Status</th>
                </tr>
            </thead><tbody>";

        $no = 1;
        foreach ($data as $row) {
            echo "<tr>
                    <td style='text-align:center;'>$no</td>
                    <td style='mso-number-format:\"\\@\";'>{$row['CustomerName']}</td>
                    <td>{$row['CompanyName']}</td>
                    <td style='text-align:center;'>{$row['TotalJob']}</td>
                    <td>{$row['FirstJob']}</td>
                    <td>{$row['LastJob']}</td>
                    <td style='text-align:center;'>{$row['RetentionDays']}</td>
                    <td>{$row['Status']}</td>
                </tr>";

            $where = "WHERE lj.CustomerID = " . $this->db->escape($row['CustomerID']);
            if ($role != 1 && !empty($companyID)) {
                $where .= " AND lj.CompanyID = " . $this->db->escape($companyID);
            }

            if (!empty($from) && !empty($until)) {
                $where .= " AND lj.JobDate BETWEEN " . $this->db->escape($from) . " AND " . $this->db->escape($until);
            }

            
            // === Detail job ===
            $detailQuery = $this->db->query("
                SELECT 
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
                LEFT JOIN ListCompany lc ON lj.CompanyID = lc.ListCompanyID
                LEFT JOIN (
                    SELECT JobID, MAX(Reason) AS Reason
                    FROM HistoryCancelJob
                    GROUP BY JobID
                ) h ON lj.JobID = h.JobID
                $where
                GROUP BY lj.JobID, lj.JobName, lj.JobDate, lj.TypeJob, lj.Status, lj.Notes, lu.Fullname, lc.CompanyName, h.Reason
                ORDER BY lj.JobDate DESC
            ");

            $details = $detailQuery->result_array();

            if (count($details) > 0) {
                echo "<tr><td colspan='8' style='background:#f3f3f3; font-weight:bold;'>Job Details for {$row['CustomerName']}</td></tr>";
                echo "<tr style='background-color:#e6f7ff; font-weight:bold;'>
                        <td></td>
                        <td>Job Name</td>
                        <td>Job Date</td>
                        <td>Type Job</td>
                        <td>Status</td>
                        <td>Notes</td>
                        <td>Handled By</td>
                        <td>Cancel Reason</td>
                    </tr>";

                foreach ($details as $job) {
                    // Ganti tipe job manual (tanpa match)
                    $typeJob = '-';
                    if ($job['TypeJob'] == 1) $typeJob = 'Line Interrupt';
                    elseif ($job['TypeJob'] == 2) $typeJob = 'Reconnection';
                    elseif ($job['TypeJob'] == 3) $typeJob = 'Short Circuit';

                    // Status job
                    $jobStatus = '-';
                    if ($job['JobStatus'] == 1) $jobStatus = 'Ongoing';
                    elseif ($job['JobStatus'] == 2) $jobStatus = 'Finished Job';

                    echo "<tr>
                            <td></td>
                            <td>{$job['JobName']}</td>
                            <td>{$job['JobDate']}</td>
                            <td>{$typeJob}</td>
                            <td>{$jobStatus}</td>
                            <td>{$job['Notes']}</td>
                            <td>{$job['HandledBy']}</td>
                            <td>{$job['CancelReason']}</td>
                        </tr>";
                }
            }

            echo "<tr><td colspan='8' style='height:30px; background:white;'></td></tr>";

            $no++;
        }

        echo "</tbody></table>";
        exit;
    }

}