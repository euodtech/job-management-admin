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
        $this->load->helper('fcm');
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
        $data['title'] = "Job";

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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
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
                "JobDate" => $row['JobDate'],
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "Address" => $row['Address'],
                "Fullname" => $row['Fullname'],
                "TypeJob" => $row['TypeJob'],
                "Status" => $row['Status'],
                "JobID" => $jobID,
                "UserID" => $row['UserID'] ?? null,
                "StatusCancelJob" => $cancelJob
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "draw" => $draw,
                "recordsTotal" => $recordsFiltered,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
            ]));
    }


    public function getDetailPhoto()
    {
        // Sanitize input
        $jobID = (int) $this->input->post('jobID');

        // Validate input
        if ($jobID <= 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        // Fetch job photos safely
        $this->db->from('ListJobDetail');
        $this->db->where('ListJobID', $jobID);
        $data = $this->db->get()->result_array();

        // Prefix photo paths with API base URL
        $apiBase = $this->config->item('base_api_url');
        foreach ($data as &$row) {
            if (!empty($row['Photo']) && strpos($row['Photo'], 'http') !== 0) {
                $row['Photo'] = $apiBase . $row['Photo'];
            }
        }
        unset($row);

        // Return JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data ?: []));
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
            $newJobID = $this->db->insert_id();

            // Notify all active drivers in the company about the new job
            try {
                $tokens = get_company_driver_fcm_tokens($data_create['CompanyID']);
                if (!empty($tokens)) {
                    send_fcm_to_multiple(
                        $tokens,
                        'New Job Available',
                        $data_create['JobName'] . ' - ' . $data_create['JobDate'],
                        $newJobID,
                        'new_job'
                    );
                }
            } catch (\Throwable $e) {
                log_message('error', 'FCM notification failed in create(): ' . $e->getMessage());
            }

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
        $typeJob = (int) $this->input->post('type_job');

        // Map type_job to the correct route
        $typeRoutes = [
            1 => 'line-interruption-job',
            2 => 'reconnection-job',
            3 => 'short-circuit-job',
            4 => 'disconnection-job',
        ];
        $redirectRoute = isset($typeRoutes[$typeJob]) ? $typeRoutes[$typeJob] : 'job-list';

        // Validate input
        if ($jobID <= 0) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Invalid job ID!</div>'
            );
            redirect(base_url($redirectRoute));
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

        redirect(base_url($redirectRoute));
    }


    public function getJobDetail()
    {
        $jobID = (int) $this->input->post('jobID');

        $this->db->select('
            ListJob.*,
            Customer.*,
            Customer.PhoneNumber AS CustPhoneNumber,
            ListUser.*,
            RescheduledJob.*,
            ListJob.JobID AS JobID
        ');
        $this->db->from('ListJob');
        $this->db->join('Customer', 'ListJob.CustomerID = Customer.CustomerID', 'left');
        $this->db->join('ListUser', 'ListJob.UserID = ListUser.UserID', 'left');
        $this->db->join('RescheduledJob', 'ListJob.JobID = RescheduledJob.JobID AND RescheduledJob.StatusApproved = 2', 'left');
        $this->db->where('ListJob.JobID', $jobID);

        $dataReturn = $this->db->get()->row_array();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($dataReturn));
    }

    public function reschedule_job()
    {
        $data['title'] = "Job";

        $companyID = $this->session->userdata('CompanyID');

        $this->render_page('main/job/page_reschedule_job', $data);
    }

    public function summary($type_job = 1)
    {
        $data['title'] = "Job";

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
                "JobDate" => $row['JobDate'],
                "JobName" => $row['JobName'],
                "CustomerName" => $row['CustomerName'],
                "Address" => $row['Address'],
                "Fullname" => $row['Fullname'],
                "TypeJob" => $row['TypeJob'],
                "Status" => $row['Status'],
                "JobID" => $jobID,
                "StatusCancelJob" => $cancelJob,
                "StatusReschedule" => $rescheduleJob,
                "AssignWhen" => $row['AssignWhen'],
                "FinishWhen" => $row['FinishWhen']
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "draw" => $draw,
                "recordsTotal" => $recordsFiltered,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
            ]));
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

        $dateFrom     = $this->input->get('dateFrom');
        $dateUntil    = $this->input->get('dateUntil');
        $statusFilter = $this->input->get('statusFilter') ?? 'pending';

        $statusMap = ['pending' => 1, 'approved' => 2, 'rejected' => 3];
        $statusValue = isset($statusMap[$statusFilter]) ? $statusMap[$statusFilter] : 1;

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

        $this->db->where('RescheduledJob.StatusApproved', $statusValue);

        if ($statusFilter !== 'pending') {
            if (!empty($dateFrom) && !empty($dateUntil)) {
                $this->db->where('DATE(RescheduledJob.created_at) >=', $dateFrom);
                $this->db->where('DATE(RescheduledJob.created_at) <=', $dateUntil);
            } else {
                $this->db->where('DATE(RescheduledJob.created_at) >=', date('Y-m-d', strtotime('-6 days')));
                $this->db->where('DATE(RescheduledJob.created_at) <=', date('Y-m-d'));
            }
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
                "JobDate" => $row['JobDate'],
                "RequestDateJob" => $row['RescheduledDateJob'],
                "Fullname" => $row['Fullname'],
                "JobName" => $row['JobName'],
                "Reason" => $row['Reason'],
                "ReasonReject" => $row['ReasonReject'] ?? null,
                "StatusApproved" => $row['StatusApproved'],
                "RescheduledID" => $row['RescheduledID'],
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "draw" => $draw,
                "recordsTotal" => $recordsFiltered,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
            ]));
    }


    public function getPendingRescheduleCount()
    {
        $role      = (int) $this->session->userdata('Role');
        $companyID = (int) $this->session->userdata('CompanyID');

        $this->db->select('COUNT(*) as cnt');
        $this->db->from('RescheduledJob');
        $this->db->join('ListJob', 'RescheduledJob.JobID = ListJob.JobID', 'left');
        $this->db->where('RescheduledJob.StatusApproved', 1);

        if ($role !== 1) {
            $this->db->where('ListJob.CompanyID', $companyID);
        }

        $result = $this->db->get()->row();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['count' => (int) $result->cnt]));
    }

    public function actionRescheduleJob($type, $reschedule_id = null)
    {
        if ($type === "approve") {

            $reschedule_id = (int) $reschedule_id;

            $this->db->trans_begin();

            $data_reschedule = $this->db->query(
                'SELECT JobID, RescheduledDateJob FROM RescheduledJob WHERE RescheduledID = ? FOR UPDATE',
                [$reschedule_id]
            )->row_array();

            if (empty($data_reschedule)) {
                $this->db->trans_rollback();
                redirect(base_url('reschedule-job'));
                return;
            }

            $jobID = (int) $data_reschedule['JobID'];
            $newDate = $data_reschedule['RescheduledDateJob'];

            // Also lock the job row and get details for notification
            $jobRow = $this->db->query(
                'SELECT JobID, JobName, UserID FROM ListJob WHERE JobID = ? FOR UPDATE',
                [$jobID]
            )->row_array();

            $this->db->where('RescheduledID', $reschedule_id)
                    ->update('RescheduledJob', ['StatusApproved' => 2]);

            $this->db->where('JobID', $jobID)
                    ->update('ListJob', [
                        'Status'  => 3,
                        'JobDate' => $newDate
                    ]);

            $this->db->trans_commit();

            // Notify the driver that their reschedule was approved
            try {
                if (!empty($jobRow['UserID'])) {
                    $fcm_token = get_driver_fcm_token($jobRow['UserID']);
                    if ($fcm_token) {
                        $jobLabel = !empty($jobRow['JobName']) ? $jobRow['JobName'] : 'Job #' . $jobID;
                        send_fcm_notification(
                            $fcm_token,
                            'Reschedule Approved',
                            $jobLabel . ' rescheduled to ' . $newDate,
                            $jobID,
                            'reschedule_approved'
                        );
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'FCM notification failed in reschedule approve: ' . $e->getMessage());
            }

            redirect(base_url('reschedule-job'));

        } elseif ($type === "reject") {

            $reschedule_id = (int) $this->input->post('reschedule_id');
            $reasonReject  = trim($this->input->post('reason', true));

            $this->db->trans_begin();

            $data_reschedule = $this->db->query(
                'SELECT JobID FROM RescheduledJob WHERE RescheduledID = ? FOR UPDATE',
                [$reschedule_id]
            )->row_array();

            if (empty($data_reschedule)) {
                $this->db->trans_rollback();
                redirect(base_url('reschedule-job'));
                return;
            }

            $this->db->where('RescheduledID', $reschedule_id)
                    ->update('RescheduledJob', [
                        'StatusApproved' => 3,
                        'ReasonReject'   => $reasonReject
                    ]);

            $this->db->trans_commit();

            // Notify the driver that their reschedule was rejected
            try {
                $rejectedJobID = (int) $data_reschedule['JobID'];
                $jobInfoQuery = $this->db->select('JobName, UserID')
                                    ->from('ListJob')
                                    ->where('JobID', $rejectedJobID)
                                    ->get();

                if ($jobInfoQuery) {
                    $jobInfo = $jobInfoQuery->row_array();
                    if (!empty($jobInfo['UserID'])) {
                        $fcm_token = get_driver_fcm_token($jobInfo['UserID']);
                        if ($fcm_token) {
                            $jobLabel = !empty($jobInfo['JobName']) ? $jobInfo['JobName'] : 'Job #' . $rejectedJobID;
                            $reason = !empty($reasonReject) ? ': ' . $reasonReject : '';
                            send_fcm_notification(
                                $fcm_token,
                                'Reschedule Rejected',
                                $jobLabel . $reason,
                                $rejectedJobID,
                                'reschedule_rejected'
                            );
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'FCM notification failed in reschedule reject: ' . $e->getMessage());
            }

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

    // ===================== ADMIN JOB CONTROL FEATURES =====================

    /**
     * AJAX GET: Return active drivers for a job's company.
     * Params: jobID, optional excludeUserID
     */
    public function getDriversByCompany()
    {
        $jobID = (int) $this->input->get('jobID');
        $excludeUserID = $this->input->get('excludeUserID');

        if ($jobID <= 0) {
            echo json_encode([]);
            return;
        }

        $job = $this->db->select('CompanyID')
                         ->from('ListJob')
                         ->where('JobID', $jobID)
                         ->get()
                         ->row_array();

        if (empty($job)) {
            echo json_encode([]);
            return;
        }

        $companyID = (int) $job['CompanyID'];

        // Company scoping
        $role = (int) $this->session->userdata('Role');
        $sessionCompanyID = (int) $this->session->userdata('CompanyID');
        if ($role !== 1 && $companyID !== $sessionCompanyID) {
            echo json_encode([]);
            return;
        }

        $this->db->select('UserID, Fullname');
        $this->db->from('ListUser');
        $this->db->where('ListCompanyID', $companyID);
        $this->db->where('StatusActive', 0);
        $this->db->where('deleted_at IS NULL', null, false);
        $this->db->order_by('Fullname', 'ASC');

        if (!empty($excludeUserID)) {
            $this->db->where('UserID !=', (int) $excludeUserID);
        }

        $drivers = $this->db->get()->result_array();
        echo json_encode($drivers);
    }

    /**
     * AJAX POST: Admin assigns an unassigned job to a driver.
     */
    public function assignJob()
    {
        $jobID    = (int) $this->input->post('jobID');
        $driverID = (int) $this->input->post('driverID');

        if ($jobID <= 0 || $driverID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
            return;
        }

        $this->db->trans_begin();

        // Lock the job row to prevent concurrent assignment
        $job = $this->db->query(
            'SELECT JobID, CompanyID, Status, JobName FROM ListJob WHERE JobID = ? FOR UPDATE',
            [$jobID]
        )->row_array();

        if (empty($job)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Job not found.']);
            return;
        }

        if ($job['Status'] !== null) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Job is already assigned.']);
            return;
        }

        // Company scoping
        $role = (int) $this->session->userdata('Role');
        $sessionCompanyID = (int) $this->session->userdata('CompanyID');
        if ($role !== 1 && (int) $job['CompanyID'] !== $sessionCompanyID) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        // Verify driver belongs to same company and is active
        $driver = $this->db->select('UserID')
                            ->from('ListUser')
                            ->where('UserID', $driverID)
                            ->where('ListCompanyID', $job['CompanyID'])
                            ->where('StatusActive', 0)
                            ->where('deleted_at IS NULL', null, false)
                            ->get()
                            ->row_array();

        if (empty($driver)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Driver not found or not active.']);
            return;
        }

        // Lock driver's active jobs to prevent double-assignment
        $activeJob = $this->db->query(
            'SELECT JobID FROM ListJob WHERE UserID = ? AND Status = 1 FOR UPDATE',
            [$driverID]
        )->row_array();

        if (!empty($activeJob)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'This driver already has an active job (Job #' . $activeJob['JobID'] . ').']);
            return;
        }

        $this->db->where('JobID', $jobID)
                  ->update('ListJob', [
                      'UserID'     => $driverID,
                      'Status'     => 1,
                      'AssignWhen' => date('Y-m-d H:i:s')
                  ]);

        $this->db->trans_commit();

        // Notify the assigned driver
        try {
            $fcm_token = get_driver_fcm_token($driverID);
            if ($fcm_token) {
                send_fcm_notification(
                    $fcm_token,
                    'Job Assigned to You',
                    !empty($job['JobName']) ? $job['JobName'] : 'Job #' . $jobID,
                    $jobID,
                    'job_assigned'
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'FCM notification failed in assignJob(): ' . $e->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Job assigned successfully.']);
    }

    /**
     * AJAX POST: Admin reassigns a job from one driver to another.
     */
    public function reassignJob()
    {
        $jobID       = (int) $this->input->post('jobID');
        $newDriverID = (int) $this->input->post('driverID');

        if ($jobID <= 0 || $newDriverID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
            return;
        }

        $this->db->trans_begin();

        // Lock the job row to prevent concurrent modification
        $job = $this->db->query(
            'SELECT JobID, CompanyID, UserID, Status, JobName FROM ListJob WHERE JobID = ? FOR UPDATE',
            [$jobID]
        )->row_array();

        if (empty($job) || !in_array($job['Status'], ['1', '3'], false)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Job not found or not in an assignable state.']);
            return;
        }

        // Company scoping
        $role = (int) $this->session->userdata('Role');
        $sessionCompanyID = (int) $this->session->userdata('CompanyID');
        if ($role !== 1 && (int) $job['CompanyID'] !== $sessionCompanyID) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        $oldDriverID = (int) $job['UserID'];

        if ($newDriverID === $oldDriverID) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'New driver is the same as the current driver.']);
            return;
        }

        // Verify new driver
        $driver = $this->db->select('UserID')
                            ->from('ListUser')
                            ->where('UserID', $newDriverID)
                            ->where('ListCompanyID', $job['CompanyID'])
                            ->where('StatusActive', 0)
                            ->where('deleted_at IS NULL', null, false)
                            ->get()
                            ->row_array();

        if (empty($driver)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Driver not found or not active.']);
            return;
        }

        // Lock driver's active jobs to prevent double-assignment
        $activeJob = $this->db->query(
            'SELECT JobID FROM ListJob WHERE UserID = ? AND Status = 1 FOR UPDATE',
            [$newDriverID]
        )->row_array();

        if (!empty($activeJob)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'This driver already has an active job (Job #' . $activeJob['JobID'] . ').']);
            return;
        }

        // Log the reassignment
        $this->db->insert('HistoryCancelJob', [
            'JobID'      => $jobID,
            'UserBefore' => $oldDriverID,
            'Reason'     => 'Reassigned by admin',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Update the job with new driver
        $this->db->where('JobID', $jobID)
                  ->update('ListJob', [
                      'UserID'     => $newDriverID,
                      'AssignWhen' => date('Y-m-d H:i:s')
                  ]);

        $this->db->trans_commit();

        // Notify the new driver about the reassignment
        try {
            $fcm_token = get_driver_fcm_token($newDriverID);
            if ($fcm_token) {
                send_fcm_notification(
                    $fcm_token,
                    'Job Assigned to You',
                    !empty($job['JobName']) ? $job['JobName'] : 'Job #' . $jobID,
                    $jobID,
                    'job_reassigned'
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'FCM notification failed in reassignJob(): ' . $e->getMessage());
        }

        echo json_encode(['success' => true, 'message' => 'Job reassigned successfully.']);
    }

    /**
     * AJAX POST: Admin cancels an assigned job, returning it to unassigned.
     */
    public function cancelJob()
    {
        $jobID  = (int) $this->input->post('jobID');
        $reason = trim($this->input->post('reason', true));

        if ($jobID <= 0 || empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Job ID and reason are required.']);
            return;
        }

        $this->db->trans_begin();

        // Lock the job row to prevent concurrent modification
        $job = $this->db->query(
            'SELECT JobID, CompanyID, UserID, Status FROM ListJob WHERE JobID = ? FOR UPDATE',
            [$jobID]
        )->row_array();

        if (empty($job) || !in_array($job['Status'], ['1', '3'], false)) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Job not found or not in a cancellable state.']);
            return;
        }

        // Company scoping
        $role = (int) $this->session->userdata('Role');
        $sessionCompanyID = (int) $this->session->userdata('CompanyID');
        if ($role !== 1 && (int) $job['CompanyID'] !== $sessionCompanyID) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        // Log cancellation
        $this->db->insert('HistoryCancelJob', [
            'JobID'      => $jobID,
            'UserBefore' => (int) $job['UserID'],
            'Reason'     => $reason,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Reset job to unassigned
        $this->db->where('JobID', $jobID)
                  ->update('ListJob', [
                      'Status'     => null,
                      'UserID'     => null,
                      'AssignWhen' => null
                  ]);

        $this->db->trans_commit();
        echo json_encode(['success' => true, 'message' => 'Job cancelled and returned to unassigned.']);
    }

    /**
     * AJAX POST: Admin reschedules a non-finished job to a new date.
     */
    public function adminRescheduleJob()
    {
        $jobID   = (int) $this->input->post('jobID');
        $newDate = trim($this->input->post('newDate', true));
        $reason  = trim($this->input->post('reason', true));

        if ($jobID <= 0 || empty($newDate) || empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        // Validate date format
        $dateObj = DateTime::createFromFormat('Y-m-d', $newDate);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $newDate) {
            echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
            return;
        }

        $this->db->trans_begin();

        // Lock the job row to prevent concurrent modification
        $job = $this->db->query(
            'SELECT JobID, CompanyID, JobDate, Status FROM ListJob WHERE JobID = ? FOR UPDATE',
            [$jobID]
        )->row_array();

        if (empty($job) || $job['Status'] == 2) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Job not found or already finished.']);
            return;
        }

        // Company scoping
        $role = (int) $this->session->userdata('Role');
        $sessionCompanyID = (int) $this->session->userdata('CompanyID');
        if ($role !== 1 && (int) $job['CompanyID'] !== $sessionCompanyID) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        $currentDate = date('Y-m-d', strtotime($job['JobDate']));

        // Auto-reject any pending reschedule requests for this job
        $this->db->where('JobID', $jobID)
                  ->where('StatusApproved', 1)
                  ->update('RescheduledJob', [
                      'StatusApproved' => 3,
                      'ReasonReject'   => 'Overridden by admin reschedule'
                  ]);

        // Insert auto-approved reschedule record
        $this->db->insert('RescheduledJob', [
            'JobID'              => $jobID,
            'CurrentDateJob'     => $currentDate,
            'RescheduledDateJob' => $newDate,
            'Reason'             => $reason,
            'StatusApproved'     => 2,
            'created_at'         => date('Y-m-d H:i:s')
        ]);

        // Update job date and set Status to 3 (driver stays flexible)
        $this->db->where('JobID', $jobID)
                  ->update('ListJob', [
                      'JobDate' => $newDate,
                      'Status'  => 3
                  ]);

        $this->db->trans_commit();
        echo json_encode(['success' => true, 'message' => 'Job rescheduled to ' . $newDate . '.']);
    }
}