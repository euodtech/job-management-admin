<?php

class M_Global extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function query($query)
	{
		return $this->db->query($query);
	}

	/** @deprecated Use Query Builder with ->where() instead */
	function view($table)
	{
		return $this->db->get_where($table, ['status' => 1]);
	}

	function get_list($table, $data)
	{
		$query =  $this->db->get_where($table, $data);
		return $query;
	}

	/** @deprecated Use Query Builder ->get($table) instead */
	function get_result($table)
	{
		return $this->db->get($table);
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
		if (is_array($where) && !empty($where)) {
			$this->db->where($where);
		} elseif (is_string($where) && !empty($where)) {
			$this->db->where($where, null, false);
		} else {
			log_message('error', 'Invalid WHERE condition in update_data');
			return false;
		}

		$update = $this->db->update($table, $data);

		if (!$update) {
			log_message('error', 'DB Update Error: ' . json_encode($this->db->error()));
			return false;
		}

		return "success";
	}


	/** @deprecated Use update_data() with array WHERE and data instead */
	function update($table, $param)
	{
		log_message('error', 'M_Global::update() is deprecated and unsafe. Use update_data() instead.');
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


	

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getmultiparam($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function selectid($col, $table, $param)
	{
		return $this->db->query("SELECT $col FROM $table where $param order by $col desc limit 1");
	}

	/**
	 * Safe parameterized query method. Always use binds for user input.
	 * Example: globalquery("SELECT * FROM users WHERE id = ?", [$userId])
	 */
	public function globalquery($sql, $binds = [])
	{
		return $this->db->query($sql, $binds);
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getUserByEmail($table, $where)
	{
		$count_user = $this->db->query("SELECT * FROM $table WHERE $where ")->num_rows();
		return ($count_user > 0);
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getmultiparamrows($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param")->num_rows();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getmultiparam2($table, $param)
	{
		return $this->db->query("SELECT * FROM $table where $param")->result_array();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getmultiparam3($table, $param)
	{
		return $this->db->query("SELECT $table where $param");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getlast($table, $id, $param)
	{
		return $this->db->query("SELECT * FROM $table where $id = ? order by NO desc limit 1", [$param]);
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function q2join($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function qrealisasi($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT
			a.*,b.REALISASI from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function query2join($table1, $table2, $kolom1, $kolom2, $param, $value)
	{
		return $this->db->query("SELECT
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param = $value
			");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function query2joinmulti($table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT
			a.*,b.* from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function query2joinmulti2($selectcol, $table1, $table2, $kolom1, $kolom2, $param)
	{
		return $this->db->query("SELECT
			$selectcol from $table1 a left join
			$table2 b on $kolom1 = $kolom2 where $param
			");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getnumrowsupload($table, $kolom, $param, $param2, $param3)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = ? and TAHUNBELANJA = ? and KODEDANABELANJA = ?", [$param, $param2, $param3]);
		return $q->num_rows();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getnumrowsupload2($table, $kolom, $param, $param2, $param3, $param4)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = ? and TAHUNBELANJA = ? and KODEDANABELANJA = ? $param4", [$param, $param2, $param3]);
		return $q->num_rows();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getnumrowsupload3($table, $param)
	{
		$q = $this->db->query("SELECT * FROM $table where $param");
		return $q->num_rows();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getnumrows($table, $kolom, $param)
	{
		$q = $this->db->query("SELECT * FROM $table where $kolom = ?", [$param]);
		return $q->num_rows();
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function getsum($table, $kolom, $param)
	{
		return $this->db->query("SELECT SUM($kolom) as JUMLAH from $table where $param");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function distinct($col, $table, $param)
	{
		return $this->db->query("select distinct($col) as $col from $table where $param");
	}

	/** @deprecated Use globalquery() with parameterized queries instead */
	function countdistinct($col, $table, $param)
	{
		return $this->db->query("select count(distinct($col)) as $col from $table where $param");
	}

	public function countdata($table1)
	{
		return $this->db->query("SELECT count(*) as total FROM " . $this->db->escape_str($table1));
	}
}