<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster submissions by department</h3>";

$q = $this->db->query("select people from entry where year=? and format='poster'",$year);

$dist = [];
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    $one = false;
    foreach($json as $p)
    {
       
        if ($p['role'] == "Faculty" and $one == false)
            {
                $dept = $p['affiliation'];
                //if ($dept != "Other..." && $dept != '--Select--')
                {
                    if (empty($dist[$dept]))
                        $dist[$dept] = 1;
                    else $dist[$dept]++;
                    $one = true;
                }
            }
    }
}

$t = 0;
echo "<ul>";
foreach($dist as $dept => $count)
{
    echo "<li> $dept: $count";
    $t += $count;
}
echo "</ul>";
echo "Total: $t";

echo "<hr/>";

$this->Misc->nav_dropdown();

?>