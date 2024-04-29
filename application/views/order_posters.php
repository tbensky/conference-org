<?php
//$year is defined

/*
all stats
all physics
all math
12 bio
all kines
all other
18 bio
all chem
~35 bio (or whatever bio remains)
*/

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster ordering</h3>";

echo "This will order posters by dept and randomly by faculty in a given dept.";

echo "<br/>";

$this->Misc->nav_dropdown();


//reset all to -1 in sequence
$this->db->query("update entry set seq=-1 where year=? and format='poster'",$year);

//now get all posters
$q = $this->db->query("select * from entry where year=? and format='poster'",$year);


//find all posters with a valid BCSM advisor
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
                $name = rand() . "_" . str_replace(", ","",$p['name']);
                $dept = $p['affiliation'];
                $poster[$row['entry_hash']] = ["hash" => $row['entry_hash'],"dept" => $dept,"sort" => $dept . $name,"srcf" => $srcf]; //"dept" => $dept,"title" => $row['title']];
                $one = true; 
            }
    }
    
}

asort($poster);


//order the BCSM advisor posters
$order = 1;
foreach($poster as $hash => $info)
{
    $this->db->query("update entry set seq=$order where entry_hash=?",$hash);
    $order++;
}

//now, find all posters without a valid BCSM advisor
$q = $this->db->query("select * from entry where year=? and format='poster' and seq=-1",$year);
foreach($q->result_array() as $row)
{
    //print_r($row);
    $json = json_decode($row['people'],true);
    //print_r($json);
    $this->db->query("update entry set seq=$order where entry_hash=?",$row['entry_hash']);
    $dept = "Other";
    $poster[$row['entry_hash']] = ["hash" => $row['entry_hash'],"dept" => $dept,"sort" => $dept,"srcf" => $srcf]; //"dept" => $dept,"title" => $row['title']];
    $order++;
}

/*
all stats
all physics
all math
12 bio
all kines
all other
18 bio
all chem
~35 bio (or whatever bio remains)
*/

$order = [
            "Statistics",
            "Physics",
            "Mathematics",
            "Kinesiology and Public Health",
            "Other",
            "School of Education",
            "Psychology",
            "Chemistry and Biochemistry",
            "Biological Sciences"
];


$poster1 = [];

foreach($order as $dept)
{
    foreach($poster as $p)
    {
        if ($p['dept'] == $dept)
            array_push($poster1,$p);
    }
}

echo "<h3>Final poster ordering</h3>";

$order = 1;
foreach($poster1 as $hash => $info)
{
    $this->db->query("update entry set seq=$order where entry_hash=?",[$info['hash']]);

    echo "<div class='row'>";

    echo "<div class='col'>";
    echo "[$order]: " . $info['sort'];
    if ($info['srcf'])
        echo "<b>*SRCF*</b>";
    echo "</div>";

    echo "</div>";
    $order++;
}



echo "<h4>Done.  Posters ordered by dept of faculty</h4>";


?>