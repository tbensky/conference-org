<?php

class Entry extends CI_Model 
{

public function get_json($entry_hash)
{
	$year = $this->Setting->get("year");
	$q = $this->db->query("select * from entry where entry_hash=? and year=?",Array($entry_hash,$year));	
	if ($q->num_rows() == 0)
		return(false);
	$row = $q->row_array();
	$row['people'] = json_decode($row['people'],true);
	return($row);
} 

function italics($x)
{
	$a = explode("[[",$x,2);
	if ($a[0] == $x)
		return($x);
	
	$b = explode("]]",$a[1],2);
	if ($b[0] == $a[1])
		return($a[1]);

	$y = $a[0] . "<i>" . $b[0] . "</i>" . $b[1];
	return($this->italics($y));
}

function italics_rtf($x)
{
	$a = explode("[[",$x,2);
	if ($a[0] == $x)
		return($x);
	
	$b = explode("]]",$a[1],2);
	if ($b[0] == $a[1])
		return($a[1]);

	$y = $a[0] . "{\i " . $b[0] . "\i}" . $b[1];
	return($this->italics($y));
}

function fac_last($people)
{
	$stud_not_presenting = [];
	$stud_presenting = [];
	$fac = [];
	$other = [];
	foreach($people as $p)
	{
		if ($p['role'] == "Student" && (empty($p['speaker']) || $p['speaker'] != 'yes'))
			array_push($stud_not_presenting,$p);
		else if ($p['role'] == "Student" && (empty($p['speaker']) || $p['speaker'] == 'yes'))
			array_push($stud_presenting,$p);
		else if ($p['role'] == "Faculty")
			array_push($fac,$p);
		else if ($p['role'] == "Other")
			array_push($other,$p);
	}

	return(array_merge($stud_presenting,$stud_not_presenting,$other,$fac));
	
	
	
}

function find_presenter($json_people)
{
	$any_presenter = false;
	foreach($json_people as $p)
	{
		if (!empty($p['speaker']) && $p['speaker'] == "yes")
			$any_presenter = true;
	}

	$j1 = [];
	foreach($json_people as $p)
	{
		if ($any_presenter === false && empty($p['speaker']) && $p['role'] == "Student")
				$p['speaker'] = "yes";
		else if (empty($p['speaker']))
				$p['speaker'] = "no";

		array_push($j1,$p);
	}
	return($j1);
}

function get_entry_parts($json)
	{
		$entry = Array();
		$ret = "";

		if (empty($json['entry_hash']))
			$json['entry_hash'] = "";

		if (empty($json['seq']))
			$json['seq'] = "";

		$json['people'] = $this->find_presenter($json['people']);

		$json['people'] = $this->fac_last($json['people']);

		
		$entry['entry_hash'] = $json['entry_hash'];
		$entry['title'] = $this->italics($json['title']);
		$entry['abstract'] = $this->italics($json['abstract']);
		$entry['seq'] = $json['seq'];
		$entry['format'] = $json['format'];
		$entry['talk_avail'] = $json['talk_avail'];
		$entry['poster_avail'] = $json['poster_avail'];
		$entry['place'] = $json['place'];
		$entry['time'] = $json['time'];
		$entry['time_group'] = $json['time_group'];
		$affil_list = Array();
		$people_list = Array();

		foreach($json['people'] as $p)
		{
			if ($p['affiliation'] != "Other...")
			{
				if ($p['affiliation'] == 'Kinesiology')
					$p['affiliation'] = 'Kinesiology and Public Health';
				if ($p['affiliation'] != 'School of Education')
					$affil = "Department of " . $p['affiliation'];
				else $affil = $p['affiliation'];
			}
			else $affil = $p['other_affiliation'];

			array_push($people_list,Array('name' => $p['name'],'affil' => $affil,'frost' => $p['frost'],'santa_rosa' => $p['santa_rosa'],'speaker' => $p['speaker']));
			array_push($affil_list,$affil);
		}

		$entry['person_and_affil'] = Array();
		foreach($people_list as $p)
			array_push($entry['person_and_affil'],Array('name' => $p['name'],'affil' => $p['affil'],'speaker' => $p['speaker']));
	
		$frost = "";
		$santa_rosa = "";
		$any_frost = false;
		$any_presenter = false;
		$any_santa_rosa = false;

		$affil_list = array_values(array_unique($affil_list));
		$authors = "";
	
		$x = "";
		$aindex = "";
		
		foreach($people_list as $vals)
		{
			$frost = "";
			if ($vals['frost'] == 'yes')
			{
				$frost = "&dagger;";
				$any_frost = true;
			}

			$santa_rosa = "";
			if ($vals['santa_rosa'] == 'yes')
			{
				$santa_rosa = "&sect;";
				$any_santa_rosa = true;
			}

			if ($vals['speaker'] == "yes")
			{
				$frost .= "&starf;";
				$any_presenter = true;
			}
			$a = explode(",",$vals['name']);
			if (count($a) == 2)
			{
				$x .= $a[1] . " " . $a[0];
			
				$aindex .= $a[0] . ", " . $a[0] . "|";
				$i = 1 + array_search($vals['affil'],$affil_list);
				if (count($affil_list) > 1)
					$x  .= "<sup>$i$frost$santa_rosa</sup>,";
				else $x .= "<sup>$frost$santa_rosa</sup>, ";
			}
		}
		$ret .= rtrim($x,", ");
	
		$entry['people'] = $ret;	

		$i = 1;
		$x = "";
		foreach ($affil_list as $value)
		{
			if (count($affil_list) > 1)
				$x .= "<sup>$i</sup> $value, ";
			else $x .= "$value, ";
			$i++;	
		}
		if ($any_frost)
			$x .= "<sup>&dagger;</sup>Frost Support, ";
		if ($any_santa_rosa)
			$x .= "<sup>&sect;</sup>Santa Rosa Creek Foundation Support, ";
		if ($any_presenter)
			$x .= "<sup>&starf;</sup>Speaker";
		$x = rtrim($x,", ");

		$entry['affil'] = $x;
		return($entry);

	}

function get_entry_parts_flat($json)
	{
		$entry = Array();
		$ret = "";
		
		$entry['title'] = $this->italics($json['title']);
		$entry['abstract'] = $this->italics($json['abstract']);

		$affil_list = Array();
		$people_list = Array();

		foreach($json['people'] as $p)
		{
			
			if ($p['affiliation'] != "Other...")
			{
				if ($p['affiliation'] == 'Kinesiology')
					$p['affiliation'] = 'Kinesiology and Public Health';
				if ($p['affiliation'] != 'School of Education')
					$affil = "Department of " . $p['affiliation'];
				else $affil = $p['affiliation'];
			}
			else $affil = $p['other_affiliation'];

			array_push($people_list,Array(
								'name' => $p['name'],
								'affil' => $affil,
								'frost' => $p['frost'],
								'santa_rosa' => $p['santa_rosa']
							));
			array_push($affil_list,$affil);
		}

	
		$frost = "";
		$any_frost = false;

		$santa_rosa = "";
		$any_santa_rosa = false;

		$affil_list = array_values(array_unique($affil_list));
		$authors = "";
	
		$x = "";
		$aindex = "";
		
		foreach($people_list as $vals)
		{
			$frost = "";
			if ($vals['frost'] == 'yes')
			{
				$frost = "&dagger;";
				$any_frost = true;
			}

			$santa_rosa = "";
			if ($vals['santa_rosa'] == 'yes')
			{
				$santa_rosa = "&sect;";
				$any_santa_rosa = true;
			}

			$a = explode(",",$vals['name']);
			if (count($a) == 2)
			{
				$x .= $a[1] . " " . $a[0];
				$aindex .= $a[1] . " " . $a[0] . ",";
			}
		}
		$ret .= rtrim($x,", ");
	
		$entry['people'] = $ret;	
		$entry['aindex'] = $aindex;

		$i = 1;
		$x = "";
		foreach ($affil_list as $value)
		{
			$x .= "$value, ";
		}
		$x = rtrim($x,", ");
		$entry['affil'] = $x;
		return($entry);

	}


function gen_preview($json)
	{
		$entry = $this->get_entry_parts($json);
		$ret = "";
	
		$ret .= "<h2>";
		if (!empty($entry['seq']))
			$ret .= "[" . $entry['seq'] . "] ";
		if (!empty($entry['time']))
			$ret .= $entry['time'] . ": ";
		$ret .= $entry['title'] . "</h2>";
		$ret .= "<h4>" . $entry['people'] . "</h4>";
		$ret .= "<i>" . $entry['affil'] . "</i>";
		$ret .= "<br/><br/>";
		//$ret .= str_replace('$','$',$entry['abstract']);
		$ret .= $entry['abstract'];
		$ret .= "<hr/>";
		$ret .= "Type: " . ucfirst($entry['format']);
		$ret .= "<br/>";
		if ($entry['format'] == 'talk')
			$ret .= "Availability: " . $entry['talk_avail'];
		else $ret .= "Availability: " . $entry['poster_avail'];
		return($ret);

	}

	function gen_preview_mobile($json)
	{
		$entry = $this->get_entry_parts($json);
		$ret = "";

		//$ret .= "<div class='target'>";
		
		$ret .= "<h2>";
	

		if (!empty($entry['time']))
			$ret .= "<code>(" . $entry['time'] . ")</code><br/>";

		$ret .= "<span id=\"" . $json['entry_hash'] . "\">";
		if (!empty($entry['seq']))
			$ret .= "[" . $entry['seq'] . "]: ";
		$ret .=  $entry['title'] . "</h2>";
		$ret .= "</span>";
		$ret .= "<h4>" . $entry['people'] . "</h4>";
		$ret .= "<i>" . $entry['affil'] . "</i>";
		$ret .= "<br/><br/>";
		$ret .= $entry['abstract'];

		//$ret .= "</div>";

		return($ret);

	}

	function gen_preview_mobile_poster($json)
	{
		$entry = $this->get_entry_parts($json);
		$ret = "";
	
		$ret .= "<h2>";
		$ret .= "<span id=\"" . $json['entry_hash'] . "\">";
		if (!empty($entry['seq']))
			$ret .= "[" . $entry['seq'] . "]: ";
		$ret .= $entry['title'] . "</h2>";
		$ret .= "</span>";
		$ret .= "<h4>" . $entry['people'] . "</h4>";
		$ret .= "<i>" . $entry['affil'] . "</i>";
		$ret .= "<br/><br/>";
		$ret .= $entry['abstract'];
		return($ret);

	}

	function list_all($q)
	{
		echo "<table class='table table-hover'>";
		foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			echo "<tr class='target'><td>";
			echo anchor("start/crud_edit/" . $row['entry_hash'],'Edit',Array("class" => "btn btn-primary btn-xs"));
			echo "&nbsp; Preview link: <input value='";
			echo site_url("start/view/" . $row['entry_hash']);
			echo "' size=100>";
			echo "<br/>";
			echo "Emails:<br/>";
			echo "<textarea rows=3 cols=50>" . $this->get_emails($row['entry_hash']) . "</textarea>";
			echo "<p/>";
			$roles = $this->get_roles($row['entry_hash']);
			/*
			if ($roles['student'] == true && $roles['faculty'] == false)
				echo '<h3><span class="label label-danger">Student only</span></h3>';
			*/
			if ($roles['student'] == false && $roles['faculty'] == true)
				echo '<h3><span class="label label-danger">Faculty only</span></h3>';
			echo "<p/>";
			echo $this->Entry->gen_preview($json);
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function list_all_search($q)
	{
		echo "<table class='table table-hover'>";
		foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			echo "<tr class='target'><td>";
			
			echo $this->Entry->gen_preview_mobile($json);
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function mobile_list_all_talk($room,$time_group,$q)
	{
		echo "<center><h1><kbd>$room</kbd><br/>$time_group</h1></center>";
		echo "<table class='table table-hover'>";
		foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			echo "<tr><td>";
			echo $this->Entry->gen_preview_mobile($json);
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function mobile_list_all($q)
	{
		echo "<table class='table table-hover'>";
		foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			echo "<tr class='target'><td>";
			echo $this->Entry->gen_preview_mobile_poster($json);
			echo "</td></tr>";
		}
		echo "</table>";
	}

	function count_them()
	{
		$year = $this->Setting->get("year");

		$q = $this->db->query("select count(*) as count from entry where format='poster' and year=?",[$year]);
		$row = $q->row_array();	
		$pc = $row['count'];

		$q = $this->db->query("select count(*) as count from entry where format='talk' and year=?",[$year]);
		$row = $q->row_array();	
		$tc = $row['count'];
		return(Array('posters' => $pc,'talks' => $tc));
	}

	//https://stackoverflow.com/questions/14684077/remove-all-html-tags-from-php-string
	function strip_tags_content($text) 
	{
    	return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
 	}

 	function flip_names($x)
 	{
 		$a = explode(",",$x);
 		return(trim($a[1]) . " " . trim($a[0]));
 	}

 	function place_dropdown($id,$cur)
 	{
 		echo "<select id=place_$id onchange='update_tp($id)'>";
 		echo "<option value='-'>-</option>";
 		$places = $this->Setting->get("places");
 		foreach(explode(",",$places) as $place)
 		{
 			echo "<option value=\"" . $place . "\"";
 			if ($place == $cur)
 				echo " selected";
 			echo ">" . $place . "</option>";
 		}
 		echo "</select>";
 	}

 	function time_dropdown($id,$cur)
 	{
 		echo "<select id=time_$id onchange='update_tp($id)'>";
 		echo "<option value='-'>-</option>";
 		$times = $this->Setting->get("times");
 		foreach(explode(",",$times) as $time)
 		{
 			echo "<option value=\"" . $time . "\"";
 			if ($time == $cur)
 				echo " selected";
 			echo ">" . $time . "</option>";
 		}
 		echo "</select>";
 	}

 	function time_group_dropdown($id,$cur)
 	{
 		echo "<select id=time_group_$id onchange='update_tp($id)'>";
 		echo "<option value='-'>-</option>";
 		$tgs = $this->Setting->get("time_groups");
 		foreach(explode(",",$tgs) as $tg)
 		{
 			echo "<option value=\"" . $tg . "\"";
 			if ($tg == $cur)
 				echo " selected";
 			echo ">" . $tg . "</option>";
 		}
 		echo "</select>";
 	}

	function get_place_color($place)
	{
		$colors = ["Salmon","Pink","Plum","PaleGreen","Turquoise"];
		$pc = [];
		$c = 0;
		$places = $this->Setting->get("places");
		foreach(explode(",",$places) as $p)
		{
			$pc[$p] = $colors[$c];
			$c++;	
		}
		return($pc[$place]);

	}

	function get_time_group_color($time_group)
	{
		if (empty($time_group))
			return("white");
		$colors = ["DeepSkyBlue","Tan","Silver"];
		$tgc = [];
		$c = 0;
		$tgs = $this->Setting->get("time_groups");
		foreach(explode(",",$tgs) as $tg)
		{
			$tgc[$tg] = $colors[$c];
			$c++;	
		}
		return($tgc[$time_group]);

	}

	function get_talk_list($year)
	{
		$q = $this->db->query("select * from entry where year=? and format='talk' order by seq asc",Array($year));
		echo "<table class='table'>";
		echo "<thead><tr><td>Control</td><td>Preference</td><td>Room</td><td>Time</td><td>Time Group</td><td>Info</td></tr></thead>";
		foreach($q->result_array() as $row)
		{
			$affil = [];;
			$json = json_decode($row['people'],true);
			foreach($json as $p)
				array_push($affil,$p['affiliation']);
			$affil = array_unique($affil);
			echo "<tr bgcolor='" . $this->get_place_color($row['place']) . "'><td>";
			echo $row['seq'] . ": ";
			echo "<br/>";
			echo "<button class='btn btn-outline-primary btn-sm' onclick=up('" . $row['entry_hash'] . "')><i class='fa-solid fa-arrow-up'></i></button>";
			echo " ";
			echo "<button class='btn btn-outline-primary btn-sm' onclick=down('" . $row['entry_hash'] . "')><i class='fa-solid fa-arrow-down'></i></button>";
			echo "</td>";
			echo "<td>";
			echo $row['talk_avail'];
			echo "</td>";
			echo "<td>";
			$this->place_dropdown($row['entry_id'],$row['place']);
			echo "</td>";

			echo "<td>";
			$this->time_dropdown($row['entry_id'],$row['time']);
			echo "</td>";

			//echo "<td bgcolor='" . $this->get_time_group_color($row['time_group']) . "'>";
			echo "<td style='background:" . $this->get_time_group_color($row['time_group']) . "'>";
			$this->time_group_dropdown($row['entry_id'],$row['time_group']);
			echo "</td>";

			echo "<td>";
			$json = $this->Entry->get_json($row['entry_hash']);
			/*
			$entry = $this->Entry->get_entry_parts_flat($json);
			echo $entry['title'] . ": " . $entry['people'];
			*/
			echo "<b>" . implode(",",$affil) . "</b><br/>";
			$s = "";
			$f = "";
			foreach($json['people'] as $p)
			{
				if ($p['role'] != 'Faculty')
					$s .= $this->flip_names($p['name']) . ", ";
				else $f .= $this->flip_names($p['name']) . ", ";
			}
			$s = trim($s,", ");
			$f = trim($f,", ");
			$title = $this->italics($json['title']);

			if (empty($s) && !empty($f)) // no students
				echo "$f: " . $title;
			else if (empty($f) && !empty($s)) // no advisor
				echo "$s: "  . $title;
			else echo "$s (advisor $f): " . substr($title,10);
			echo "</td></tr>";
		}
		echo "</table>";
	}



	//SRCF = santa rosa creek foundation
	function get_poster_list($year)
	{
		$q = $this->db->query("select * from entry where year=? and format='poster' order by seq asc",Array($year));
		echo "<table class='table table-sm'>";
		echo "<thead><tr><td>Control</td><td>Faculty</td><td>Dept</td><td>Title</td><td>SRCF</td></tr></thead>";
		foreach($q->result_array() as $row)
		{
			$json = json_decode($row['people'],true);
			$srcf = 0;
			foreach($json as $p)
				{
					if ($p['santa_rosa'] == 'yes')
						$srcf++;
				}

			$faculty = $this->get_poster_faculty_info($json);
			$name = "No faculty listed";
			$dept = "Other";
			if ($faculty !== false)
			{
				$name = $faculty['name'];
				$dept = $faculty['dept_display'];
			}
			
			echo "<tr>";
			echo "<td>";
			
			echo "<br/>";
			echo '<div class="btn-group align-top" role="group" aria-label="Basic example">';
			echo $row['seq'] . ": ";
			echo "<button class='btn btn-outline-primary btn-sm align-top' onclick=poster_delta('" . $row['entry_hash'] . "',-1)><i class='fa-solid fa-arrow-up'></i></button>";
			echo "<button class='btn btn-outline-primary btn-sm' onclick=poster_delta('" . $row['entry_hash'] . "',1)><i class='fa-solid fa-arrow-down'></i></button>";
			echo "<button class='btn btn-outline-primary btn-sm align-top' onclick=poster_delta('" . $row['entry_hash'] . "',-5)><i class='fa-solid fa-arrow-up'></i>5</button>";
			echo "<button class='btn btn-outline-primary btn-sm' onclick=poster_delta('" . $row['entry_hash'] . "',5)><i class='fa-solid fa-arrow-down'></i>5</button>";
			
			echo '</div>';
			echo "</td>";
			
			echo "<td>";
			echo $name;
			echo "</td>";

			echo "<td>";
			echo substr($dept,0,10);
			echo "</td>";


			echo "<td>";
			echo "<small>" . substr($row['title'],0,30) . "</small>";
			echo "</td>";

			echo "<td>";
			if ($srcf)
				echo "<b><font color=red>Yes</font></b>";
			else echo "No";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}


	function get_talk_list_rtf()
	{
		

		$year = $this->Setting->get("year");
		$rooms = $this->Setting->get("places");
		$time_groups = $this->Setting->get("time_groups");
		$tg = explode(",",$time_groups);
		echo "{\\rtf1\\ansi\\deff0";

	foreach($tg as $time_group)
		{
			foreach(explode(",",$rooms) as $room)
				{

					$q = $this->db->query("select * from entry where format='talk' and year=? and time_group=? and place=? order by seq asc",Array($year,$time_group,$room));
					echo "{\\pard \\fs44 \\qc ";
					echo "$room ($time_group)\\line \\par}";

					echo "\\row";

					$min = "30";
					foreach($q->result_array() as $row)
					{
						$json = $this->Entry->get_json($row['entry_hash']);
					
						$s = "";
						$f = "";
						$j1 = $this->find_presenter($json['people']);
						foreach($j1 as $p)
						{
							if ($p['role'] != 'Faculty')
							{
								if (!empty($p['speaker']) && $p['speaker'] == "yes")
									$s .= "{\\b " . $this->flip_names($p['name']) . "\\b0}" . ", ";
								else $s .= $this->flip_names($p['name']) . ", ";
							}
							else $f .= $this->flip_names($p['name']) . ", ";
						}
						$s = trim($s,", ");
						$f = trim($f,", ");
						$title = $this->italics_rtf($json['title']);

						echo "\\trowd";
						echo "\\cellx2500";
						echo "\\cellx9500";

						if  ($min < 10)
							$min = "0$min";
						$room = $row['place'];
						$time = $row['time'];
						echo "\\intbl $room ($time) \\cell";
					
						if (empty($s) && !empty($f)) // no students
							echo "\\intbl {\\b $f\\b0:} $title\\line \\cell";
						else if (empty($f) && !empty($s)) // no advisor
							echo "\\intbl {\\b $s:\\b0:} $title\\line \\cell";
						else echo "\\intbl $s (advisor $f): $title\\line \\cell";
						echo "\n\n";
						echo "\\row";

						$min += 15;
						$min = $min % 60;
					}
				//echo "{\\pard \\brdrb \\brdrs \\brdrw10 \\brsp20 \\par}{\\pard\\par}";
					
				}
		}
		echo "}";
	}

	function get_emails($entry_hash)
	{
		$r = "";
		$json = $this->get_json($entry_hash);
		foreach($json['people'] as $p)
			$r .= $p['email'] . "\n";
		return(trim($r));
	}

	function get_roles($entry_hash)
	{
		$student = false;
		$faculty = false;
		$json = $this->get_json($entry_hash);
		foreach($json['people'] as $p)
		{
			if ($p['role'] == 'Student')
				$student = true;
			if ($p['role'] == 'Faculty')
				$faculty = true;
		}
		return(Array('student' => $student,'faculty' => $faculty));
	}

	function get_poster_faculty_info($people)
	{
		foreach($people as $p)
		{
			if ($p['role'] != 'Faculty')
				continue;

			if (empty($p['affiliation']) || $p['affiliation'] == '--Select--')
				continue;

			if ($p['affiliation'] == 'Other...')
			{
				if (empty($p['other_affiliation']))
					continue;

				return(Array(
					'name' => $p['name'],
					'dept_display' => trim($p['other_affiliation']),
					'order_dept' => 'Other'
				));
			}

			return(Array(
				'name' => $p['name'],
				'dept_display' => $p['affiliation'],
				'order_dept' => $p['affiliation']
			));
		}

		return(false);
	}

	 public function reset_poster_order()
    {
		return; //not sure we want to do this after new poster sort by dept. 4/23/2024 at Harbor Town Outlet mall
    	if ($this->Setting->get('poster_order') == 'off' || $this->Setting->get('poster_order') == 'false' || $this->Setting->get('poster_order') === false)
    		return;
    	$year = $this->Setting->get("year");
    	$q = $this->db->query("select * from entry where format='poster' and year=? order by entry_id asc",Array($year));
    	$i = 1;

    	foreach($q->result_array() as $row)
    	{
    		$this->db->query("update entry set seq=$i where entry_hash=?",$row['entry_hash']);
    		$i++;
    	}
    }

    public function show_index($year)
    {
    	$idx = [];

    	$q = $this->db->query("select * from entry where year=? order by seq asc",Array($year));
    	foreach($q->result_array() as $row)
    	{
    		$json = json_decode($row['people'],true);
    		foreach($json as $person)
    		{
    			$name = $person['name'];
    			$line = ucfirst($row['format']) . " #" . $row['seq'];
    			if ($row['format'] == 'talk')
    			{
    				$a = explode("-",$row['time']);
    				if (strstr($a[0],":") === false)
    					$a[0] .= ":00";
    				$line .= ": (" . $row['place'] . " at " . $a[0] . ")";
    				$place = $row['place'];
    				$time = $row['time'];
    				$time_group = $row['time_group'];
    				$z = anchor("start/mobile/" . $row['format'] . "/" . $place . "/" . $time_group . "/#" . $row['entry_hash'],$line,Array("class" => "btn btn-sm btn-primary"));
    			}
    			else
    			{
    				$place = "all";
    				$time = "all";
    				$time_group = "all";
    				$z = anchor("start/mobile/" . $row['format'] . "/" . $place . "/" . $time_group . "/#" . $row['entry_hash'],$line,Array("class" => "btn btn-sm btn-info"));
    			}
    			
    			if (empty($idx[$name]))
    				$idx[$name] = $z;
    			else $idx[$name] .= ", " . $z;
    		}
    	}
  
    	ksort($idx);
    	
    	echo "<table class='table'>";
    	foreach($idx as $name => $line)
    	{
    		echo "<tr><td>";
    		echo "<strong>$name</strong>" . ": " . $line;
    		echo "</td></tr>";
    	}
    	echo "</table>";
    }

	function room_is_empty($year,$place,$time_group)
	{
		$q = $this->db->query("select * from entry where place=? and time_group=? and year=?",[$place,$time_group,$year]);
		return $q->num_rows() == 0;
	}
}


?>
