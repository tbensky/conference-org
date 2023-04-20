<?php

foreach($css_files as $file)
	echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"$file\">";

foreach($js_files as $file)
	echo "<script src=\"$file\"></script>";


echo "<div class='container'>";
echo "<div class='row'>";

echo $this->Misc->nav_dropdown();
echo "<br/><br/>";


echo $output;

echo "</div>";
echo "</div>";


?>