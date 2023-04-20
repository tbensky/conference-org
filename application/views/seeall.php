<?php
$year = $this->Setting->get("year");

echo "<div class='container'>";
echo "<div class='row'>";

echo $this->Misc->nav_dropdown();
echo "<br/><br/>";


echo "<h1>Talks</h1>";
$q = $this->db->query("select * from entry where format='talk' and year=? order by seq asc",[$year]);
$this->Entry->list_all($q);

echo "<h1>Posters</h1>";
$q = $this->db->query("select * from entry where format='poster' and year=?",[$year]);
$this->Entry->list_all($q);





echo "</div>";
echo "</div>";
?>