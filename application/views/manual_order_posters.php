<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster ordering</h3>";

echo $this->Misc->nav_dropdown();

echo "<div id='poster'>";

echo $this->Misc->nav_dropdown();
echo "<br/><br/>";
?>

<script>

update();

function update()
{
	var url = '<?php echo site_url(); ?>' + "/start/get_poster_list/<?php echo $year; ?>";

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				$('#poster').html(e);
	  			}
	  });


}

function poster_delta(entry_hash,d)
{
	var url = '<?php echo site_url(); ?>' + "/start/move_poster/" + entry_hash + `/${d}`;

	console.log(url);

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				update();
	  			}
	  });

}


</script>