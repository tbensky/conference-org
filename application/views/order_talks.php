<?php

// $year is defined

echo "<div class='container'>";
echo "<div class='row'>";
echo "<div class='col'>";
echo $this->Misc->nav_dropdown();


echo "<button class='btn btn-secondary' onclick='reset()'>Reset order</button>";
echo "<div class='mt-3'>";
echo "<label for='swap_talks_text' class='form-label'>Swap talks</label>";
echo "<div class='input-group' style='max-width: 360px;'>";
echo "<input id='swap_talks_text' type='text' class='form-control' placeholder='5,8'>";
echo "<button class='btn btn-secondary' type='button' onclick='swap_talks()'>Swap</button>";
echo "</div>";
echo "<small class='text-muted'>Enter two comma-delimited order numbers to swap their positions.</small>";
echo "<div id='swap_status' class='mt-2'></div>";
echo "</div>";

echo "<br/>";
echo "(Load " . anchor("start/order_talks_rtf","this file") . " into Word when done.)";
echo "</div>";

echo "<br/><br/>";

echo "<div class='mt-5' id='talks'></div>";


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

	if (confirm("Are you sure?") == false)
		return;

	$.ajax({
	  type: "POST",
	  url: url,
	  success: function(e)
	  			{
	  				update();
	  			}
	  });


}

function swap_talks()
{
	var raw = $('#swap_talks_text').val().trim();
	var pieces = raw.split(',');
	var status = $('#swap_status');

	status.removeClass('text-danger text-success').text('');

	if (pieces.length !== 2) {
		status.addClass('text-danger').text('Enter exactly two order numbers, such as 5,8.');
		return;
	}

	var first = pieces[0].trim();
	var second = pieces[1].trim();

	if (!/^\d+$/.test(first) || !/^\d+$/.test(second)) {
		status.addClass('text-danger').text('Both values must be whole numbers.');
		return;
	}

	var url = '<?php echo site_url(); ?>' + "/start/swap_talks/<?php echo $year; ?>";

	status.text('Swapping...');

	$.ajax({
	  type: "POST",
	  url: url,
	  dataType: "json",
	  data: {first: first, second: second},
	  success: function(response)
	  			{
	  				if (response.success) {
	  					status.removeClass('text-danger').addClass('text-success').text(response.message);
	  					$('#swap_talks_text').val('');
	  					update();
	  				}
	  				else {
	  					status.removeClass('text-success').addClass('text-danger').text(response.message);
	  				}
	  			},
	  error: function()
	  			{
	  				status.removeClass('text-success').addClass('text-danger').text('Swap failed.');
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
