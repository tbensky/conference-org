<?php

class Setting extends CI_Model 
{
	public function set($name,$value)
	{
		$this->db->query("delete from setting where name=?",$name);
		$this->db->query("insert into setting values(NULL,?,?)",$name,$value);
	}

	public function get($name)
	{
		$q = $this->db->query("select value from setting where name=?",$name);
		if ($q->num_rows() == 1)
		{
			$row = $q->row_array();
			return($row['value']);
		}
		return(false);
	}

}


?>