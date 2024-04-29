<?php
//$year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";

echo "<h3>Poster ordering</h3>";

echo $this->Misc->nav_dropdown();


echo "<br/>";
echo "Move command: <input id=group_move_text type=text placeholder='k,n1,n2,n3,...'>";
echo "<button class='btn btn-sm btn-secondary' onclick='group_move($year)'>Go</button>";
echo "<br/>(Move posters n1,n2,n3... to after poster k)</br>";

echo "<br/>";

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

function group_move($year,$cmd)
{
	var cmd = $('#group_move_text').val().replaceAll(",","-");
	var url = '<?php echo site_url(); ?>' + `/start/group_move_poster/<?php echo $year; ?>/${cmd}`;

	$('#poster').html("Moving...");
	console.log(url);

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				update();
					$('#group_move_text').val("");
	  			}
	  });
}


</script>