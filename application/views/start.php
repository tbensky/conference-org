<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//put ?deadline after conference URL for late submissions
$qs = $_SERVER['QUERY_STRING'];

$year = $this->Setting->get('year');
$dates = $this->Setting->get('dates');
$submission_deadline = $this->Setting->get('submission_deadline');
$deadline = $this->Setting->get('deadline');
$day_calc = $this->Setting->get('day_calc');
$poster_ws = $this->Setting->get('poster_ws');
$poster_ws_date = $this->Setting->get('poster_ws_date');
$posters_due = $this->Setting->get('posters_due');
$cover_art_submit = $this->Setting->get('cover_art_submit');
$cover_art_deadline = $this->Setting->get('cover_art_deadline');
$more_events = $this->Setting->get('more_events');
$qr = base_url() . "assets/2014_qrcode.png";


$logo = base_url("assets/cp_logo.png");

date_default_timezone_set("America/Los_Angeles");

$date1 = date_create_from_format('Y-m-d', $day_calc);

//Create a date object out of today's date:
$date2 = date_create_from_format('Y-m-d', date('Y-m-d'));

//Create a comparison of the two dates and store it in an array:
$diff = (array) date_diff($date1, $date2);
$days = $diff['days'];

if ($deadline == 'true')
	$days_msg = "($days days ago.)";
else $days_msg = "That's in $days days!";

echo <<<EOT

<p/>
<p/>
<div class="container">
<div class="row">

<style>
.list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus {
  z-index:2;
  color:#ffffff;
  background-color:#006400;
  border-color:#006400;
}
}
</style>




<div class="jumbotron">
<center><img src=$logo class="img-fluid"></center>


	
  <center>
  <h2 class="display-6 m-5 fw-bold">$year Research Conference<br/>$dates</h2>

  </center>
EOT;

if ($this->Setting->get('show_program_link') == 'true')
{
	$ret = $this->Entry->count_them();
	echo "<hr/>";
	echo "<center>";
	echo '<h3><span class="label label-info">';
	echo "Submissions: " . $ret['talks'] . " talks and " . $ret['posters'] . " posters.";
	echo "</h3></span>";
	echo "<hr/>";

	echo "<h5>";
	echo $this->Setting->get('posters_due');
	echo "</h5>";

	echo "<hr/>";

	echo "<h4>";
	echo "<a class='fs-2' href=" . $this->Setting->get('program_link') . ">" . $this->Setting->get('program_link_desc') . "</a>";
	echo "</h4>";



echo<<<QR
	<div class='row align-items-center mt-5'>
			<div class='col-3'></div>
			<div class='col-6'>
	  			<img class='img-fluid' src='$qr'>
			</div>
			<div class='col-3'></div>
			</div>
QR;
	echo "</center>";
	return;
}

$symbol_help = 'For typsetting purposes, special symbols are in the form of codes places between pairs of dollar signs (\$ and \$). Here are some examples:
	<ul class="list-group ms-5">
	<li> <b>Subscripts</b> are indicated with an underscore: For $H_2O$ type: <code>H$_2$O</code>.  For: $x_{initial}$ type: <code>x$_{initial}$</code>.</li>
	<li> <b>Superscripts</b> are indicated with a caret: For $x^2$ type: <code>x$^2$</code>. For: $y^{1+z}$ type: <code>y$^{1+z}$</code>.</li>
	<li> <b>Greek letters</b> must go between dollar signs like this: <code>$\Delta$</code> for $\Delta$, <code>$\gamma$</code> for $\gamma$ and <code>$\omega$</code> for $\omega$. More symbols are <a href=https://artofproblemsolving.com/wiki/index.php/LaTeX:Symbols target="_blank">here</a> (standard LaTeX formatting).</li>
	<li> <b>Italics</b> are done by enclosing text in <span id=brack>[[</span> and <span id=brack>]]</span>. So <code><span id=brack>[[</span>text in italics<span id=brack>]]</span></code> would render as <i>text in italics</i>.</li>
	<li> Remember that species names should be italicized.</li>
	<li> Please use the "Preview" button to be sure your abstract looks correct.
	<li> <a href=https://docs.google.com/document/d/1-ZzfNY26XZ6N5-lttdKr_xpDSy5HScZ4Ha2Y8UWASXU/edit?usp=sharing target=_blank>More information</a>.</li>
	</ul>
';


echo<<<EOT1



  <ul class="list-group mb-5">

  <li class="list-group-item active"> Important Dates</li>

<li class="list-group-item"><i class="fa-regular fa-circle-check"></i> $submission_deadline. <code>$days_msg</code></li>

<li class="list-group-item"><i class="fa-regular fa-circle-check"></i> $poster_ws</li>

<li class="list-group-item"><i class="fa-regular fa-circle-check"></i> $posters_due</li>


$more_events
</ul>


</div>


EOT1;



if ($deadline == 'true' && empty($qs))
{
	$ret = $this->Entry->count_them();
	echo "<center>";
	echo '<h3><span class="label label-info">';
	echo "The entry deadline has passed.  " . $ret['talks'] . " talks and " . $ret['posters'] . " posters were submitted.";
	echo "</h3></span>";
	echo "</center>";
	return;
}


?>

	<div class="form-group">
		<div id="content">

<h2 class="mb-3">Submit your abstract here</h2>

<h4>1. Submit a talk or poster?</h4>
<div class="row">
	<div class="col-4">
		<select onchange="update_submission()" class="form-select" id="format">
			<option value=none>--Select--</option>
			<option value=talk>Talk</option>
			<option value=poster>Poster</option>
		</select>
	</div>
</div>
<br>
<div class="text-muted">
	<ul>
		<li>Please, only one total submission per research effort.
		<li> <strong>Talks</strong> are 10 minutes in length, with 5 minutes for questions and to transition into the next speaker.
		<li> <strong>Posters</strong> should be 4 feet x 3 feet (122 cm x 91 cm) in width, height.
		</ul></div>

<br/>


<div class="form-group">
	<h4>2. What is the title of your <span id="submission_type">submission</span>?</h4>
	<input class="form-control" type=text id=title size=80 placeholder="Type your title here"></input>
	<button data-bs-toggle="collapse" data-bs-target="#format_help_title" class="btn btn-sm btn-warning mt-1">Symbol help&nbsp;<i class="fa-solid fa-subscript"></i></button>
	<br/>
<div id="format_help_title" class="collapse">
	<?php echo $symbol_help; ?>

</div>
</div>


<div class="form-group mt-5">
<h4>3. People involved</h4>
<small class="text-muted">All submissions must have at least 1 student presenter and 1 faculty mentor.  <strong>Student presenters include any student author who will attend the conference in person to present their poster or give a talk.</strong></small>
<table id="people_table" class="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Affiliation</th>
			<th>Role</th>
			<th>Presenter</th>
			<th>Email</th>
			<!--
				<th>T-shirt size&nbsp;<a href=https://www.apparelvideos.com/cs/CatalogBrowser?todo=mm&productId=NL6210 target=_blank><i style="color: black;" class="fa-solid fa-circle-info"></i></a></th>
			
			<th>Frost Support</th>
			<th>Santa Rosa Creek Fdn. Support</th>
			-->
			<th>Support</th>
			<th>Previous Scientific<br/>Conference Experience?</th>
		</tr>
	</thead>
	<tbody>
		<span id="people"></span>
	</tbody>
</table>

<button class="btn btn-sm btn-info" onclick="add_person()" id="add_link">Add  person&nbsp;<i class="fa-regular fa-user-plus"></i></button>
</div>



<br/>



<div class="form-group">
<h4 class="mt-5">4. Abstract</h4>
<textarea id=abstract class="form-control" rows=10 cols=90 placeholder="Type your abstract here.  No titles or author names here. Keep it to around 200 words long please.">
</textarea>
<br/>
Words: <span id="display_count">0</span> | Remaining: <span id="word_left">0</span>
<br/>
<button type="button" class="btn btn-info btn-sm" onClick="go('preview')">Preview&nbsp;<i class="fa-solid fa-magnifying-glass"></i></button>
<button data-bs-toggle="collapse" data-bs-target="#format_help" class="btn btn-sm btn-warning">Symbol help&nbsp;<i class="fa-solid fa-subscript"></i></button>

<p/>
<div id="format_help" class="collapse">
<?php echo $symbol_help; ?>
</div>


<br/>

<div class="form-group">
<h4>5. Scheduling</h4>
<div id="other_notes"><i>--Select "Talk" or "Poster" first (see #1).--</i></div>
<p/>
</div>

<div class="form-group">
<h4>6. Disclaimer</h4>
<blockquote>I agree to Cal Poly's video/audio image release form, which means that I grant permission to Cal Poly San Luis Obispo and its employees and agents to use my visual/audio content, which includes, but is not limited to, any type of recording, photographs, digital images, drawings, renderings, voices, sounds, video recordings, audio clips, concept ideas and any accompanying written descriptions. <a href=https://drive.google.com/file/d/1eTYcYouL2rXaS6qOhQszEY7oVJpwSd1D/view?usp=sharing target=_blank>Read the full release form</a>.
</blockquote>
<input type=checkbox id=disclaimer> Please check this box indicating you read the disclaimer. 

<p/>
</div>


<br/>


<div class="form-group">
<h4>7. All done! Click to submit.</h4>

<div id="emailhelp" class="collapse">
	<ul>
		<li> General conference question may be directed to Dena Grossenbacher at <a href=mailto:dgrossen@calpoly.edu>dgrossen@calpoly.edu</a>.  
		<li> Abstract submission questions may be directed to Tom Bensky at <a href=mailto:tbensky@calpoly.edu>tbensky@calpoly.edu</a>.
	</ul>
</div>

<br/>
<div id="submit_panel">
<button type="button" class="btn btn-sm btn-info" onClick="go('preview')">Preview&nbsp;<i class="fa-solid fa-magnifying-glass"></i></button>
<button class="btn btn-sm btn-success" onclick="go('submit')">Submit&nbsp;<i class="fa-solid fa-arrow-up-right-from-square"></i></button>
<button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#emailhelp" aria-expanded="false" aria-controls="collapseExample">Need help&nbsp;<i class="fa-solid fa-circle-question"></i></button>
</div>
</div>



<br/>
<br/>



</div>
</div>
</div>


<!-- Modal -->
  <div class="modal fade" id="preview_dialog" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Abstract Preview</h4>
        </div>
        <div class="modal-body">
        	<span id="preview_content"></span>
        	<hr/>
        	<div class="text-right">
        	Not looking right? <a href=https://docs.google.com/document/d/1-ZzfNY26XZ6N5-lttdKr_xpDSy5HScZ4Ha2Y8UWASXU/edit?usp=sharing target=_blank>Click here</a>.
        	</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        </div>
      </div>

  </div>
    </div>
  </div>


<script>

var people_count = 0;
var preview = false;

$(document).ready(function()
{
	var maxc = 300;
var wordCounts = {};
$("#abstract").keyup(function() {
    var matches = this.value.match(/\b/g);
    wordCounts[this.id] = matches ? matches.length / 2 : 0;
    var finalCount = 0;
    $.each(wordCounts, function(k, v) {
        finalCount += v;
    });
	
	 if (finalCount > maxc) {
      // Split the string on first 200 words and rejoin on spaces
      var trimmed = $(this).val().split(/\s+/, maxc).join(" ");
      // Add a space at the end to make sure more typing creates new words
      $(this).val(trimmed + " ");
    }
    else {
      $('#display_count').text(finalCount);
      $('#word_left').text(maxc-finalCount);
    }
    $('#display_count').html(finalCount);
    //am_cal(finalCount);
}).keyup();

add_person();

//$('#preview_dialog').modal('show')

$('#preview_dialog').on('shown.bs.modal', function (e) {
  go('preview');
})
}); 



function update_submission()
{
	x = $('#format').val();
	
	switch(x)
	{
		case 'talk':
			var html = '<p/>';
			html += 'To help us schedule your talk, please select your available times on Friday of the conference:<br/>';
			html += '<div id="note">(** Please talk to your faculty advisor about their availablity too, since they like to attend these talks! **)</div><p/>';
			html += '<input class=talk_avail type=checkbox id=talk_avail value="9:00am-10:30am"> 9:00am - 10:30am<br/>';
			//html += '<input class=talk_avail type=checkbox id=talk_avail value="10:30am-12:00pm"> 10:30am - 12:00pm<br/>';
			html += '<input class=talk_avail type=checkbox id=talk_avail value="1:00pm-3:00pm"> 1:00pm - 3:00pm<br/>';
			//html += '<input class=talk_avail type=checkbox id=talk_avail value="2:30pm-4:00pm"> 2:30pm - 4:00pm<br/>';
			$('#other_notes').html(html);
			$('#submission_type').html("talk");
			break;
		case 'poster':
			var html = '<p/>';
			html += 'A poster workshop is offered each year, to help you make your poster.  Please help us to organize this:'
			html += '<div id="note">(Note: Posters should be 4\' wide by 3\' tall.  For the workshop meet in Fisher Science (building 33), outside the Biology Museum, 33-285.)</div>';
			html += '<p/>';
			html += '<input class=poster_avail type=checkbox id=poster_avail value="We will need to have a poster printed"> We will need to have a poster printed<br/>';
			html += '<input class=poster_avail type=checkbox id=poster_avail value="We have one already"> We already have a poster used in another conference<br/>';
			html += '<input class=poster_avail type=checkbox id=poster_avail value="We know how to make one"> We know how to make a poster, and do not need to attend the workshop.<br/>';
			html += '<input class=poster_avail type=checkbox id=poster_avail value="We can attend <?php echo $poster_ws_date; ?>"> We can attend a poster workshop on <?php echo $poster_ws_date; ?><br/>';
			html += '<input class=poster_avail type=checkbox id=poster_avail value="We would like to attend a workshop but CANNOT attend on <?php echo $poster_ws_date; ?>">We would like to attend a workshop but CANNOT attend on <?php echo $poster_ws_date; ?><br/>';

			$('#other_notes').html(html);
			$('#submission_type').html("poster");
			break;
		case 'none':
			$('#other_notes').html('');
			$('#submission_type').html("submission");
			break;
		}
}
			
			

function get_prefix(x,id)
{
	return('people_' + x + '_' + id);
}

function get_value(x,_id)
{
	var id = '#people_' + x + '_' + _id;

	//checkboxes
	if (_id == 'attended_sci_conf' || 
		_id == 'presented_sci_conf' ||
		_id == 'frost_scholar' || 
		_id == 'santa_rosa')
		{
			if ($(id).is(":checked"))
				return("yes");
			return("no");
		}

	return($(id).val());
}

function add_person()
{
	var html;
	
	html = '<tr id="people_' + people_count + '"><td>';
	html += '<input id=' + get_prefix(people_count,'name') + ' size=20 class="form-control form-control-sm" placeholder="Last, First"></input>';
	html += '</td>';
	
	html += '<td>';
	html += '<select class="form-control form-select-sm" onchange="affiliation_change(' + people_count + ');" id=' + get_prefix(people_count,'affiliation') + '>';
	
	html += '<option>--Select--</option>';
	html += '<option>Biological Sciences</option>';
	html += '<option>Chemistry and Biochemistry</option>';
	html += '<option>Mathematics</option>';
	html += '<option>Physics</option>';
	html += '<option>Kinesiology and Public Health</option>';
	html += '<option>Statistics</option>';
	html += '<option>School of Education</option>';
	html += '<option>Liberal Studies</option>';
	html += '<option>Other...</option>';
	html += '</select>';
	html += '<br/>';
	html += '<div style="display: none; visibility: hidden;" id=' + get_prefix(people_count,'other_affil') + '>Other: <input id=' + get_prefix(people_count,'other_affiliation') + '></input></div>';
	html += '</td>';

	var speaker_select_id = get_prefix(people_count,'speaker');
	var email_id = get_prefix(people_count,'email');
	var role = get_prefix(people_count,'faculty_rep');
	var tshirt_id = get_prefix(people_count,'tshirt');
	
	html += '<td>';
	html += `<select class="form-control form-select-sm" id="${role}" onChange="check_presenter('${speaker_select_id}','${email_id}','${role}','${tshirt_id}')">`;
	html += '<option>--Select--</option>';
	html += '<option>Faculty</option>';
	html += '<option>Student</option>';
	html += '<option>Other</option>';
	html += '</select>';
	html += '</td>';

	html += '<td>';
	html += `<select class="form-control form-select-sm" id="${speaker_select_id}" onChange="check_presenter('${speaker_select_id}','${email_id}','${role}','${tshirt_id}')">`;
	html += '<option>--Select--</option>';
	html += '<option value=yes>Yes</option>';
	html += '<option value=no>No</option>';
	html += '</select>';
	html += '</td>';

	html += '<td>';
	html += `<input id="${email_id}" size=20 class="form-control form-control-sm"></input>`;
	html += '</td>';

	//T-shirt sizes not needed 11-Dec-2023

	html += `<input type=hidden id="${tshirt_id}" value="none">`;
	
	/*
	html += '<td>';
	html += `<select class="form-control" id="${tshirt_id}" onChange="check_presenter('${speaker_select_id}','${email_id}','${role}'),'${tshirt_id}'">`;
	html += '<option value=select_default>--Select--</option>';
	html += '<option value=xs>XS</option>';
	html += '<option value=sm>S</option>';
	html += '<option value=med>M</option>';
	html += '<option value=large>L</option>';
	html += '<option value=xl>XL</option>';
	html += '<option value=2xl>2XL</option>';
	html += '<option value=3xl>3XL</option>';
	html += '<option value=4xl>4XL</option>';
	html += '<option value=not_presenting>Not presenting</option>';
	html += '</select>';
	html += '</td>';
	*/


	//support
	html += `<td>

	<div class="dropdown">
		<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="support_dropdown" data-bs-toggle="dropdown" aria-expanded="false">
			Select
		</button>
			<ul class="dropdown-menu p-1" aria-labelledby="support_dropdown">
				<li><small><input type=checkbox id=${get_prefix(people_count,'frost_scholar')}> Frost Support</small>
				<li><small><input type=checkbox id=${get_prefix(people_count,'santa_rosa')}> Santa Rosa Creek Foundation</small>
			</ul>
		</div>
	</td>`;

	
	/*
	html += '<td>';
	html += '<select class="form-control form-select-sm" id=' + get_prefix(people_count,'frost_scholar') + '>';
	html += '<option>--Select--</option>';
	html += '<option value=yes>Yes</option>';
	html += '<option value=no>No</option>';
	html += '</select>';
	html += '</td>';


	html += '<td>';
	html += '<select class="form-control form-select-sm" id=' + get_prefix(people_count,'santa_rosa') + '>';
	html += '<option>--Select--</option>';
	html += '<option value=yes>Yes</option>';
	html += '<option value=no>No</option>';
	html += '</select>';
	html += '</td>';
	*/

	//previous conferences

	html += `<td>

	<div class="dropdown">
		<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="experience_dropdown" data-bs-toggle="dropdown" aria-expanded="false">
			Select
		</button>
			<ul class="dropdown-menu p-1" aria-labelledby="experience_dropdown">
				<li><small><input type=checkbox id=${get_prefix(people_count,'attended_sci_conf')}> Attended one</small>
				<li><small><input type=checkbox id=${get_prefix(people_count,'presented_sci_conf')}> Presented at one</small>
			</ul>
		</div>
	</td>`;



	
	html += '<td>';
	html += '<i style="color: #ff5555;" class="fa-regular fa-circle-xmark" onclick="remove_person(' + people_count + ');"></i>';
	html += '</td></tr>';
	
	people_count++;
	$('#pc').html(people_count);
	$('#people_table').append(html);
}

function check_presenter(speaker_select_id,email_id,role_id,tshirt_id)
{
	var speaker_yn =$(`#${speaker_select_id}`).val();
	var role = $(`#${role_id}`).val();

	if (role == 'Faculty' || speaker_yn == 'yes')
	{
		$(`#${email_id}`).attr("disabled",false);
		$(`#${email_id}`).val("");
		$(`#${email_id}`).attr("placeholder","Type email address here");

		//$(`#${tshirt_id}`).attr("disabled",false);
		//$(`#${tshirt_id}`).val("select_default");
	}
	else 
	{
		$(`#${email_id}`).attr("disabled",true);
		$(`#${email_id}`).val("Not needed");

		//$(`#${tshirt_id}`).attr("disabled",true);
		//$(`#${tshirt_id}`).val("not_presenting");
	}
}


function remove_person(x)
{
	var id='#people_' + x
	$(id).remove();
	people_count--;
	$('#pc').html(people_count);
	return;
}

function affiliation_change(people_count)
{	
	var id = '#' + get_prefix(people_count,'affiliation');
	var val = $(id + ' :selected').val();
	
	if (val == "Other...")
		$('#' + get_prefix(people_count,'other_affil')).css('visibility','visible').css('display','block');
	else $('#' + get_prefix(people_count,'other_affil')).css('visibility','hidden').css('display','none');
}

function has_unicode(str) 
{
    for (var i = 0; i < str.length; i++) 
    {
        if (str.charCodeAt(i) > 127)
        {
        	console.log('unicode: '+str[i]);
        	return true;
        } 
    }
    return false;
}

function find_unicode (str) 
{
    for (var i = 0; i < str.length; i++) 
    {
        if (str.charCodeAt(i) > 127)
        {
        	var st = i-5;
        	var ed = i+5;
        	if (st < 0)
        		st = 0;
        	if (ed > str.length-1)
        		ed = str.length-1;
        	var s;
        	s = '';
        	for(j=st;j<ed;j++)
        		s = s + str[j];
        	return("The problem is with the " + str[i]+ ' in ' + s);
        } 
    }
}


function empty(str) {
    return (!str || 0 === str.length);
}

// https://stackoverflow.com/questions/46155/how-to-validate-an-email-address-in-javascript
function is_email(email) 
{
	return(true);
	/*
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
	*/
}


function go(action)
{
	var format = $('#format').val();
	var abstract = $('#abstract').val();
	var title = $('#title').val();
	var i;
	var people = [];
	var url = '<?php echo site_url(); ?>' + "/start/incoming";
	var urlp = '<?php echo site_url(); ?>' + "/start/preview";
	var spinner = '<img src="<?php echo base_url(); ?>/assets/submit_spinner.gif">';
	var year = '<?php echo $year; ?>';


		var disc = $('#disclaimer').is(":checked");

		


	if (format != 'talk' && format != 'poster')
	{
		alert('Please select a format for your submission (talk or poster).');
		return;
	}

	if (people_count == 0)
	{
		alert("Please add some people to your submission.");
		return;
	}

	if (empty(title) || title.length < 10)
	{
		alert("Your title is too short.");
		return;
	}

	if (empty(abstract) || abstract.length < 10)
	{
		alert("Your title is too short.");
		return;
	}

	if (has_unicode(abstract))
	{
		uni = find_unicode(abstract);
		alert("Your abstract has special characters in it that we can't typeset.\n\n"+uni+"\n\nIf you pasted in your abstract from Word, please edit these characters directly in the abstract box here.  Also, see the help button.");
		return;
	}
	
	if (has_unicode(title))
	{
		uni = find_unicode(title);
		alert("Your title has special characters in it that we can't typeset..\n\n"+uni+"\n\nIf you pasted in your abstract from Word, please edit these characters directly in the title box here.  Also, see the help button.");
		return;
	}


	var name_ok = true;
	var email_ok = true;
	var has_student = false;
	var has_faculty = false;
	for(i=0;i<people_count;i++)
	{
		var name = get_value(i,'name');
		if (name.length < 2 || name.indexOf(",") == -1)
			name_ok = false;
		var email = get_value(i,'email');
		if (!is_email(email))
			email_ok = false;
		var affil = get_value(i,'affiliation');
		var other_affil = get_value(i,'other_affiliation');
		var rep = get_value(i,'faculty_rep');
		var speaker = get_value(i,'speaker');
		var frost = get_value(i,'frost_scholar');
		var santa_rosa = get_value(i,'santa_rosa');
		var attended_sci_conf = get_value(i,'attended_sci_conf');
		var presented_sci_conf = get_value(i,'presented_sci_conf');
		
		//console.log(`attended=${attended_sci_conf},presented=${presented_sci_conf}`);

		//var tshirt = get_value(i,'tshirt');
		//var line = {'name': name,'email': email,'affiliation': affil,'other_affiliation': other_affil, 'role': rep, 'speaker': speaker, 'frost':frost,'tshirt':tshirt};
		var line = {'name': name,
					'email': email,
					'affiliation': affil,
					'other_affiliation': other_affil, 
					'role': rep, 
					'speaker': speaker, 
					'frost':frost,
					'santa_rosa': santa_rosa,
					'attended_sci_conf': attended_sci_conf,
					'presented_sci_conf': presented_sci_conf		
				};

		if (rep == 'Student')
			has_student = true;
		if (rep == 'Faculty')
			has_faculty = true;
		people.push(line);
		console.log(line);
	}

	if (has_student == false || has_faculty == false)
	{
		alert('Your entry must have at least one faculty and one student involved.');
		return;

	}



	if (people_count != people.length)
	{
		alert('Something is wrong with the list of people you are trying to submit.  Are '+people_count+' or '+people.length+' people involved in this work?');
		return;

	}

	if (name_ok == false)
	{
		alert('A person\'s name is too short, or you didn\'t separate their last and first name with a single comma.');
		return;
	}


	if (email_ok == false)
	{
		alert('Check the email addresses for people you listed.  At least one is not valid.');
		return;
	}


	var talk_avail = $('.talk_avail:checkbox:checked').map(
																function() {
															    return this.value;
																}).get().join(",");
	var poster_avail = $('.poster_avail:checkbox:checked').map(
																function() {
															    return this.value;
															    }).get().join(",");
	
	
	var entry = {
					'year': year,
					'format': format,
					'title': title,
					'abstract': abstract,
					'people': people,
					'talk_avail': talk_avail,
					'poster_avail': poster_avail,
					'time': '',
					'place': '',
					'time_group': ''
				};

	console.log(entry);

	switch(action)
	{
		case 'submit':
					if (preview == false)
					{
						alert("Please preview your submission first, to be sure it looks right!");
						return;
					}
					if (disc == false)
					{
						alert("Please indicate that you've read the disclaimer.");
						return;
					}
					$('#submit_panel').html(spinner);
					$.ajax({
							  type: "POST",
							  url: url,
							  data: {json: entry},
							  success: function(ret)
							  					{
							  						$('#content').html(ret);
							  					}
							});
					break;
		case 'preview':
		default:
					preview = true;
					$.ajax({
							  type: "POST",
							  url: urlp,
							  data: {json: entry},
							  success: function(ret)
							  					{
							  						console.log(ret);
							  						$('#preview_content').html(ret);
							  						MathJax.typeset();
							  						$('#preview_dialog').modal('show');
		
							  					}
							});
					break;
	}

	//preview


	
}

</script>



