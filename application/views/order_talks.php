<?php

// $year is defined

echo "<div class='container'>";
echo "<div class='row'>";

echo $this->Misc->nav_dropdown();
echo "<br/><br/>";

echo "<button onclick='reset()'>Reset order</button>";
echo "&nbsp;(Load " . anchor("start/order_talks_rtf","this file") . " into Word when done.)";

echo "<br/><br/>";

echo "<div id='talks'></div>";


echo "</div>";
echo "</div>";

?>

<script>

update();

function update()
{
	var url = '<?php echo site_url(); ?>' + "/start/get_talk_list/<?php echo $year; ?>";

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				$('#talks').html(e);
	  			}
	  });


}

function reset()
{
	var url = '<?php echo site_url(); ?>' + "/start/reset_talk_list";

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				update();
	  			}
	  });


}

function up(entry_hash)
{
	var url = '<?php echo site_url(); ?>' + "/start/move_talk/" + entry_hash + "/-1";

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

function down(entry_hash)
{
	var url = '<?php echo site_url(); ?>' + "/start/move_talk/" + entry_hash + "/1";

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

function update_tp(id)
{
	var url = '<?php echo site_url(); ?>' + "/start/save_tp/" + id;
	var place_id = "#place_" + id;
	var time_id = "#time_" + id;
	var time_group_id = "#time_group_" + id;
	var place = $(place_id).val();
	var time = $(time_id).val();
	var time_group = $(time_group_id).val();
	
	$.ajax({
	  type: "POST",
	  url: url,
	  data: {time: time, place: place, time_group: time_group},
	  success: function(e)
	  			{
	  				update();
	  			}
	  });
}

</script>