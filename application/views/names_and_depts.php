<?php

//$who and $what are defined

$year = $this->Setting->get("year");

$q = $this->db->query("select * from entry where format=? and year=?",[$what,$year]);
echo $this->Setting->get('year') . ",$who ($what)\n";

foreach($q->result_array() as $row)
{
	$json = $this->Entry->get_json($row['entry_hash']);
	foreach($json['people'] as $p)
	{
		if ($p['role'] == 'Student')
		{
			$affil = $p['affiliation'];
			if ($affil == 'Other...')
				$affil = $p['other_affiliation'];

			$affil = "Department of " . $affil;

			echo $this->Entry->flip_names($p['name']) . "," . $affil . ",";
			if ($row['format'] == "poster")
				echo "Poster #" . $row['seq'];
			else echo "Talk #" . $row['seq'];
			echo "\n";
		}
	}
}
?>