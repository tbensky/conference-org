<?php

echo "<div class='container'>";
echo "<div class='row'>";
$year = $this->Setting->get("year");

echo form_open("start/incoming_emails");

$q = $this->db->query("select * from entry and year=?",[$year]);

$c = 1;
foreach($q->result_array() as $row)
{
	$p = json_decode($row['people'],true);
	/*
	echo "<pre>";
	print_r($p);
	echo "</pre>";
	*/
	foreach($p as $person)
	{
		$id = base64_encode($person['name'] . "_" . $row['entry_id']);
		if (empty($person['email']))
		{
			echo $c . ": ";
			echo $person['name'] . ": ";
			echo "<input name=\"$id\">@calpoly.edu";
			echo '<br/>';
			echo '<br/>';
			$c++;
		}
	}

}

echo "<input type=submit>";
echo form_close();

echo "</div>";
echo "</div>";


?>