<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Job extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('status') != "kusam") {
        redirect(base_url('auth'));
        return; // <-- safe tweak
    }

        $this->load->library('form_validation');
        $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->helper('idr_helper');
        $this->load->helper('date_format_helper');
    }

    public function historyCancelJob()
    {
        // Get jobID from GET and ensure it's an integer
        $jobID = (int) $this->input->get('jobID');

        // Use Active Record for safe query
        $this->db->select('HistoryCancelJob.*, ListUser.Fullname');
        $this->db->from('HistoryCancelJob');
        $this->db->join('ListUser', 'HistoryCancelJob.UserBefore = ListUser.UserID', 'left');
        $this->db->where('HistoryCancelJob.JobID', $jobID);
        $this->db->order_by('HistoryCancelJob.created_at', 'ASC');
        
        $data['history'] = $this->db->get()->result_array();

        // Load the view with the result
        $this->load->view('main/job/page_history_cancel_job', $data);
    }


    public function historyReschedule()
    {
        // Get jobID from GET and cast to integer
        $jobID = (int) $this->input->get('jobID');

        // Safe query using Active Record
        $this->db->from('RescheduledJob');
        $this->db->where('JobID', $jobID);
        $this->db->order_by('created_at', 'ASC');
        
        $data['history'] = $this->db->get()->result_array();

        // Load the view
        $this->load->view('main/job/page_history_reschedule_job', $data);
    }


    public function index($type_job = 1)
    {
        $data['title'] = "Efms | Job";

        $companyID = $this->session->userdata('CompanyID');

        $this->db->from('Customer');
        $this->db->where('ListCompanyID', $companyID);
        $this->db->order_by('created_at', 'DESC');
        $data['customer'] = $this->db->get()->result_array();


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

        $type_job = (int) $type_job; // cast to int to prevent injection

        $this->db->from('ListJob');
        $this->db->where('TypeJob', $type_job);
        $this->db->group_start(); // (Status IS NULL OR Status = 1 OR Status = 3)
        $this->db->where('Status', null);
        $this->db->or_where('Status', 1);
        $this->db->or_where('Status', 3);
        $this->db->group_end();

        if ($role != 1) {
            $this->db->where('CompanyID', $companyID);
        }

        $this->db->order_by('created_at', 'DESC');
        $listJob = $this->db->get()->result_array();

        $todayJob = 0;
        $ongoingJob = 0;
        $upComingJob = 0;
        $rescheduleJob = 0;
        $completedJob = 0;
        $dNow = date('Y-m-d');

        foreach ($listJob as $val) {
            $jobDate = date('Y-m-d', strtotime($val['JobDate']));

            if ($jobDate == $dNow) {
                $todayJob++;
            } elseif ($jobDate > $dNow && $val['Status'] === null) {
                $upComingJob++;
            }

            if ($val['Status'] == 1) {
                $ongoingJob++;
            } elseif ($val['Status'] == 2) {
                $completedJob++;
            } elseif ($val['Status'] == 3) {
                $rescheduleJob++;
            } 
        }

        $return = [
            "todayJob" => $todayJob, //corrected. todalJob -> todayJob
            "ongoingJob" => $ongoingJob,
            "completedJob" => $completedJob,
            "onComingJob" => $upComingJob,
            "rescheduleJob" => $rescheduleJob
        ];

        echo json_encode($return);
    }

    public function getDataAllJob($type_job = 1)
    {
        $role      = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');
        $type_job  = (int) $type_job;

        $request = $_REQUEST;
        $draw    = intval($request['draw'] ?? 0);
        $start   = intval($request['start'] ?? 0);
        $length  = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';

        $columns = [
            0 => 'ListJob.JobID',
            1 => 'ListJob.JobDate',
            2 => 'ListJob.JobName',
            3 => 'Customer.CustomerName',
            4 => 'Customer.Address',
            5 => 'ListUser.Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';

        /* ===================== BASE QUERY ===================== */
        $this->db->select('ListJob.*, Customer.*, ListUser.Fullname');
        $this->db->from('ListJob');
        $this->db->join('Customer', 'ListJob.CustomerID = Customer.CustomerID', 'left');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID', 'left');

        // Status condition
        $this->db->group_start();
        $this->db->where('ListJob.Status', null);
        $this->db->or_where('ListJob.Status', 1);
        $this->db->or_where('ListJob.Status', 3);
        $this->db->group_end();

        if ($role !== 1) {
            $this->db->where('ListJob.CompanyID', $companyID);
        }

        if ($type_job !== 0) {
            $this->db->where('ListJob.TypeJob', $type_job);
        }

        if (!empty($searchValue)) {
            $searchValue = $this->db->escape_like_str($searchValue);
            $this->db->group_start();
            $this->db->like('ListUser.Fullname', $searchValue);
            $this->db->or_like('Customer.Address', $searchValue);
            $this->db->or_like('Customer.CustomerName', $searchValue);
            $this->db->or_like('ListJob.JobDate', $searchValue);
            $this->db->or_like('ListJob.JobName', $searchValue);
            $this->db->group_end();
        }

        /* ===================== COUNT ===================== */
        $recordsFiltered = $this->db->count_all_results('', false);

        /* ===================== DATA ===================== */
        $this->db->order_by($orderBy, $orderDir);
        $this->db->limit($length, $start);
        $query = $this->db->get()->result_array();

        $data = [];
        $no = $start + 1;

        foreach ($query as $row) {
            $jobID = (int) $row['JobID'];

            $cancelJob = $this->db
                ->select('HistoryCancelJobID')
                ->from('HistoryCancelJob')
                ->where('JobID', $jobID)
                ->order_by('created_at', 'ASC')
                ->get()
                ->result_array();

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
        // Sanitize input
        $jobID = (int) $this->input->post('jobID');

        // Validate input
        if ($jobID <= 0) {
            echo json_encode([]);
            return;
        }

        // Fetch job photos safely
        $this->db->from('ListJobDetail');
        $this->db->where('ListJobID', $jobID);
        $data = $this->db->get()->result_array();

        // Return JSON
        echo json_encode($data ?: []);
    }


    public function create()
    {
        $companyID = (int) $this->session->userdata('CompanyID');
        $role      = (int) $this->session->userdata('Role');

        // Sanitize & normalize inputs
        $phoneRaw = preg_replace('/[^0-9]/', '', $this->input->post('phone_number'));
        $formatPhoneNumber = '+63' . $phoneRaw;

        $selected_company = ($role !== 1)
            ? $companyID
            : (int) $this->input->post('company_selected');

        $data_create_customer = [
            "CustomerName"   => trim($this->input->post('customer_name')),
            "CustomerEmail"  => trim($this->input->post('customer_email')),
            "Latitude"       => $this->input->post('latitude'),
            "Longitude"      => $this->input->post('longitude'),
            "ListCompanyID"  => $selected_company,
            "PhoneNumber"    => $formatPhoneNumber,
            "Address"        => trim($this->input->post('address')),
            "created_at"     => date('Y-m-d H:i:s')
        ];

        // Insert customer (uses CI query builder internally)
        $create_customer = (int) $this->M_Global->insertid($data_create_customer, "Customer");

        $data_create = [
            "JobName"    => trim($this->input->post('job_name')),
            "CustomerID"=> $create_customer,
            "CompanyID" => $companyID,
            "TypeJob"   => (int) $this->input->post('type_job_input'),
            "JobDate"   => $this->input->post('job_date'),
            "CreatedBy"=> (int) $this->session->userdata('AdminID'),
            "created_at"=> date('Y-m-d H:i:s')
        ];

        $job = $this->M_Global->insert($data_create, "ListJob");

        if ($job === "success") {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job created successfully</div>'
            );
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to create Job!</div>'
            );
        }

        switch ((string) $data_create['TypeJob']) {
            case '1': $redirect = "line-interruption-job"; break;
            case '2': $redirect = "reconnection-job"; break;
            case '3': $redirect = "short-circuit-job"; break;
            case '4': $redirect = "disconnection-job"; break;
            default:  $redirect = "job-list"; break;
        }

        redirect(base_url($redirect));
    }

    public function update()
    {
        $jobID      = (int) $this->input->post('job_id');
        $customerID = (int) $this->input->post('customer_id');

        $companyID = (int) $this->session->userdata('CompanyID');
        $role      = (int) $this->session->userdata('Role');

        // Sanitize phone number
        $phoneRaw = preg_replace('/[^0-9]/', '', $this->input->post('phone_number'));
        $formatPhoneNumber = '+63' . $phoneRaw;

        $selected_company = ($role !== 1)
            ? $companyID
            : (int) $this->input->post('company_selected');

        /* ================= CUSTOMER UPDATE ================= */
        $data_update_customer = [
            "CustomerName"  => trim($this->input->post('customer_name')),
            "CustomerEmail" => trim($this->input->post('customer_email')),
            "Latitude"      => $this->input->post('latitude'),
            "Longitude"     => $this->input->post('longitude'),
            "ListCompanyID" => $selected_company,
            "PhoneNumber"   => $formatPhoneNumber,
            "Address"       => trim($this->input->post('address')),
            "created_at"    => date('Y-m-d H:i:s')
        ];

        // SAFE where clause (no raw input)
        $where_customer = "CustomerID = {$customerID}";
        $customer = $this->M_Global->update_data($where_customer, $data_update_customer, "Customer");

        /* ================= JOB UPDATE ================= */
        $data_update = [
            "JobName"    => trim($this->input->post('job_name')),
            "CustomerID"=> $customerID,
            "CreatedBy"=> (int) $this->session->userdata('AdminID'),
            "TypeJob"   => (int) $this->input->post('type_job_input'),
            "JobDate"   => $this->input->post('job_date'),
        ];

        $where_job = "JobID = {$jobID}";
        $job = $this->M_Global->update_data($where_job, $data_update, "ListJob");

        if ($job === "success") {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job updated successfully</div>'
            );
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to updated Job!</div>'
            );
        }

        redirect(base_url('job-list'));
    }


    public function delete()
    {
        // Sanitize input
        $jobID = (int) $this->input->post('job_id');

        // Validate input
        if ($jobID <= 0) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Invalid job ID!</div>'
            );
            redirect(base_url('job-list'));
            return;
        }

        // Delete job safely
        $delete = $this->M_Global->delete('ListJob', "JobID = {$jobID}");

        if ($delete === "success") {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Job deleted successfully</div>'
            );
        } else {
            // Log error for debugging
            log_message('error', "Failed to delete job with ID {$jobID}");

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Failed to delete job!</div>'
            );
        }

        redirect(base_url('job-list'));
    }


    public function getJobDetail()
    {
        $jobID = (int) $this->input->post('jobID');

        $this->db->select('
            ListJob.*,
            Customer.*,
            Customer.PhoneNumber AS CustPhoneNumber,
            ListUser.*,
            RescheduledJob.*
        ');
        $this->db->from('ListJob');
        $this->db->join('Customer', 'ListJob.CustomerID = Customer.CustomerID', 'left');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID', 'left');
        $this->db->join('RescheduledJob', 'ListJob.JobID = RescheduledJob.JobID', 'left');
        $this->db->where('ListJob.JobID', $jobID);
        $this->db->where('RescheduledJob.StatusApproved', 2);

        $dataReturn = $this->db->get()->row_array();

        echo json_encode($dataReturn);
    }

    public function reschedule_job()
    {
        $data['title'] = "Efms | Job";

        $companyID = $this->session->userdata('CompanyID');

        $this->render_page('main/job/page_reschedule_job', $data);
    }

    public function summary($type_job = 1)
    {
        $data['title'] = "Efms | Job";

        $companyID = (int) $this->session->userdata('CompanyID');
        $type_job  = (int) $type_job;

        $this->db->from('Customer');
        $this->db->where('ListCompanyID', $companyID);
        $data['customer'] = $this->db->get()->result_array();

        $data['type_job'] = $type_job;

        $this->render_page('main/job/page_job_summary', $data);
    }


    public function getDataAllJobCustomer()
    {
        $role      = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw    = intval($request['draw'] ?? 0);
        $start   = intval($request['start'] ?? 0);
        $length  = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';

        $customerID = $this->input->get('customerID');
        $dateFrom   = $this->input->get('dateFrom');
        $dateUntil  = $this->input->get('dateUntil');

        $customerID = ($customerID !== 'all') ? (int) $customerID : 'all';

        $columns = [
            0 => 'ListJob.JobID',
            1 => 'ListJob.JobDate',
            2 => 'ListJob.JobName',
            3 => 'Customer.CustomerName',
            4 => 'Customer.Address',
            5 => 'ListUser.Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';

        /* ===================== BASE QUERY ===================== */
        $this->db->select('ListJob.*, Customer.*, ListUser.Fullname');
        $this->db->from('ListJob');
        $this->db->join('Customer', 'ListJob.CustomerID = Customer.CustomerID', 'left');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID', 'left');

        // Completed jobs only
        $this->db->where('ListJob.Status', 2);

        if (!empty($dateFrom) && !empty($dateUntil)) {
            $this->db->where('DATE(ListJob.JobDate) >=', $dateFrom);
            $this->db->where('DATE(ListJob.JobDate) <=', $dateUntil);
        }

        if ($role !== 1) {
            $this->db->where('ListJob.CompanyID', $companyID);
        }

        if ($customerID !== 'all') {
            $this->db->where('ListJob.CustomerID', $customerID);
        }

        if (!empty($searchValue)) {
            $searchValue = $this->db->escape_like_str($searchValue);
            $this->db->group_start();
            $this->db->like('ListUser.Fullname', $searchValue);
            $this->db->or_like('Customer.CustomerName', $searchValue);
            $this->db->or_like('ListJob.JobName', $searchValue);
            $this->db->group_end();
        }

        /* ===================== COUNT ===================== */
        $recordsFiltered = $this->db->count_all_results('', false);

        /* ===================== DATA ===================== */
        $this->db->order_by($orderBy, $orderDir);
        $this->db->limit($length, $start);
        $query = $this->db->get()->result_array();

        $data = [];
        $no = $start + 1;

        foreach ($query as $row) {
            $jobID = (int) $row['JobID'];

            $cancelJob = $this->db
                ->select('HistoryCancelJobID')
                ->from('HistoryCancelJob')
                ->where('JobID', $jobID)
                ->order_by('created_at', 'ASC')
                ->get()
                ->result_array();

            $rescheduleJob = $this->db
                ->select('RescheduledID')
                ->from('RescheduledJob')
                ->where('JobID', $jobID)
                ->get()
                ->result_array();

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
        $role      = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');

        $request = $_REQUEST;
        $draw    = intval($request['draw'] ?? 0);
        $start   = intval($request['start'] ?? 0);
        $length  = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';

        $dateFrom  = $this->input->get('dateFrom');
        $dateUntil = $this->input->get('dateUntil');

        $columns = [
            0 => 'ListJob.JobID',
            1 => 'ListJob.JobDate',
            2 => 'ListJob.JobName',
            3 => 'Customer.CustomerName',
            4 => 'Customer.Address',
            5 => 'ListUser.Fullname',
        ];

        $orderColIndex = isset($request['order'][0]['column']) ? (int)$request['order'][0]['column'] : 1;
        $orderDir = isset($request['order'][0]['dir']) && in_array(strtoupper($request['order'][0]['dir']), ['ASC', 'DESC'])
            ? strtoupper($request['order'][0]['dir'])
            : 'DESC';

        $orderBy = $columns[$orderColIndex] ?? 'ListJob.JobDate';

        /* ===================== BASE QUERY ===================== */
        $this->db->select('ListJob.*, RescheduledJob.*, Customer.*, ListUser.Fullname');
        $this->db->from('RescheduledJob');
        $this->db->join('ListJob', 'RescheduledJob.JobID = ListJob.JobID', 'left');
        $this->db->join('Customer', 'ListJob.CustomerID = Customer.CustomerID', 'left');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID', 'left');

        if (!empty($dateFrom) && !empty($dateUntil)) {
            $this->db->where('DATE(ListJob.JobDate) >=', $dateFrom);
            $this->db->where('DATE(ListJob.JobDate) <=', $dateUntil);
        }

        if ($role !== 1) {
            $this->db->where('ListJob.CompanyID', $companyID);
        }

        if (!empty($searchValue)) {
            $searchValue = $this->db->escape_like_str($searchValue);
            $this->db->group_start();
            $this->db->like('ListUser.Fullname', $searchValue);
            $this->db->or_like('RescheduledJob.Reason', $searchValue);
            $this->db->or_like('ListJob.JobName', $searchValue);
            $this->db->group_end();
        }

        /* ===================== COUNT ===================== */
        $recordsFiltered = $this->db->count_all_results('', false);

        /* ===================== DATA ===================== */
        $this->db->order_by($orderBy, $orderDir);
        $this->db->limit($length, $start);
        $query = $this->db->get()->result_array();

        $data = [];
        $no = $start + 1;

        foreach ($query as $row) {
            $jobID = (int) $row['JobID'];

            // kept to avoid logic break (even if unused in response)
            $cancelJob = $this->db
                ->select('HistoryCancelJobID')
                ->from('HistoryCancelJob')
                ->where('JobID', $jobID)
                ->order_by('created_at', 'ASC')
                ->get()
                ->result_array();

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
        if ($type === "approve") {

            $reschedule_id = (int) $reschedule_id;

            $data_reschedule = $this->db
                ->select('JobID')
                ->from('RescheduledJob')
                ->where('RescheduledID', $reschedule_id)
                ->get()
                ->row_array();

            if (empty($data_reschedule)) {
                redirect(base_url('reschedule-job'));
                return;
            }

            $jobID = (int) $data_reschedule['JobID'];

            $this->db->where('RescheduledID', $reschedule_id)
                    ->update('RescheduledJob', ['StatusApproved' => 2]);

            $this->db->where('JobID', $jobID)
                    ->update('ListJob', ['Status' => 3]);

            redirect(base_url('reschedule-job'));

        } elseif ($type === "reject") {

            $reschedule_id = (int) $this->input->post('reschedule_id');
            $reasonReject  = trim($this->input->post('reason', true));

            $data_reschedule = $this->db
                ->select('JobID')
                ->from('RescheduledJob')
                ->where('RescheduledID', $reschedule_id)
                ->get()
                ->row_array();

            if (empty($data_reschedule)) {
                redirect(base_url('reschedule-job'));
                return;
            }

            $jobID = (int) $data_reschedule['JobID'];

            $this->db->where('RescheduledID', $reschedule_id)
                    ->update('RescheduledJob', [
                        'StatusApproved' => 3,
                        'ReasonReject'   => $reasonReject
                    ]);

            $this->db->where('JobID', $jobID)
                    ->update('ListJob', [
                        'UserID'     => null,
                        'Status'     => null,
                        'AssignWhen' => null
                    ]);

            redirect(base_url('reschedule-job'));
        }
    }


    public function getDataJobForCardJobSummary()
    {
        $role      = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');
        $dateFrom  = $this->input->post('dateFrom', true);
        $dateUntil = $this->input->post('dateUntil', true);

        $additional_where_job = [];
        if (!empty($dateFrom) && !empty($dateUntil)) {
            $additional_where_job[] = "DATE(ListJob.JobDate) >= " . $this->db->escape($dateFrom);
            $additional_where_job[] = "DATE(ListJob.JobDate) <= " . $this->db->escape($dateUntil);
        }

        $additional_where_customer = " WHERE 1=1 ";
        if ($role !== 1) {
            $additional_where_job[] = "ListJob.CompanyID = " . $companyID;
            $additional_where_customer .= " AND ListCompanyID = " . $companyID;
        }

        // Fetch customers
        $listCustomer = $this->M_Global->globalquery("SELECT CustomerID FROM Customer $additional_where_customer")->result_array();

        $hasil = array_map(function($d) use ($additional_where_job) {

            $customerID = (int) $d['CustomerID'];

            $whereJob = "CustomerID = $customerID";
            if (!empty($additional_where_job)) {
                $whereJob .= " AND " . implode(' AND ', $additional_where_job);
            }

            $totalJobRow = $this->M_Global->globalquery("
                SELECT COUNT(JobID) AS TotalJob FROM ListJob WHERE $whereJob
            ")->row_array();

            $d['TotalJob'] = (int) ($totalJobRow['TotalJob'] ?? 0);

            return $d;
        }, $listCustomer);

        echo json_encode($hasil);
    }
}