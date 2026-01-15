<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MemberModel extends CI_Model
{

   function getMember($postData = null)
   {

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
      if ($searchValue != '') {
         $searchQuery = " (CustomerName like '%" . $searchValue . "%' or Email like '%" . $searchValue . "%' or Phone like'%" . $searchValue . "%' or ProvinceName like '%" . $searchValue . "%' or CityName like '%" . $searchValue . "%') ";
      }

      ## Total number of records without filtering
      $this->db->select('count(*) as allcount');
      $this->db->join('Province', 'Customer.ProvinsiID = Province.ProvinceID', 'left');
      $this->db->join('City', 'Customer.KotaID = City.CityID', 'left' );
      $records = $this->db->get('Customer')->result();
      $totalRecords = $records[0]->allcount;

      ## Total number of record with filtering
      $this->db->select('count(*) as allcount');
      if ($searchQuery != '') {
         $this->db->where($searchQuery);
      }
      $this->db->join('Province', 'Customer.ProvinsiID = Province.ProvinceID', 'left' );
      $this->db->join('City', 'Customer.KotaID = City.CityID', 'left' );
      $records = $this->db->get('Customer')->result();
      $totalRecordwithFilter = $records[0]->allcount;

      ## Fetch records
      $this->db->select('*');
      if ($searchQuery != '') {

         $this->db->where($searchQuery);
      }
      //   $this->db->order_by($columnName, $columnSortOrder);
      //   $this->db->limit($rowperpage, $start);
      $this->db->order_by("CustomerID", "desc");
      $this->db->join('Province', 'Customer.ProvinsiID = Province.ProvinceID', 'left' );
      $this->db->join('City', 'Customer.KotaID = City.CityID', 'left' );
      // $records = $this->db->get('Customer')->result();
        $records = $this->db->limit($rowperpage, $start)->get('Customer')->result();
      //   var_dump($records);
      //   exit();
      $data = array();
      //   return $records; 
      foreach ($records as $record) {

        //  $custid = $record->CustomerID;

        //  $recordstrans = $this->db->query("SELECT SUM(TotalTransaction) as tots FROM Transaction WHERE CustomerID = '$custid' AND (StatusTransID = 3 || StatusTransID = 4) ")->result();


        //  $qtyTrans = $this->db->query("SELECT COUNT(TransactionNumber) as totsQty FROM Transaction WHERE CustomerID = '$custid' AND (StatusTransID = 3 || StatusTransID = 4) ")->result();

        //  foreach ($recordstrans as $trs) {
        //     $transdata = $trs->tots;
        //  }

         $data[] = array(
            "CustomerID" => $record->CustomerID,
            "CustomerName" => $record->CustomerName,
            "Email" => $record->Email,
            "Phone" => $record->Phone,
            "BirthDate" => $record->BirthDate,
            "TotalPoin" => $record->TotalPoin,
            "Password" => $record->Password,
            // "StaffStatus" => $record->StaffStatus,
            "ProvinceName" => $record->ProvinceName,
            "TotalTransaction" => $transdata,
            "CityName"  => $record->CityName,
            "QtyTrans" => $qtyTrans[0]->totsQty,
            "Gender" => $record->Gender,
            "created_at" => $record->created_at
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