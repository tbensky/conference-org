<?php

$year = $this->Setting->get('year');

$logo = base_url("assets/cp_logo.png");


echo "<div class='container'>";

echo "<div id='row'>";

echo "<center><img src=$logo class='img-fluid'></center>";
echo "<p/><p/>";
echo "<h2>Poster Upload for $year conference</h2>";

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
            echo "Your poster file has been successfully uploaded.  Here's what we received (please check it): ";
            $url = base_url() . "/uploads/$newFileName";
            echo "<a href=$url target='_blank'>Your poster</a>";
        } else {
            echo 'There was an error moving the file.';
        }
    } else {
        echo 'No file uploaded or upload error.';
    }

    echo "</div></div>";
    return;
}

echo<<<RET
Please create a PDF of your poster and upload it here for printing.
RET;


echo "<p/><p/>";


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
    $id = trim($year . "-" . $dept. "-" . $poster . "-" . substr($clean,0 ,20));
    $select = "$id,<option value=\"" . $id . "\">" . $id . "</option>";
    array_push($op,$select);
}

$url = site_url() . "/start/posterupload";
echo "<form action={$url} method='POST' enctype='multipart/form-data'>";

echo "<h4>Select your poster</h4>";
echo "<select name='poster_name'>";
sort($op);
$op = array_merge(["0,<option>--Select your poster--</option>"],$op);
foreach($op as $x) {
    [$label,$html] = explode(",",$x);
    print($html);
}
echo "</select>";

echo "<p/><p/>";

echo<<<RET1
<h4>Select your poster PDF file for uploading</h4>
<input type="file" name="myfile">
<button class="btn-success" type="submit" name="submit">Upload</button>
RET1;

echo "</form>";

echo "</div>";


echo "</div>";

?>