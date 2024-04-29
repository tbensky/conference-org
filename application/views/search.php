<?php
$year = $this->Setting->get("year");

echo "<div class='container'>";
echo "<div class='row'>";

echo "<center>";
echo "<h4>Full program search</h4>";

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

echo "<h1 class='mt-3'>Talks</h1>";
$q = $this->db->query("select * from entry where format='talk' and year=? order by seq asc",[$year]);
$this->Entry->list_all_search($q);

echo "<h1>Posters</h1>";
$q = $this->db->query("select * from entry where format='poster' and year=? order by seq asc",[$year]);
$this->Entry->list_all_search($q);





echo "</div>";
echo "</div>";
?>

<script>

  var nodes = document.getElementsByClassName('target');
  $("#record_count").html(nodes.length);

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