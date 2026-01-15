<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VoucherCodeModel extends CI_Model {

   function getVoucherCode($postData=null){

     $response = array();

     ## Read value
     $draw = $postData['draw'];
     $start = $postData['start'];
     $rowperpage = $postData['length']; // Rows display per page
     $columnIndex = $postData['order'][0]['column']; // Column index
     $columnName = $postData['columns'][$columnIndex]['data']; // Column name
     $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
     $searchValue = $postData['search']['value']; // Search value

     ## Search 
     $searchQuery = "";

     if($searchValue != ''){
        $searchQuery = " (VoucherName like '%".$searchValue."%' or VoucherCode like '%".$searchValue."%' or QtyVo like'%".$searchValue."%' or ValidFrom like '%".$searchValue."%' or ValidUntil like '%".$searchValue."%'  ) ";
     }

     ## Total number of records without filtering
     $this->db->select('count(*) as allcount');
     $this->db->from('VoucherManual');
     $this->db->join('VoucherMaster', 'VoucherManual.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     
     $records = $this->db->get()->result();
     $totalRecords = $records[0]->allcount;

     ## Total number of record with filtering
     $this->db->select('count(*) as allcount');
     $this->db->from('VoucherManual');
     $this->db->join('VoucherMaster', 'VoucherManual.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     
     if($searchQuery != '')
        $this->db->where($searchQuery);


     $records = $this->db->get()->result();
     $totalRecordwithFilter = $records[0]->allcount;

     ## Fetch records
     $this->db->select('*');
     $this->db->from('VoucherManual');
     $this->db->join('VoucherMaster', 'VoucherManual.MasterVoucherID = VoucherMaster.VoucherMasterID', 'left');
     
     if($searchQuery != '')
     $this->db->where($searchQuery);
     //$this->db->order_by($columnName, $columnSortOrder);
     $this->db->limit($rowperpage, $start);
     $this->db->order_by("VoucherManualID", "desc");
     $records = $this->db->get()->result();

     $data = array();

     foreach($records as $record ){

         $data[] = array( 
               "VoucherName" => $record->VoucherName,
               "VoucherCode"=>$record->VoucherCode,
               "QtyVo"=>$record->QtyVo,
               "ValidFrom"=>$record->ValidFrom,
               "ValidUntil"=>$record->ValidUntil,
               "VoucherManualID"=>$record->VoucherManualID,
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