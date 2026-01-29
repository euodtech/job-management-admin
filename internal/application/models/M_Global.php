<?php

class M_Global extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('default', TRUE);
	}

	function query($query)
	{
		return $this->db->query($query);
	}

	function view($table)
	{
		return $this->db->query("SELECT * FROM $table where status=1");
	}

	function get_list($table, $data)
	{
		$query =  $this->db->get_where($table, $data);
		return $query;
	}

	function get_result($table)
	{
		return $this->db->query("SELECT * FROM $table");
	}

	function insert($data, $table)
	{
		$insert = $this->db->insert($table, $data);


		if (!$insert) {
			$error = $this->db->error();
		} else {
			$error = "success";
		}
		return $error;
	}

	function bulkinsert($data, $table)
	{
		$insert = $this->db->insert_batch($table, $data);

		if (!$insert) {
			$error = $this->db->error();
		} else {
			$error = "success";
		}
		return $error;
	}

	function insertid($data, $table)
	{
		$insert = $this->db->insert($table, $data);
		if ($insert) {
			return $this->db->insert_id(); // berhasil, return ID
		} else {
			return false; // gagal
		}
	}

	function update_data($where, $data, $table)
	{
		// Force array-based WHERE to prevent SQL injection
		if (!is_array($where) || empty($where)) {
			log_message('error', 'Invalid WHERE condition in update_data');
			return false;
		}

		$this->db->where($where);
		$update = $this->db->update($table, $data);

		if (!$update) {
			log_message('error', 'DB Update Error: ' . json_encode($this->db->error()));
			return false;
		}

		return true;
	}


	function update($table, $param)
	{
		$this->db->query("UPDATE $table set $param");
	}

	public function delete($table, $where)
	{
		if (is_array($where)) {
			$this->db->where($where);
		} else {
			$this->db->where($where, null, false); // keep legacy raw where
		}

		if ($this->db->delete($table)) {
			return 'success';
		}

		return 'failed';
	}


	

	function getmultiparam($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param");
	}

	function selectid($col, $table, $param)
	{
		return $this->db->query("SELECT $col FROM $table where $param order by $col desc limit 1");
	}

	// function globalquery($param)
	// {
	// 	return $this->db->query("$param");
	// }
	public function globalquery($sql, $binds = [])
	{
		return $this->db->query($sql, $binds);
	}


	function getUserByEmail($table, $where)
	{
		$count_user = $this->db->query("SELECT * FROM $table WHERE $where ")->num_rows();

		if($count_user > 0 ) {
			$return = true;
		} else {
			$return = false;
		}
		
		return $return;
	}

	function getmultiparamrows($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param")->num_rows();
	}


	function getmultiparam2($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param")->result_array();
	}

	function getmultiparam3($table, $param)
	{
		return $this->db->query("SELECT $table where $param");
	}

	function getlast($table, $id, $param)
	{
		return $this->db->query("SELECT * FROM $table where $id = '$param' order by NO desc limit 1");
	}

	function q2join($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT 
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param 
			");
	}

	function qrealisasi($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT 
			a.*,b.REALISASI from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param 
			");
	}


	function query2join($table1, $table2, $kolom1, $kolom2, $param, $value)
	{
		return $this->db->query("SELECT 
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param = $value
			");
	}

	function query2joinmulti($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT 
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	function query2joinmulti2($selectcol, $table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT 
			$selectcol from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	function getnumrowsupload($table, $kolom, $param, $param2, $param3)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = '$param' and TAHUNBELANJA = '$param2' and KODEDANABELANJA='$param3'");
		return $q->num_rows();
	}

	function getnumrowsupload2($table, $kolom, $param, $param2, $param3, $param4)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = '$param' and TAHUNBELANJA = '$param2' and KODEDANABELANJA='$param3' $param4");
		return $q->num_rows();
	}

	function getnumrowsupload3($table, $param)
	{
		$q = $this->db->query("SELECT * FROM $table where $param");
		return $q->num_rows();
	}

	function getnumrows($table, $kolom, $param)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = '$param'");
		return $q->num_rows();
	}

	function getsum($table, $kolom, $param)
	{
		return $this->db->query("SELECT SUM($kolom) as JUMLAH from $table where $param");
	}


	function distinct($col, $table, $param)
	{
		return $this->db->query("select distinct($col) as $col from $table where $param");
	}

	function countdistinct($col, $table, $param)
	{
		return $this->db->query("select count(distinct($col)) as $col from $table where $param");
	}
	public function countdata($table1)
	{
		$query = "SELECT count(*) as total FROM " . $table1;
		return $this->db->query($query);
	}
}