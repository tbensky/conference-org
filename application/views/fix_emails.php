<?php

$lookup = Array();
foreach($email as $key => $val)
{
	$db = base64_decode($key);
	$a = explode("_",$db);
	if (count($a)==2)
	{
		$lookup[$a[0]] = $val;
	}
}


$year = $this->Setting->get("year");
$q = $this->db->query("select * from entry and year=?",[$year]);
foreach($q->result_array() as $row)
{
	$entry_id = $row['entry_id'];
	$people = json_decode($row['people'],true);
	$np = Array();
	foreach($people as $p)
		{
			if (!empty($lookup[$p['name']]))
			{
				$p['email'] = $lookup[$p['name']] . "@calpoly.edu";
				array_push($np,$p);
			}
		}

	if (count($np))
	{
		$up = json_encode($np);
		$sql = "update entry set people='" . $up . "' where entry_id=$entry_id";
		$this->db->query($sql);
	}	

}

echo anchor("start/admin","Return");

?>