<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job extends MY_Controller
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

    public function historyCancelJob()
    {
        $jobID = $this->input->get('jobID');

        $data['history'] = $this->M_Global->globalquery("SELECT HistoryCancelJob.*, ListUser.Fullname  FROM HistoryCancelJob LEFT JOIN ListUser ON HistoryCancelJob.UserBefore = ListUser.UserID WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC ")->result_array();

        $this->load->view('main/job/page_history_cancel_job',$data);

    }

    public function historyReschedule()
    {
        $jobID = $this->input->get('jobID');

        $data['history'] = $this->M_Global->globalquery("SELECT RescheduledJob.* FROM RescheduledJob  WHERE JobID = '$jobID' ORDER BY RescheduledJob.created_at ASC ")->result_array();

        $this->load->view('main/job/page_history_reschedule_job',$data);

    }

    public function index($type_job = 1)
    {
        $data['title'] = "Efms | Job";

        $companyID = $companyID = $this->session->userdata('CompanyID');

        $data['customer'] = $this->M_Global->globalquery("SELECT * FROM  Customer WHERE ListCompanyID = '$companyID' ORDER BY created_at DESC ")->result_array();

        $data['type_job'] = $type_job;

        switch ($type_job) {
            case '1':
                $label_job = "Line Interrupt";
                break;
            case '2':
                $label_job = "Reconnection";
                break;
            case '3':
                $label_job = "Short Circuit";
                break;
            case '4':
                $label_job = "Disconnection";
                break;
            
            default:
                $label_job = "Line Interrupt";
                break;
        }

        $data['label_job'] = $label_job;

        $this->render_page('main/job/page_job',$data); 
    }

    public function getDataJobForCard($type_job = 1)
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $additional_where_job = " WHERE TypeJob =  " . $type_job . " AND (Status IS NULL OR Status = 1 OR Status = 3)";

        if($role != 1) {
            $additional_where_job .= " AND ListJob.CompanyID = " . $companyID;
        }

        $listJob = $this->M_Global->globalquery("
            SELECT 
                ListJob.*
            FROM ListJob 
             $additional_where_job
            ORDER BY ListJob.created_at DESC
        ")->result_array();

        $todayJob = 0;
        $ongoingJob = 0;
        $upComingJob = 0;
        $rescheduleJob = 0;
        $completedJob = 0;
        $dNow = date('Y-m-d');

        foreach ($listJob as $val) {
            // Ambil hanya tanggal dari JobDate (datetime)
            $jobDate = date('Y-m-d', strtotime($val['JobDate']));

            // === PEKERJAAN HARI INI ===
            if ($jobDate == $dNow) {

                $todayJob++;
            }
            // === PEKERJAAN YANG AKAN DATANG ===
            else if ($jobDate > $dNow && $val['Status'] == null) {
                $upComingJob++;
            }

            if($val['Status'] == 1) {
                $ongoingJob++;
            } elseif($val['Status'] == 2) {
                $completedJob++;
            } elseif($val['Status'] == 3) {
                $rescheduleJob++;
            } 
        }

        $return = [
            "todalJob" => $todayJob,
            "ongoingJob" => $ongoingJob,
            "completedJob" => $completedJob,
            "onComingJob" => $upComingJob,
            "rescheduleJob" => $rescheduleJob
        ];

        echo json_encode($return);
    }

    public function getDataAllJob($type_job = 1)
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';

        $columns = [
            0 => 'JobID',
            1 => 'JobDate',
            2 => 'JobName',
            3 => 'CustomerName',
            4 => 'Address',
            5 => 'Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';



        $sql = "
            SELECT 
                ListJob.*, 
                Customer.*, 
                ListUser.Fullname 
            FROM ListJob 
            LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID 
            LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID 
        ";

        // WHERE $additional_where_job
        //  ORDER BY ListJob.JobDate ASC

        $where = [];

        $where[] = " (ListJob.Status IS NULL OR ListJob.Status = 1 OR ListJob.Status = 3) ";

        // if (empty($fromDate)) {
        //     $where[] = " DATE(ListJob.JobDate) >= CURRENT_DATE()  ";
        // }

        if($role != 1) {
            $where[] = "ListJob.CompanyID = " . $companyID;
        }

        if($type_job != null) {
            $where[] = "ListJob.TypeJob = " . $type_job;
        }
        
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $where[] = "(
                ListUser.Fullname LIKE '%{$searchValueEscaped}%' OR
                Customer.Address LIKE '%{$searchValueEscaped}%' OR
                Customer.CustomerName LIKE '%{$searchValueEscaped}%' OR
                ListJob.JobDate LIKE '%{$searchValueEscaped}%' OR
                ListJob.JobName LIKE '%{$searchValueEscaped}%'
            )";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $totalQuery = $this->M_Global->globalquery($sql)->result_array();
        $recordsFiltered = count($totalQuery);

        $sql .= " ORDER BY $orderBy $orderDir LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $jobID = $row['JobID'];

            $cancelJob = $this->M_Global->globalquery("SELECT HistoryCancelJobID FROM HistoryCancelJob WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC ")->result_array();

            $data[] = [
                "no" => $no++,
                "JobDate" => return_date_format($row['JobDate']),
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "Address" => $row['Address'],
                "Fullname" => $row['Fullname'],
                "TypeJob" => $row['TypeJob'],
                "Status" => $row['Status'],
                "JobID" => $jobID,
                "StatusCancelJob" => $cancelJob
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function getDetailPhoto()
    {
        $jobID = $this->input->post('jobID');

        $data = $this->M_Global->globalquery("SELECT * FROM ListJobDetail WHERE ListJobID = '$jobID' ")->result_array();

        echo json_encode($data);
    }

    public function create()
    {

        $companyID = $this->session->userdata('CompanyID');
        $formatPhoneNumber = "+63" . $this->input->post('phone_number');

        $selected_company = ($this->session->userdata("Role") != 1) ? $companyID : $this->input->post('company_selected');

        $data_create_customer = [
            "CustomerName" => $this->input->post('customer_name'),
            "CustomerEmail" => $this->input->post('customer_email'),
            "Latitude" => $this->input->post('latitude'),
            "ListCompanyID" => $selected_company,
            "Longitude" => $this->input->post('longitude'),
            "PhoneNumber" => $formatPhoneNumber,
            "Address" => $this->input->post('address'),
            "created_at" => date('Y-m-d H:i:s')
        ];

        $create_customer = $this->M_Global->insertid($data_create_customer, "Customer");

        $data_create = [
            "JobName" => $this->input->post('job_name'),
            "CustomerID" => $create_customer,
            "CompanyID" => $this->session->userdata('CompanyID'),
            "TypeJob" => $this->input->post('type_job_input'),
            "JobDate" => $this->input->post('job_date'),
            "CreatedBy" => $this->session->userdata('AdminID'),
            "created_at" => date('Y-m-d H:i:s')
        ];

        // create user
        $job = $this->M_Global->insert($data_create, "ListJob");

        if ($job == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job created successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Job!</div>');
        }

        switch ($data_create['TypeJob']) {
            case '1':
                $redirect = "line-interruption-job";
                break;
            case '2':
                $redirect = "reconnection-job";
                break;
            case '3':
                $redirect = "short-circuit-job";
                break;
            case '4':
                $redirect = "disconnection-job";
                break;
            
            default:
                $redirect = "job-list";
                break;
        }

        redirect(base_url($redirect));
    }
    public function update()
    {
        $jobID = $this->input->post('job_id');

        $customerID = $this->input->post('customer_id');

        $companyID = $this->session->userdata('CompanyID');
        $formatPhoneNumber = "+63" . $this->input->post('phone_number');

        $selected_company = ($this->session->userdata("Role") != 1) ? $companyID : $this->input->post('company_selected');

        $data_update_customer = [
            "CustomerName" => $this->input->post('customer_name'),
            "CustomerEmail" => $this->input->post('customer_email'),
            "Latitude" => $this->input->post('latitude'),
            "ListCompanyID" => $selected_company,
            "Longitude" => $this->input->post('longitude'),
            "PhoneNumber" => $formatPhoneNumber,
            "Address" => $this->input->post('address'),
            "created_at" => date('Y-m-d H:i:s')
        ];

        $where_customer = " CustomerID =  '$customerID' ";
        // create user
        $customer = $this->M_Global->update_data($where_customer, $data_update_customer, "Customer");

        
        $dara_update = [
            "JobName" => $this->input->post('job_name'),
            "CustomerID" => $this->input->post('customer_id'),
            "CreatedBy" => $this->session->userdata('AdminID'),
            "TypeJob" => $this->input->post('type_job_input'),
            "JobDate" => $this->input->post('job_date'),
        ];



        $where = " JobID =  '$jobID' ";
        // create user
        $job = $this->M_Global->update_data($where, $dara_update, "ListJob");

        if ($job == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job updated successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to updated Job!</div>');
        }

        redirect(base_url('job-list'));
    }

    public function delete()
    {
        $jobID = $this->input->post('job_id');

        $delete = $this->M_Global->delete('ListJob' , "JobID = '$jobID' ");

        if ($delete == "success") {
            $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job delete successfully</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to delete Job!</div>');
        }

        redirect(base_url('job-list'));
    }

    public function getJobDetail()
    {
        $jobID = $this->input->post('jobID');

        $dataReturn = $this->M_Global->globalquery("SELECT ListJob.*, Customer.*, Customer.PhoneNumber as CustPhoneNumber, ListUser.*, RescheduledJob.* FROM ListJob LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID LEFT JOIN RescheduledJob ON ListJob.JobID = RescheduledJob.JobID WHERE ListJob.JobID  = '$jobID' AND RescheduledJob.StatusApproved = 2 ")->row_array();

        echo json_encode($dataReturn);
    }

    public function reschedule_job()
    {
        $data['title'] = "Efms | Job";

        $companyID = $companyID = $this->session->userdata('CompanyID');

        $this->render_page('main/job/page_reschedule_job',$data);

    }

    public function summary($type_job = 1)
    {
        $data['title'] = "Efms | Job";

        $companyID = $companyID = $this->session->userdata('CompanyID');

        $data['customer'] = $this->M_Global->globalquery("SELECT * FROM  Customer WHERE ListCompanyID = '$companyID' ")->result_array();

        $data['type_job'] = $type_job;
        $this->render_page('main/job/page_job_summary',$data); 
    }

    public function getDataAllJobCustomer()
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';
        $customerID = $this->input->get('customerID');
        $dateFrom = $this->input->get('dateFrom');
        $dateUntil = $this->input->get('dateUntil');

        $columns = [
            0 => 'JobID',
            1 => 'JobDate',
            2 => 'JobName',
            3 => 'CustomerName',
            4 => 'Address',
            5 => 'Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';



        $sql = "
            SELECT 
                ListJob.*, 
                Customer.*, 
                ListUser.Fullname 
            FROM ListJob 
            LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID 
            LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID 
           
        ";

        $where = [];

        $where[] = " ListJob.Status = 2 ";

        if (!empty($dateFrom)) {
            $where[] = " DATE(ListJob.JobDate) >= '$dateFrom' AND DATE(ListJob.JobDate) <= '$dateUntil' ";
        }

        if($role != 1) {
            $where[] = "ListJob.CompanyID = " . $companyID;
        }

        if($customerID != 'all') {
            $where[] = "ListJob.CustomerID =  " . $customerID;
        }
        
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $where[] = "(
                ListUser.Fullname LIKE '%$searchValueEscaped%' OR
                Customer.CustomerName LIKE '%$searchValueEscaped%' OR
                ListJob.JobName LIKE '%$searchValueEscaped%'
            )";
        }

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

        $sql .= " ORDER BY $orderBy $orderDir LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $jobID = $row['JobID'];

            $cancelJob = $this->M_Global->globalquery("SELECT HistoryCancelJobID FROM HistoryCancelJob WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC ")->result_array();

            $rescheduleJob = $this->M_Global->globalquery("SELECT RescheduledID FROM RescheduledJob WHERE JobID = '$jobID' ")->result_array();

            $data[] = [
                "no" => $no++,
                "JobDate" => return_date_format($row['JobDate']),
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "Address" => $row['Address'],
                "Fullname" => $row['Fullname'],
                "TypeJob" => $row['TypeJob'],
                "Status" => $row['Status'],
                "JobID" => $jobID,
                "StatusCancelJob" => $cancelJob,
                "StatusReschedule" => $rescheduleJob,
                "AssignWhen" => return_date_format($row['AssignWhen']),
                "FinishWhen" => return_date_format($row['FinishWhen'])
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }


    public function getJobReschedule()
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw   = intval($request['draw']);
        $start  = intval($request['start']);
        $length = intval($request['length']);
        $searchValue = $request['search']['value'] ?? '';
        $dateFrom = $this->input->get('dateFrom');
        $dateUntil = $this->input->get('dateUntil');

        $columns = [
            0 => 'ListJob.JobID',
            1 => 'JobDate',
            2 => 'JobName',
            3 => 'CustomerName',
            4 => 'Address',
            5 => 'Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';



        $sql = "
            SELECT 
                ListJob.*, 
                RescheduledJob.*, 
                Customer.*, 
                ListUser.Fullname 
            FROM RescheduledJob 
            LEFT JOIN ListJob ON RescheduledJob.JobID = ListJob.JobID 
            LEFT JOIN Customer ON ListJob.CustomerID = Customer.CustomerID 
            LEFT JOIN ListUser ON ListJob.UserID = ListUser.UserID 
           
        ";

        $where = [];

        // $where[] = " ListJob.Status = 2 ";

        if (!empty($dateFrom)) {
            $where[] = " DATE(ListJob.JobDate) >= '$dateFrom' AND DATE(ListJob.JobDate) <= '$dateUntil' ";
        }

        if($role != 1) {
            $where[] = "ListJob.CompanyID = " . $companyID;
        }
        
        if (!empty($searchValue)) {
            $searchValueEscaped = $this->db->escape_like_str($searchValue);
            $where[] = "(
                ListUser.Fullname LIKE '%$searchValueEscaped%' OR
                RescheduledJob.Reason LIKE '%$searchValueEscaped%' OR
                ListJob.JobName LIKE '%$searchValueEscaped%'
            )";
        }

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

        $sql .= " ORDER BY $orderBy $orderDir LIMIT $start, $length";
        $query = $this->M_Global->globalquery($sql)->result_array();

        $data = [];
        $no = $start + 1;
        foreach ($query as $row) {

            $jobID = $row['JobID'];

            $cancelJob = $this->M_Global->globalquery("SELECT HistoryCancelJobID FROM HistoryCancelJob WHERE JobID = '$jobID' ORDER BY HistoryCancelJob.created_at ASC ")->result_array();

            $data[] = [
                "no" => $no++,
                "JobDate" => return_date_format($row['JobDate']),
                "RequestDateJob" => return_date_format($row['RescheduledDateJob']),
                "Fullname" => $row['Fullname'],
                "JobName" => $row['JobName'],
                "Reason" => $row['Reason'],
                "StatusApproved" => $row['StatusApproved'],
                "RescheduledID" => $row['RescheduledID'],
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function actionRescheduleJob($type, $reschedule_id = null)
    {

        if($type == "approve")
        {

            $data_reschedule = $this->M_Global->globalquery("SELECT JobID FROM RescheduledJob WHERE RescheduledID = '$reschedule_id' ")->row_array();

            $jobID = $data_reschedule['JobID'];

            $updateStatusApprove = $this->M_Global->update('RescheduledJob', "StatusApproved = 2 WHERE RescheduledID = '$reschedule_id' ");

            $updateStatusJob = $this->M_Global->update('ListJob', "Status = 3 WHERE JobID = '$jobID' ");

            redirect(base_url('reschedule-job'));

        } elseif($type == "reject") {

            $reasonReject = $this->input->post("reason");
            $reschedule_id = $this->input->post("reschedule_id");

            $data_reschedule = $this->M_Global->globalquery("SELECT JobID FROM RescheduledJob WHERE RescheduledID = '$reschedule_id' ")->row_array();

            $jobID = $data_reschedule['JobID'];
            
            
            $updateStatusApprove = $this->M_Global->update('RescheduledJob', "StatusApproved = 3, ReasonReject = '$reasonReject' WHERE RescheduledID = '$reschedule_id' ");

            $dataUpdate = [
                "UserID" => null,
                "Status" => null,
                "AssignWhen" => null
            ];

            $whereJob = "JobID = " . $jobID;

            $updateStatusJob = $this->M_Global->update_data($whereJob, $dataUpdate, "ListJob");

            redirect(base_url('reschedule-job'));
        }

    }

    public function getDataJobForCardJobSummary()
    {
        $role = $this->session->userdata('Role');
        $companyID = $this->session->userdata('CompanyID');
        $dateFrom = $this->input->post('dateFrom');
        $dateUntil = $this->input->post('dateUntil');

        $additional_where_job = " AND 
                DATE(ListJob.JobDate) >= '$dateFrom' AND DATE(ListJob.JobDate) <= '$dateUntil' ";

        if($role != 1) {
            $additional_where_job .= " AND ListJob.CompanyID = " . $companyID;
            $additional_where_customer = " WHERE ListCompanyID =  " . $companyID;
        }

        $listCustomer = $this->M_Global->globalquery("SELECT CustomerID FROM Customer $additional_where_customer ")->result_array();

        $hasil = array_map(function($d) use ($additional_where_job) {

            $customerID = $d['CustomerID'];

            $d['TotalJob'] = $this->M_Global->globalquery("
                SELECT COUNT(ListJob.JobID) AS TotalJob FROM ListJob WHERE CustomerID = $customerID $additional_where_job ORDER BY TotalJob DESC
            ")->row_array()['TotalJob'];

            return $d;
        }, $listCustomer);

        echo json_encode($hasil);
    }

}