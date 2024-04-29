<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>$year poster assignmnets</h3>";


$q = $this->db->query("select * from entry where format='poster' and year=? order by seq asc",[$year]);


$used = [];
$poster = [];
$c = 1;
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);

    //look for santa rosa creek foundation posters
    $srcf = 0;
    foreach($json as $p)
    {
        if ($p['santa_rosa'] == "yes")
            $srcf++;
    }

    $one = false;
   
    foreach($json as $p)
    {
        if ($p['role'] == "Faculty" && $p['affiliation'] != 'Other...' && $p['affiliation'] != '--Select--' && $one == false)
            {
                $name = $p['name'];
                $dept = $p['affiliation'];
                $poster[$c] = ["seq" => $row['seq'],"dept" => $dept,"faculty" => $name,"title" => $row['title'],"santa_rosa" => $srcf];
                $one = true; 
                $c++;
            }
    }
}


/*
//2024-Apr-29 at 70 Finiss St.: this report is generating a few extra lines at the end. maybe this is why?
//get the rest that have no BSCM faculty affiliation
$q = $this->db->query("select * from entry where format='poster' and year=? and seq>=$c order by seq asc",[$year]);
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    $one = false;
    foreach($json as $p)
    {
        if ($p['role'] == "Faculty" && $one == false)
            {
                $name = $p['name'];
                $dept = $p['affiliation'];
                $poster[$c] = ["seq" => $row['seq'],"dept" => $dept,"faculty" => $name,"title" => $row['title']];
                $one = true; 
                $c++;
            }
    }
    
}
*/ 

foreach($poster as $p)
{
    echo "<b>" . $p['seq'] . "</b>: " . $p['dept'] . " (" . $p['faculty'] . "), " . substr($p['title'],0,50);
    if ($p['santa_rosa'])
        echo " <b>*SRCF support*</b>";
    echo "<br/>";
}



$this->Misc->nav_dropdown();

echo "</div>";
echo "</div>";
echo "</div>";

?>