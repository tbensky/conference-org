<?php

echo `<div class="container">`;
echo `<div class="row">`;
echo `<div class="col">`;

echo "<h1>T-Shirts for $year</h1>";

$q = $this->db->query("select * from entry where year=?",[$year]);

foreach($q->result_array() as $row)
{
    $people = $row['people'];
    $json_people = json_decode($people,true);

    foreach($json_people as $p)
    {
            if ($p['speaker'] == 'yes' || $p['role'] == 'Faculty')
                {
                        $name = str_replace(" ","",$p['name']);
                        $name = str_replace(",","_",$name);
                        echo $name . "," . $p['role'] , "," . $p['email'] . "," . $p['tshirt'] . "," . "speaker=" . $p['speaker'] . "<br/>";
                }
    }

}

echo `</div>`;
echo `</div>`;
echo `</div>`;



?>
