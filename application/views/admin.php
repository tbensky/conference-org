<?php

$year = $this->Setting->get("year");

echo "<div class='container'>";
echo "<div class='row'>";


echo "<h2>CSM Research Conference - Admin Page</h2>";

echo "<ul class=\"list-group\">";

echo "<li class=\"list-group-item\">" .  "View: ";
echo "<ul>";
echo "<li>" . anchor("start/crud/all","All submissions");
echo "<li>" . anchor("start/crud/talk","Talks only");
echo "<li>" . anchor("start/crud/poster","Posters only");
echo "</ul>";

echo "<li class=\"list-group-item\">" . anchor("start/seeall","Preview all entries");

echo "<li class=\"list-group-item\">" . anchor("start/mobile/menu/all/all","Mobile program");


echo "<li class=\"list-group-item\">" . "Emails: ";
echo "<ul>";
echo "<li>" . anchor("start/getemails/talks","All talks");
echo "<li>" . anchor("start/getemails/posters","All posters");
echo "<li>" . anchor("start/getemails/posters_to_print","Posters that need printing"); 
echo "<li>" . anchor("start/getemails/students_giving_talks","Students giving talks (without faculty advisors)"); 
echo "<li>" . anchor("start/getemails/students_giving_posters","Students giving posters (without faculty advisors)"); 
echo "<li>" . anchor("start/getemails/students_giving_posters_not_printed","Students giving posters that may need to be printed (without faculty advisors)"); 
echo "<li>" . anchor("start/getemails/cp_fac_only","Cal Poly Faculty supervisors only (of talks or posters)"); 

echo "</ul>";


echo "<li class=\"list-group-item\">" . anchor("start/tshirts/$year","Get T-shirt sizes");

echo "<li class=\"list-group-item\">" . anchor("start/order_talks/$year","Order the talks") . " (Load " . anchor("start/order_talks_rtf","this file") . " into Word when done.)";;

echo "<li class=\"list-group-item\"> Student names and departments for: " . anchor("start/names_and_depts/poster","Posters") . ", " . anchor("start/names_and_depts/talk","Talks");

echo "<li class=\"list-group-item\">" . anchor("start/book/$year","Generate book");

echo "<li class=\"list-group-item\">" . anchor("start/settings","Settings");
echo "<ul>";
echo "<li> dates=[string for landing page dates] like: Thursday and Friday, May 18 and 19";
echo "<li> submission_deadline=[submission deadline] like: Friday April 15: Registration/Abstract submission deadline.";
echo "<li> day_calc=[YYYY-MM-DD] like 2022-04-15 for time left calculator.";
echo "<li> poster_ws=[poster workshop string] like: Tuesday, April 19, 5-6pm: Poster workshop";
echo "<li> poster_ws_date=[poster workshop date] like: Tuesday, April 19, 5-6pm (i.e. just the date, no text for check-box use)";
echo "<li> posters_due=[posters due date] like: Monday May 2 by 9am: Posters due for printing (printing and sumbitting instructions coming soon!)";
echo "<li> cover_art_submit=[link] Link to cover art submission";
echo "<li> cover_art_deadline=Message on submission deadline to go right after link.";
echo "<li> deadline=[true,false] (put <b>?deadline</b> after URL for late entries)";
echo "<li> poster_order=[on,off]";
echo "<li> show_program_link=[true,false]";
echo "<li> program_link=[link to program] and program_link_desc=[description of program]";
echo "<li> show_mobile_button=[true,false]";
echo "<li> places=comma list of rooms talks will be held in";
echo "<li> times=comma list of possible talk times (like 9-9:15,9:15-9:30, etc)";
echo "<li> times_groups=comma delimited list of time spans of talks (should encompass those in times setting above, like 9-11,1-3)";
echo "<li> summary=text of conference summary/overview";
echo "<li> more_events=HTML code to appear under main date box on page header";
echo "</ul>";




echo "<li class=\"list-group-item\">" . anchor("start/qrscript","Generate QR Script");

echo "</ul>";

echo "</div>";
echo "</div>";

?>