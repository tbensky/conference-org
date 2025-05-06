<?php

$year = $this->Setting->get("year");


echo "year,format,title,abstract,people,talk availability,poster availability,order\n";
$q = $this->db->query("select * from entry where year=?",[$year]);

foreach($q->result_array() as $row)
{
    echo '"' . $row['year'] . '",';
    echo '"' . $row['format'] . '",';
    echo '"' . trim($row['title']) . '",';
    $cleaned = preg_replace('/\r\n|\r|\n/', '', $row['abstract']);
    echo '"' . trim(substr($cleaned,0,50)) . '",';

    echo '"';
    foreach(json_decode($row['people'],true) as $p)
    {
        echo "[" . $p['name'] . "," . $p['email'] . ",", $p['affiliation'] . "," . $p['role'] . "]";
    }
    echo '",';

    echo '"' . $row['talk_avail'] . '",';
    echo '"' . $row['poster_avail'] . '",';
    echo '"' . $row['seq'] . '"';
    echo "\n";

}

?>