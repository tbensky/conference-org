<?php

$year = $this->Setting->get('year');

$logo = base_url("assets/cp_logo.png");


echo "<div class='container'>";

echo "<div id='row'>";

echo "<center><img src=$logo class='img-fluid'></center>";
echo "<p/><p/>";
echo "<h2>Poster Upload for $year BCSM Research Conference</h2>";

echo<<<intro
<h4>Upload your poster here, and we'll print it for you!</h4>
<ul>
<li> Posters will be printed in landscape format: 4ft wide by 3ft tall. 
<li> Your pdf document should be roughly proportional to the 4 x 3 ft dimensions and at high resolution. 
<li> If you are working in PowerPoint, you should manually set the slide width and height to 48 x 36 inches respectively (go to File/Page setup/Slide sized for).
<li> All posters will be delivered to Baker on Thursday morning of the conference for you to hang prior to the kickoff event.  
<li> More instructions to follow by email.
</ul>
<hr/>
intro;

function sanitizeFileName($filename) {
    // Remove path information
    $filename = basename($filename);

    // Remove anything that's not a-z, A-Z, 0-9, dot, hyphen, or underscore
    $filename = preg_replace("/[^a-zA-Z0-9\.\-\_]/", "_", $filename);

    // Optionally: limit length
    $filename = substr($filename, 0, 255);

    return $filename;
}

if (isset($_POST['submit'])) {
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] === UPLOAD_ERR_OK) {
        $poster_name = $_POST['poster_name'];
        $fileTmpPath = $_FILES['myfile']['tmp_name'];
        $fileName = $_FILES['myfile']['name'];
        $fileSize = $_FILES['myfile']['size'];
        $fileType = $_FILES['myfile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $clean = sanitizeFileName($poster_name);
        $newFileName = $clean . '.' . $fileExtension;

        // Directory where the file will be moved
        $uploadFileDir = '/var/www/conference.csm.calpoly.edu/uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the file from temp directory to your desired folder

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            echo "Your poster file has been successfully uploaded. No confirmation email will be sent.  Here's what we received (please check it): ";
            $url = base_url() . "/uploads/$newFileName";
            echo "<a href=$url target='_blank'>Your poster</a>";
            echo "<br/>";
            echo "Link to your poster PDF: <input type=text value='$url'>";
        } else {
            echo 'There was an error moving the file.';
        }
    } else {
        echo 'No file uploaded or upload error.';
    }

    echo "</div></div>";
    return;
}


$q = $this->db->query("select * from entry where year=? and format='poster'",[$year]);



$op = [];
foreach ($q->result_array() as $row)
{
    $p = json_decode($row['people'],true);
    $poster = "";
    $dept = "";
    foreach ($p as $person)
    {
        if ($person['role'] == 'Faculty')
            $dept = $person['affiliation'];
        [$last,$first] = explode(",",$person['name']);
        $poster .= $last . "_";
    }

    $poster = rtrim($poster,"_");
    $clean = preg_replace("/[[:punct:]]+/", "", $row['title']);
    $clean = preg_replace("/\p{P}+/u", "", $clean);
    $id = trim($year . "-" . $dept. "-" . $poster . "-" . substr($clean,0 ,30));
    $select = "$id,<option value=\"" . $id . "\">" . $id . "</option>";
    array_push($op,$select);
}

$url = site_url() . "/start/posterupload";
echo "<form action={$url} method='POST' enctype='multipart/form-data'>";

echo "<h4>1. Select the abstract submission for your poster</h4>";
echo "<select name='poster_name'>";
sort($op);
$op = array_merge(["0,<option>--Select your poster--</option>"],$op);
foreach($op as $x) {
    [$label,$html] = explode(",",$x);
    print($html);
}
echo "</select>";


echo<<<RET1
<br/><br/>
<h4>2. Select the poster PDF file to pair with this abstract</h4>
<input type="file" name="myfile">

<br/><br/>
<h4>3. Click the upload button!</h4>


<button class="btn btn-success btn-sm" type="submit" name="submit">Upload <i class="fa-solid fa-file-arrow-up"></i></button>
RET1;

echo "</form>";

echo "</div>";


echo "</div>";

?>