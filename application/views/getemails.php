<?php

//$how is defined

echo "<div class='container'>";
echo "<div class='row'>";

$this->Misc->nav_dropdown();
$year = $this->Setting->get("year");


$r = "";
$c = 0;

switch($how)
{
    case 'talks':
            $q = $this->db->query("select * from entry where format='talk' and year=?",[$year]);
            $title = "Talk emails";
            $students_only = false;
            $fac_only = false;
            break;
    case 'posters':
            $q = $this->db->query("select * from entry where format='poster' and year=?",[$year]);
            $title = "Poster emails";
            $students_only = false;
            $fac_only = false;
            break;
    case 'posters_to_print':
            $q = $this->db->query("select * from entry where format='poster' and poster_avail not like 'We have one%' and year=?",[$year]);
            $title = "Poster emails (likely need one printed)";
            $students_only = false;
            $fac_only = false;
            break;        
    case 'students_giving_talks':
            $q = $this->db->query("select * from entry where format='talk' and year=?",[$year]);
            $title = "Emails of students giving talks (no faculty emails)";
            $students_only = true;
            $fac_only = false;
            break; 
    case 'students_giving_posters':
            $q = $this->db->query("select * from entry where format='poster' and year=?",[$year]);
            $title = "Emails of students giving posters (no faculty emails)";
            $students_only = true;
            $fac_only = false;
            break; 
    case 'students_giving_posters_not_printed':
            $q = $this->db->query("select * from entry where format='poster' and poster_avail not like 'We have one%' and year=?",[$year]);;
            $title = "Emails of students giving posters that potentially need printing (no faculty emails)";
            $students_only = true;
            $fac_only = false; 
            break; 
    case 'cp_fac_only':
            $q = $this->db->query("select * from entry where year=?",[$year]);
            $title = "Emails of CP faculty only (no students or outside advisors";
            $students_only = false;
            $fac_only = true;
            break; 
}

foreach($q->result_array() as $row)
{
    $people = json_decode($row['people'],true);
    foreach($people as $p)
    {
        if (!empty($p['email']) && $students_only === false && $fac_only == false)
        {
            $r .= trim(strtolower($p['email'])) . "\n";
            $c++;
        }
        else if (!empty($p['email']) && $students_only === true && $fac_only == false && $p['role'] == "Student")
             {
                $r .= trim(strtolower($p['email'])) . "\n";
                $c++;
            }
        else if (!empty($p['email']) && $students_only === false && $fac_only == true && $p['role'] == "Faculty" && $p['affiliation'] != "Other...")
             {
                $r .= trim(strtolower($p['email'])) . "\n";
                $c++;
            }
    }
}

$r = implode("\n",array_unique(explode("\n",$r)));


echo "<h2>$title</h2>";

echo $c . " emails";
echo "<br/>";
echo "<textarea rows=30 cols=40>$r</textarea>";

echo "</div></div>";

?>