<?php

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Set poster locations for $year</h3>";
echo $this->Misc->nav_dropdown();
echo "<br/><br/>";

if (!empty($result))
{
	$alert_class = $result['success'] ? "alert-success" : "alert-danger";
	echo "<div class='alert $alert_class'>";
	foreach($result['messages'] as $message)
		echo htmlspecialchars($message) . "<br/>";
	echo "</div>";
}

echo "<p>Enter one range per line in the form <code>start,end,location</code>.</p>";
echo "<p>Example: <code>1,10,3rd floor</code></p>";

echo form_open("start/poster_locations");
echo "<div class='form-group'>";
echo "<textarea name='location_text' class='form-control' rows='8' placeholder='1,10,3rd floor'>";
echo htmlspecialchars($location_text);
echo "</textarea>";
echo "</div>";
echo "<button type='submit' class='btn btn-primary'>Save poster locations</button>";
echo form_close();

echo "<hr/>";
echo "<h4>Current poster locations</h4>";
echo "<table class='table table-striped table-sm'>";
echo "<tr><th>Poster</th><th>Location</th><th>Title</th></tr>";

foreach($posters as $poster)
{
	$place = trim((string)$poster['place']);
	if ($place === "")
		$place = "&nbsp;";
	else
		$place = htmlspecialchars($place);

	echo "<tr>";
	echo "<td>" . (int)$poster['seq'] . "</td>";
	echo "<td>" . $place . "</td>";
	echo "<td>" . htmlspecialchars($poster['title']) . "</td>";
	echo "</tr>";
}

echo "</table>";

echo "</div>";
echo "</div>";
echo "</div>";

?>
