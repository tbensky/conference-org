<?php

$year = $this->Setting->get('year');


$q = $this->db->query("select * from entry where year=?",$year);

echo "<textarea cols=100 rows=40>";

echo "#!/bin/bash\n";

foreach($q->result_array() as $row)
	echo "wget \"https://chart.googleapis.com/chart?cht=qr&chs=100x100&chl=https://conference.csm.calpoly.edu/index.php/start/view/" . $row['entry_hash'] . "\" -O " , $row['entry_hash'] . ".png" . "\n";
echo "</textarea>";

?>