<?php

echo `<div class="container">`;
echo `<div class="row">`;
echo `<div class="col">`;

echo "<h1>T-Shirts for $year</h1>";

$q = $this->db->query("select * from entry where year=?",[$year]);

echo "format,name,role,email<br/>";
foreach($q->result_array() as $row)
{
    $people = $row['people'];
    $json_people = json_decode($people,true);
    foreach($json_people as $p)
    {
        if ($p['speaker'] == 'yes')
        {
            echo '"' . $row['format'] . '",';
            echo '"' . $p['name'] . '",';
            echo '"' . $p['role'] . '",';
            echo '"' . $p['email'] . '"';
            echo "<br/>";
        }
    }
      

}

echo `</div>`;
echo `</div>`;
echo `</div>`;



?>
