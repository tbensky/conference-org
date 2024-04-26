<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster ordering</h3>";

$this->db->query("update entry set seq=-1 where year=? and format='poster'",$year);
$q = $this->db->query("select * from entry where year=? and format='poster'",$year);

$poster = [];
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    $one = false;

    //look for santa rosa creek foundation posters
    $srcf = 0;
    foreach($json as $p)
    {
        if ($p['santa_rosa'] == "yes")
            $srcf++;
    }

    foreach($json as $p)
    {
        if ($p['role'] == "Faculty" && $p['affiliation'] != 'Other...' && $p['affiliation'] != '--Select--' && $one == false)
            {
                $name = str_replace(", ","",$p['name']);
                $dept = $p['affiliation'];
                $poster[$row['entry_hash']] = ["sort" => $dept . $name,"srcf" => $srcf]; //"dept" => $dept,"title" => $row['title']];
                $one = true; 
            }
    }
    
}

asort($poster);


$order = 1;

foreach($poster as $hash => $info)
{
    $this->db->query("update entry set seq=$order where entry_hash=?",$hash);
    echo "<div class='row'>";

    echo "<div class='col'>";
    echo "[$order]: " . $info['sort'];
    if ($info['srcf'])
        echo "<b>*SRCF*</b>";
    echo "</div>";

    echo "</div>";
    $order++;
}



echo "<h1>No faculty affilication in BCSM</h1>";
echo "<h4>(Order and included at end of program.)</h4>";
echo "<h5>(Typically students in BCSM with non-BCSM advisors.)</h5>";
echo "<pre>";
$q = $this->db->query("select * from entry where year=? and format='poster' and seq=-1",$year);
foreach($q->result_array() as $row)
{
    print_r($row);
    $json = json_decode($row['people'],true);
    print_r($json);
    $this->db->query("update entry set seq=$order where entry_hash=?",$row['entry_hash']);
    $order++;
}
echo "</pre>";



echo "<h4>Done.  Posters ordered by dept of faculty</h4>";


$this->Misc->nav_dropdown();

?>