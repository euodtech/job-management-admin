<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_data_sidebar')) {

    function get_data_sidebar($adminID)
    {
    	$CI =& get_instance();
	    $CI->load->model('M_Global');

	    if (!$CI->M_Global) {
	        return "Gagal memuat model M_Global";
	    }

	    $getAdmin = "SELECT SidebarMenu FROM AdminLogin where AdminID = '$adminID'";
		$adminRes = $CI->M_Global->globalquery($getAdmin)->row_array();
		$id_sidebar = json_decode($adminRes['SidebarMenu']);


		$getHeader = "SELECT * FROM SidebarHeader";
		$headerRes = $CI->M_Global->globalquery($getHeader)->result_array();

		$sideBarSel = implode(',',$id_sidebar);

		$data = [];
		foreach($headerRes as $hRes)
		{
			$data_parentArr = [];

		        $sql = "SELECT * FROM SidebarMenu WHERE SidebarHeaderID = '$hRes[SideBarHeaderID]' && SidebarMenuID IN ($sideBarSel) ";
		        $data_parent = $CI->M_Global->globalquery($sql)->result_array();

		        foreach ($data_parent as $pRes) {

		        	$sql_child = "SELECT * FROM SidebarMenu WHERE SidebarParentID = '$pRes[SidebarMenuID]'";
			        $data_child = $CI->M_Global->globalquery($sql_child)->result_array();

			        $data_childArr = [];
			        foreach($data_child as $cRes)
			        {
			        	$data_childArr[] = array(
				        	"SidebarLabel"	=> $cRes['SidebarLabel'],
				        	"SidebarUrl"	=> $cRes['SidebarUrl']
				        );
			        }

		        	$data_parentArr[] = array(
			        	"SidebarLabel"	=> $pRes['SidebarLabel'],
			        	"SidebarUrl"	=> $pRes['SidebarUrl'],
			        	"SidebarIcon"	=> $pRes['SidebarIcon'],
			        	"Children"		=> $data_childArr
			        );

			        
		        }

		    if(count($data_parentArr) != 0)
		    {
			    $data[] = array(

			    		"SideBarHeaderLabel" => $hRes['SideBarHeaderLabel'],
			    		"SidebarMenu" => $data_parentArr
			    );
			}
		}

	    
	    

	    return $data;
    }
}

if (!function_exists('get_total_job')) {

    function get_total_job($type="all")
    {

		$CI =& get_instance();
		$role = $CI->session->userdata('Role');
        $companyID = $CI->session->userdata('CompanyID');

    	$CI =& get_instance();
	    $CI->load->model('M_Global');

	    if (!$CI->M_Global) {
	        return "Gagal memuat model M_Global";
	    }


	    $getAdmin = "SELECT * FROM ListJob where  (Status IS NULL OR Status = 1 OR Status = 3) ";

		if($role != '1') {

			$getAdmin .= " AND CompanyID =  '$companyID' ";

		}

		if($type != 'all') {
			$getAdmin .= " AND TypeJob = '$type' ";
		}

		$countJob = $CI->M_Global->globalquery($getAdmin)->result_array();

	    return count($countJob);
    }
}

if (!function_exists('get_total_reschedule')) {

    function get_total_reschedule()
    {

		$CI =& get_instance();
		$role = $CI->session->userdata('Role');
        $companyID = $CI->session->userdata('CompanyID');

    	$CI =& get_instance();
	    $CI->load->model('M_Global');

	    if (!$CI->M_Global) {
	        return "Gagal memuat model M_Global";
	    }


	    $getAdmin = "SELECT * FROM RescheduledJob LEFT JOIN ListJob ON RescheduledJob.JobID = ListJob.JobID WHERE DATE(RescheduledJob.created_at) = CURRENT_DATE AND StatusApproved = 1 ";

		if($role != '1') {

			$getAdmin .= " AND ListJob.CompanyID =  '$companyID' ";

		}

		$countJob = $CI->M_Global->globalquery($getAdmin)->result_array();

	    return count($countJob);
    }
}

