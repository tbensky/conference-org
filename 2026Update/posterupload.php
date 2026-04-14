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

function buildHumanChallenge() {
    $left = random_int(2, 9);
    $right = random_int(1, 8);

    if (random_int(0, 1) === 1) {
        return [
            'prompt' => "What is $left + $right?",
            'answer' => (string) ($left + $right),
        ];
    }

    $sum = $left + $right;
    return [
        'prompt' => "What is $sum - $right?",
        'answer' => (string) $left,
    ];
}

function resetHumanChallenge($session) {
    $challenge = buildHumanChallenge();
    $session->set_userdata('poster_human_prompt', $challenge['prompt']);
    $session->set_userdata('poster_human_answer', $challenge['answer']);
    $session->set_userdata('poster_human_started_at', time());
}

$humanPrompt = $this->session->userdata('poster_human_prompt');
if (!$this->session->userdata('poster_human_prompt') || !$this->session->userdata('poster_human_answer')) {
    resetHumanChallenge($this->session);
    $humanPrompt = $this->session->userdata('poster_human_prompt');
}

if (isset($_POST['submit'])) {
    $poster_name = trim($_POST['poster_name'] ?? '');
    $human_answer = trim($_POST['human_answer'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $expected_answer = (string) $this->session->userdata('poster_human_answer');
    $started_at = (int) $this->session->userdata('poster_human_started_at');
    $challenge_age = time() - $started_at;

    if ($website !== '') {
        echo '<i class="fa-solid fa-circle-xmark" style="color: #ff4013;"></i> Upload blocked.';
    } elseif ($poster_name === '' || $poster_name === '0') {
        echo '<i class="fa-solid fa-circle-xmark" style="color: #ff4013;"></i> Please select your poster first.';
    } elseif ($expected_answer === '' || $human_answer !== $expected_answer) {
        echo '<i class="fa-solid fa-circle-xmark" style="color: #ff4013;"></i> Human check failed. Please try again.';
    } elseif ($started_at === 0 || $challenge_age < 2 || $challenge_age > 3600) {
        echo '<i class="fa-solid fa-circle-xmark" style="color: #ff4013;"></i> Human check expired. Please try again.';
    } elseif (isset($_FILES['myfile']) && $_FILES['myfile']['error'] === UPLOAD_ERR_OK) {
        $poster_name = $_POST['poster_name'];
        $fileTmpPath = $_FILES['myfile']['tmp_name'];
        $fileName = $_FILES['myfile']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $clean = sanitizeFileName($poster_name);
        $newFileName = $clean . '.' . $fileExtension;

        // Directory where the file will be moved
        $uploadFileDir = '/var/www/conference.csm.calpoly.edu/uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the file from temp directory to your desired folder

         // Allowed types
         $allowedExtensions = ['pdf'];
         $allowedMimeTypes = ['application/pdf', 'application/x-pdf'];
         $detectedMimeType = '';
         if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detectedMimeType = finfo_file($finfo, $fileTmpPath);
                finfo_close($finfo);
            }
         } elseif (function_exists('mime_content_type')) {
            $detectedMimeType = mime_content_type($fileTmpPath);
         }
         $fileHeader = file_get_contents($fileTmpPath, false, null, 0, 5);
         $looksLikePdf = ($fileHeader === '%PDF-');

         if (in_array($fileExtension, $allowedExtensions, true) && in_array($detectedMimeType, $allowedMimeTypes, true) && $looksLikePdf) {

                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    resetHumanChallenge($this->session);
                    echo '<i class="fa-solid fa-circle-check" style="color: #77bb41;"></i> Your poster file has been successfully uploaded.';
                    echo "<ul>";
                    echo "<li> No confirmation email will be sent.  ";
                    $url = base_url() . "uploads/$newFileName";
                    echo "<li> Here's what we received (please check it): ";
                    echo "<a href=$url target='_blank'>Your poster</a>";
                    echo "<li> Link to your poster PDF: <input style='font-size: 8pt' size=100 type=text value='$url'>";
                    echo "</ul>";
                } else {
                    echo 'There was an error moving the file.';
                }
         }
         else {
            echo '<i class="fa-solid fa-circle-xmark" style="color: #ff4013;"></i> Please upload a PDF file.  <a href=https://conference.csm.calpoly.edu/index.php/start/posterupload>Try again</a>.';
         }
    } else {
        echo 'No file uploaded or upload error.';
    }

    resetHumanChallenge($this->session);

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
RET1;

echo "<br/><br/>";
echo "<h4>3. Prove you are human</h4>";
echo "<label for='human_answer'>$humanPrompt</label><br/>";
echo "<input type='text' id='human_answer' name='human_answer' autocomplete='off' required>";
echo "<div style='position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;'>";
echo "<label for='website'>Leave this field empty</label>";
echo "<input type='text' id='website' name='website' tabindex='-1' autocomplete='off'>";
echo "</div>";

echo "<br/><br/>";
echo "<h4>4. Click the upload button!</h4>";
echo "<button class='btn btn-success btn-sm' type='submit' name='submit'>Upload <i class='fa-solid fa-file-arrow-up'></i></button>";

echo "</form>";

echo "</div>";


echo "</div>";

?>
