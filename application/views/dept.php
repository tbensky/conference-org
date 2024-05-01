<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster submissions by department</h3>";

$q = $this->db->query("select people from entry where year=? and format='poster'",$year);

$dist = [];
$santa_rosa = 0;
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    $one = false;
    foreach($json as $p)
    {
        if ($p['role'] == "Faculty" && $p['affiliation'] != 'Other...' && $p['affiliation'] != '--Select--' && $one == false)
            {
                $dept = $p['affiliation'];
                if (empty($dist[$dept]))
                    $dist[$dept] = 1;
                else $dist[$dept]++;
                $one = true;

                if ($p['santa_rosa'] == 'yes')
                    $santa_rosa++;
    
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
echo "<br/>";
echo "Total noting Santa Rosa Creek Foundation support: $santa_rosa";

echo "<hr/>";

echo "<h2>Needs poster printing</h2>";
echo "<h3>Know how to make one + not We have one already</h3>";

echo "<textarea cols=100 rows=20>";
echo "Poster number,title,authors\n";
$q = $this->db->query("select * from entry where format='poster' and year=? and poster_avail like '%We know how to make one%' and poster_avail not like '%We have one already%' order by seq asc",[$year]);
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    echo '"' . $row['seq'] . '","' . $row['title'] . '",';

    echo '"';
    foreach($json as $p)
        echo $p['name'] . "(" . $p['role'] . "," . $p['affiliation'] . "), ";
    echo '"' . "\n";
}
echo "</textarea>";


echo "<h2>Needs poster printing</h2>";
echo "<h3>Just not 'We have one already'</h3>";

echo "<textarea cols=100 rows=20>";
echo "Poster number,title,authors\n";
$q = $this->db->query("select * from entry where format='poster' and year=? and poster_avail not like '%We have one already%' order by seq asc",[$year]);
foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);
    echo '"' . $row['seq'] . '","' . $row['title'] . '",';

    echo '"';
    foreach($json as $p)
        echo $p['name'] . "(" . $p['role'] . "," . $p['affiliation'] . "), ";
    echo '"' . "\n";
}
echo "</textarea>";

$this->Misc->nav_dropdown();

?>