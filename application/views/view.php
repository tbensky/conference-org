<?php

	echo "<div class=\"container\">";
	echo "<div class=\"row\">";
	echo "<div class=\"col\">";
	$json = $this->Entry->get_json($entry_hash);

	if ($json === false)
	{
		echo "<h3>That conference submission does not exist.</h3>";
	}
	else
	{
		echo $this->Entry->gen_preview($json);
		echo "<hr/>";
		echo '<p class="bg-success">Submitted on ' . $json['submit'] . '</p>';
	}
	echo "</div>";
	echo "</div>";
	echo "</div>";
?>