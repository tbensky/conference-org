<?php

//$type, room and $time_group

echo "<div class='container'>";
echo "<div class='row'>";



$year = $this->Setting->get("year");
$rooms = $this->Setting->get("places");
$time_groups = $this->Setting->get("time_groups");
$this->Entry->reset_poster_order();

$qr = base_url() . "assets/2014_qrcode.png";


if ($type == "talk" && $room == "all" && $time_group == "all")
{
	echo "<h1>Talks</h1>";

	foreach(explode(",",$rooms) as $room)
	{
		foreach(explode(",",$time_groups) as $time_group)
		{
			$q = $this->db->query("select * from entry where format='talk' and place=? and time_group=? and year=? order by seq asc",Array($room,$time_group,$year));
			$this->Entry->mobile_list_all_talk($room,$time_group,$q);
		}	
	}
	
}
else if ($type == "poster" && $room == "all" && $time_group == "all")
{
	echo "<center>";
	echo "<h4>Poster search</h4>";

	echo "<div class='row'>";
	echo "<div class='col-2'>";
	echo anchor("start/mobile/menu/all/all","<i class='fa-solid fa-backward'></i>",["class" => "btn btn-outline-secondary"]); 
	echo "</div>";

	echo "<div class='col-10'>";
	echo "<input type=text class=form-control id='search' onkeyup='myFunction()' placeholder='Type name, title, keyword, etc.'>";
	echo "<div><small class='text-muted'><span id='record_count'></span> entries below</small></div>"; 
	echo "</div>";
	echo "</div>";
	echo "</center>";

	echo "<h1>Posters</h1>";
	
	$q = $this->db->query("select * from entry where format='poster' and year=? order by seq asc",Array($year));
	$this->Entry->mobile_list_all($q);
	echo "<hr/>";
	echo "<center>";
	echo anchor("start/mobile/menu/all/all","Return",Array("class" => "btn btn-primary btn-lg"));
	echo "</center>";
	echo "<br/><br/>";
}
else if ($type == "menu" && $room == "all" && $time_group == "all")
{
	$logo = base_url("assets/cp_logo_sm.png");
	echo "<br/>";
	echo "<center>";
	echo "<img src=$logo class='img-fluid'>";
	echo "<br/>";
	echo "<h3 class='mt-3 fw-bold fs-2' style='color: #004022'>$year Student Research Conference</h3><hr/>";
	echo "<div class='row justify-content-center'>";
	echo "<div class='col-md-6 col-md-offset-3 text-left'>";
	echo $this->Setting->get("summary");
	echo "</div>";
	echo "</div>";

	echo "<hr/>";
	echo "<h2>Talks</h2>";

	$tg = explode(",",$time_groups);

	foreach($tg as $time_group)
		{
			$hour = substr($time_group,0,2);
			if ($hour >= 8 && $hour <= 12)
				$pre = "Morning";
			else $pre = "Afternoon";
			echo "<h4>$pre: $time_group</h4>";
			foreach(explode(",",$rooms) as $room)
			{
				if (!$this->Entry->room_is_empty($year,$room,$time_group))
				{
					echo anchor("start/mobile/talk/$room/$time_group","$room",Array("class" => "btn btn-success btn-lg"));
					echo "<br/>";
					echo "<br/>";
				}
			}
		}

	echo "<hr/>";
	echo "<h2>Posters</h2>";

	echo anchor("start/mobile/poster/all/all","All posters",Array("class" => "btn btn-success btn-lg"));
	echo "<br/>";
	$poster_map = $this->Setting->get("poster_map");
	echo $poster_map;
	
	echo "<hr/>";
	echo "<h2>Other</h2>";
	echo "<small class='text-muted'>(Tap to see/hide.)</small>";

	$search = anchor("start/search/year",'<i class="fa-solid fa-magnifying-glass"></i>',["class" => "btn btn-lg btn-primary"]);

	echo <<<EOT
	<div id="my_group">
	
		<div class="btn-group" role="group" aria-label="Basic example">	
				<button class="btn btn-lg btn-secondary text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIndex" data-parent="#my_group" aria-expanded="false" aria-controls="collapseExample">Index</button>
				<button class="btn btn-lg btn-warning text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResources" data-parent="#my_group" aria-expanded="false" aria-controls="collapseExample">Resources</button>
				<button class="btn btn-lg btn-info text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQR" data-parent="#my_group" aria-expanded="false" aria-controls="collapseExample"><i class="fa-solid fa-qrcode"></i></button>
				$search
		</div>
	
		</p>

		<div class="accordion-group">
		<div class="collapse" id="collapseIndex">
  		<div class="card card-body">
EOT;
			$this->Entry->show_index($year);
echo <<<EOT1
  		</div>
		</div>
		</center>

		<div class="collapse" id="collapseResources">
	  		<div class="card card-body">
					<h2>Resources</h2>
					<h3>On campus</h3>
					<ul>
						<li> <a href=https://studentresearch.calpoly.edu/ target=_blank>Office of Student Research</a>
						<li> <a href=https://lsamp.calpoly.edu/ target=_blank>CSU LSAMP at Cal Poly</a>
						<li> <a href=https://beaconmentors.calpoly.edu/ target=_blank>BEACoN Mentors</a>
					</ul>

					<h3>Off campus</h3>
					<ul>
						<li> <a href=https://www.calstate.edu/impact-of-the-csu/research/csuperb/symposium target=_blank>CSU/CSUPERB Annual Biotechnology Symposium</a>
						<li> <a href=https://www.ssric.org/participate/src target=_blank target=_blank>CSU SSRIC Social Science Student Symposium</a>
						<li> <a href=https://emerging-researchers.org/ target=_blank>Emerging Researchers National (ERN) Conference</a>
						<li> <a href=https://www.cur.org/what/events/students/ncur/ target=_blank>National Conference on Undergraduate Research</a>
						<li> <a href=https://www.sacnas.org/conference target=_blank>SACNAS National Diversity in STEM Conference</a>
					</ul> 
	  		</div>
			</div>
		</div>


		<div class="collapse" id="collapseQR">
	  		<div class='row align-items-center'>
			<div class='col-3'></div>
			<div class='col-6'>
	  			<img class='img-fluid' src='$qr'>
			</div>
			<div class='col-3'></div>
			</div>
			
			
	  		</div>
	  	</div>

	</div>


<hr/>
<br/><br/>
EOT1;


	
}
else if ($type == "talk" && $room != "all" && $time_group != "all")
{
	echo "<br/>";
	echo "<center>";
	echo anchor("start/mobile/menu/all/all","Return",Array("class" => "btn btn-primary btn-lg"));
	echo "</center>";
	echo "<hr/>";
	$q = $this->db->query("select * from entry where format='talk' and place=? and time_group=? and year=? order by seq asc",Array($room,$time_group,$year));
	$this->Entry->mobile_list_all_talk($room,$time_group,$q);
	echo "<hr/>";
	echo "<center>";
	echo anchor("start/mobile/menu/all/all","Return",Array("class" => "btn btn-primary btn-lg"));
	echo "</center>";
	echo "<br/><br/>";
}



echo "</div>";
echo "</div>";
?>


<script>

var url = window.location.href;
var anchorlink = url.split('#');
var tag = anchorlink[1];
highlight(tag);

//for search
var nodes = document.getElementsByClassName('target');
$("#record_count").html(nodes.length);

function highlight(id)
{
	var use_id = '#'+id;
	console.log(use_id);
	//(use_id).css('background','#ff0000');
	$(use_id).effect("highlight", {}, 3000);
}

function myFunction() {
  var input = document.getElementById("search");
  var filter = input.value.toLowerCase();
  var nodes = document.getElementsByClassName('target');

  var block=0;
  for (i = 0; i < nodes.length; i++) {
    if (nodes[i].innerText.toLowerCase().includes(filter)) {
      nodes[i].style.display = "block";
      block++;
    } else {
      nodes[i].style.display = "none";
    }
  }
  $("#record_count").html(block);
}
</script>