<?php

function tex_header()
{
	print <<<END
\\documentclass[12pt]{article}
\\textwidth=6.5in
\\textheight=8in
\\topmargin=0in
\\headheight=0in
\\headsep=0in
\\footskip=0in
\\oddsidemargin=0in
\\usepackage{makeidx}
\\usepackage{epsfig}
\\usepackage{etoolbox}
\\usepackage{amsfonts}
\\usepackage{pdfpages}
\\makeindex
\\begin{document}
\\include{preamble}
%\\include{schedule}
%\\include{ack}
\\AtBeginEnvironment{quotation}{\\footnotesize}
\\patchcmd{\\quotation}{\\rightmargin}{\\leftmargin 0.05in \\rightmargin}{}{}
\\renewcommand{\indexname}{Index: Author names and {\sl page numbers}}


\\includepdf[pages=-]{schedule_pdf}
\\setcounter{page}{7}


END;
}

function fix_special($str)
{
	$ret = $str;

	$ret = str_replace("%","\\%",$ret);
	$ret = str_replace("\\\\","\\",$ret);
	$ret = str_replace("&"," and ",$ret);
	$ret = str_replace("#","\\#",$ret);
	$ret = str_replace("[[","{\\sl ",$ret);
	$ret = str_replace("]]","}",$ret);

	return($ret);
}

function tex_footer()
{
	print("\\end{document}");

}

function add_index($str,$dept)
{
	$a = explode(",",$str);

	foreach ($a as $name)
		{
			$name = ucfirst(trim($name));
			print("\\index{{$name}}\n");
		}
	print("\\index{{\bf $dept}}\n");
}

function reserved_spot($force_num,$type)
{
	// see if the program number is reserved
	$result = mysql_query("select * from special where force_num=$force_num");
	if (!mysql_num_rows($result))
		return(0);

	// if so, get the id
	$row = mysql_fetch_array($result);
	$csm_id = $row['csm_id'];

	// now check that the types match
	$row = mysql_fetch_array(mysql_query("select * from csm where csm_id=$csm_id"));
	if ($row['type'] != $type)
		return(0);
	// return the "go-ahead" id for the tex_output function
	return($row['csm_id']);
		
}

function get_type($csm_id)
{
	$row = mysql_fetch_array(mysql_query("select * from csm where csm_id=$csm_id"));
	return($row['type']);
}
	
	

function to_be_forced($csm_id)
{
	$result = mysql_query("select * from special where csm_id=$csm_id");
	return(mysql_num_rows($result));
}

function tex_entry($csm_id,$number)
{
	$row = mysql_fetch_array(mysql_query("select * from csm where csm_id=$csm_id"));

	//$title = strtolower($row[title]);
	//$title = ucfirst($title);
	$title = fix_special($row['title']);
	/*
	print("\\noindent{\\bf{\\Large [$number] }}\n");
	print("\\noindent{\\bf{\\Large $title}}\\\\\n");
	*/


	print("\\begin{minipage}{\\textwidth}\n");
	print("\\noindent{\\bf{\\large [$number] $title}}\\\\\n");


	$authors = fix_special($row['authors']);
	$affil = fix_special($row['affil']);
	$department = fix_special($row['department']);
	print("$authors\\\\");
	print("\n");
	if ($department != "School of Education")
		$department = "Department of " . $department;
	print("{\\sl $department}");
	if (!empty($affil))
		print(" {\\sl and $affil}");
	print("\n");
	add_index($row['aindex'],$row['department']);

	print("\\vspace{0.1in}\n\n");

	$ab = fix_special($row['abstract']);
	print("\\begin{quotation}");
	print("$ab\n\n");
	print("\\end{quotation}");

	print("\\vspace{0.2in}\n");

	print("\\end{minipage}\n");

	print("\n\n");
}
	

function generate_book($year)
{
	header("Content-type: application/octet-stream");
	header("Content-Disposition: inline; filename=\"csm_$year.tex\"");
	tex_header();


	// orals
	print("\\begin{center}\underline{\Huge{{\bf Oral Presentations - $year}}}\\end{center}\n\n");

	$result = mysql_query("select csm_id from csm where type='talk' and year='$year'");
	$c = 1;
	while($row = mysql_fetch_array($result))
		{
			tex_entry($row['csm_id'],$c);
			$c++;
		}

	print("\\pagebreak");

	//posters
	print("\\begin{center}\underline{\Huge{{\bf Poster Presentations - $year}}}\\end{center}\n\n");
	$result = mysql_query("select csm_id from csm where type='poster' and year='$year'");
	$id0 = Array();
	while($row = mysql_fetch_array($result))
		array_push($id0,$row['csm_id']);
	shuffle($id0);
	
	for($i=0;$i<count($id0);$i++)
		tex_entry($id0[$i],$i+1);
	/*
	$c = 1;
	while($row = mysql_fetch_array($result))
		{
			tex_entry($row['csm_id'],$c);
			$c++;
		}
	*/


	print("\\printindex");

	tex_footer();
}


?>
