<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VoucherCustomerModel extends CI_Model {

   function getVoucherCustomer($postData=null){

     $response = array();

     ## Read value
     $draw = $postData['draw'];
     $start = $postData['start'];
     $rowperpage = $postData['length']; // Rows display per page
     $columnIndex = $postData['order'][0]['column']; // Column index
     $columnName = $postData['columns'][$columnIndex]['data']; // Column name
     $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
     $searchValue = $postData['search']['value']; // Search value
     $dateNow = date('Y-m-d');

     ## Search 
     $searchQuery = "";

     if($searchValue != ''){
        $searchQuery = " (VoucherName like '%".$searchValue."%' or Firstname like '%".$searchValue."%' or Lastname like'%".$searchValue."%' or ValidFrom like '%".$searchValue."%' or ValidUntil like '%".$searchValue."%'  ) ";
     }

     ## Total number of records without filtering
     $this->db->select('count(*) as allcount');
     $this->db->from('VoucherCustomer');
     $this->db->join('Customer', 'VoucherCustomer.CustomerID = Customer.CustomerID', 'left');
     $this->db->join('VoucherMaster', 'VoucherCustomer.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     $this->db->where('QtyVo >', 0);
     $this->db->where('ValidUntil >', $dateNow);
     $this->db->order_by('VoucherCustomerID', 'DESC');
     $records = $this->db->get()->result();
     $totalRecords = $records[0]->allcount;

     ## Total number of record with filtering
     $this->db->select('count(*) as allcount');
     $this->db->from('VoucherCustomer');
     $this->db->join('Customer', 'VoucherCustomer.CustomerID = Customer.CustomerID', 'left');
     $this->db->join('VoucherMaster', 'VoucherCustomer.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     $this->db->where('QtyVo >', 0);
     $this->db->where('ValidUntil >', $dateNow);
     $this->db->order_by('VoucherCustomerID', 'DESC');
     if($searchQuery != '')
        $this->db->where($searchQuery);


     $records = $this->db->get()->result();
     $totalRecordwithFilter = $records[0]->allcount;

     ## Fetch records
     $this->db->select('*');
     $this->db->from('VoucherCustomer');
     $this->db->join('Customer', 'VoucherCustomer.CustomerID = Customer.CustomerID', 'left');
     $this->db->join('VoucherMaster', 'VoucherCustomer.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     $this->db->where('QtyVo >', 0);
     $this->db->where('ValidUntil >', $dateNow);
     $this->db->order_by('VoucherCustomerID', 'DESC');
     if($searchQuery != '')
     $this->db->where($searchQuery);
     //$this->db->order_by($columnName, $columnSortOrder);
     $this->db->limit($rowperpage, $start);
     $this->db->order_by("VoucherName", "desc");
     $records = $this->db->get()->result();

     $data = array();

     foreach($records as $record ){

         $data[] = array( 
               "VoucherName" => $record->VoucherName,
               "CustomerName"=>$record->CustomerName,
               "QtyVo"=>$record->QtyVo,
               "ValidFrom"=>$record->ValidFrom,
               "ValidUntil"=>$record->ValidUntil,
               "VoucherCustomerID"=>$record->VoucherCustomerID,
               "CustomerID"=>$record->CustomerID
            ); 
     }

     ## Response
     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
     );

     return $response; 
   }

}