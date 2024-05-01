<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>$year Statistics</h3>";


$q = $this->db->query("select * from entry where year=?",[$year]);


$people_count = [];
$attended_one = [];
$presented_at_one = [];
$both = [];

$student_people_count = [];
$student_attended_one = [];
$student_presented_at_one = [];
$student_both = [];

foreach($q->result_array() as $row)
{
    $json = json_decode($row['people'],true);

    foreach($json as $p)
    {
        //overall people
        array_push($people_count,$p['name']);

        if ($p['role'] == 'Student' && $p['speaker'] == 'yes')
            array_push($student_people_count,$p['name']);
        
        //attended a conference
        if ($p["attended_sci_conf"] == "yes")
            array_push($attended_one,$p['name']);
           
        //presented at a conference
        if ($p["presented_sci_conf"] == "yes")
            array_push($presented_at_one,$p['name']);

        //attended and presented
        if ($p["attended_sci_conf"] == "yes" && $p["presented_sci_conf"] == "yes")
            array_push($both,$p['name']);

        //student and attended a conferencee
        if ($p["attended_sci_conf"] == "yes" && $p['role'] == 'Student' && $p['speaker'] == 'yes')
            array_push($student_attended_one,$p['name']);
           
        if ($p["presented_sci_conf"] == "yes" && $p['role'] == 'Student' && $p['speaker'] == 'yes')
            array_push($student_presented_at_one,$p['name']);

        if ($p["attended_sci_conf"] == "yes" && $p["presented_sci_conf"] == "yes" && $p['role'] == 'Student' && $p['speaker'] == 'yes')
            array_push($student_both,$p['name']);
    }
}


echo "<h4>Results (everyone, Faculty and Students)</h4>";
echo "<ul>";
echo "<li> Total unique people in conference: " . count(array_unique($people_count));
echo "<li> Have attended a conference before: " . count(array_unique($attended_one));
echo "<li> Have presented at a conference before: " . count(array_unique($presented_at_one));
echo "<li> Did both of the above: " . count(array_unique($both));
echo "</ul>"; 
echo "<hr/>";
echo "<h4>Results (Students presenters only)</h4>";
echo "<ul>";
echo "<li> Total unique students presenters in the conference: " . count(array_unique($student_people_count));
echo "<li> Students presenters who attended a conference before: " . count(array_unique($student_attended_one));
echo "<li> Students presenters who presented at a conference before: " . count(array_unique($student_presented_at_one));
echo "<li> Students presenters who did both of the above: " . count(array_unique($student_both));
echo "</ul>"; 



$this->Misc->nav_dropdown();

echo "</div>";
echo "</div>";
echo "</div>";

?>