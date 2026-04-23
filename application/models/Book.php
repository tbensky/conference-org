<?php

class Book extends CI_Model 
{

	public function tex_header()
	{
		$preamble = $this->Setting->get('preamble');
		$ack = $this->Setting->get('acknowledgments');
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
			\\usepackage{url}
			\\makeindex
			\\begin{document}
			$preamble
			%\\include{preamble}
			%\\include{schedule}
			$ack
			%\\include{ack}
			\\AtBeginEnvironment{quotation}{\\footnotesize}
			\\patchcmd{\\quotation}{\\rightmargin}{\\leftmargin 0.05in \\rightmargin}{}{}
			\\renewcommand{\indexname}{Index: Author names and {\sl page numbers}}


			\\includepdf[pages=-]{talks_schedule.pdf}
			%\\setcounter{page}{7}


END;
	}

	public function fix_special($str)
	{
		$ret = $str;

		$ret = str_replace("&dagger;","\\dagger",$ret);

		$ret = str_replace("\\text","\\textrm",$ret);

		$ret = str_replace("%","\\%",$ret);
		$ret = str_replace("\\\\","\\",$ret);
		$ret = str_replace("&","\&",$ret);
		$ret = str_replace("#","\\#",$ret);
		//$ret = str_replace("$","\\$",$ret);
		$ret = str_replace("[[","{\\sl ",$ret);
		$ret = str_replace("]]","}",$ret);

		$ret = str_replace("<sup>","$^{",$ret);
		$ret = str_replace("</sup>","}$",$ret);


		$ret = str_replace("<i>","{\\sl ",$ret);
		$ret = str_replace("</i>","}",$ret);
		
		return($ret);
	}

public function tex_footer()
{
	print("\\end{document}");

}

public function add_index($str,$dept)
{
	$str = $this->fix_special($str);
	$dept = $this->fix_special($dept);
	print("\\index{{$str}}\n");
	print("\\index{{\bf $dept}}\n");
}

public function reserved_spot($force_num,$type)
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

public function get_type($csm_id)
{
	$row = mysql_fetch_array(mysql_query("select * from csm where csm_id=$csm_id"));
	return($row['type']);
}
	
	

public function to_be_forced($csm_id)
{
	$result = mysql_query("select * from special where csm_id=$csm_id");
	return(mysql_num_rows($result));
}

public function tex_entry($entry,$number)
{
	$title = $this->fix_special($entry['title']);
	$location = "";
	if ($entry['format'] == 'poster' && !empty($entry['place']))
		$location = " - " . $this->fix_special($entry['place']);
	
	print("\\begin{minipage}{\\textwidth}\n");
	print("\\noindent{\\bf{\\large [$number$location] $title}}\\\\\n");


	$authors = $this->fix_special($entry['people']);
	$affil = $this->fix_special($entry['affil']);
	
	print("$authors\\\\");
	print("\n");
	print(" {\\sl $affil}");
	print("\n");
	
	foreach($entry['person_and_affil'] as $person)
	{ 
		$this->add_index($person['name'],$person['affil']);
	}

	print("\\vspace{0.1in}\n\n");

	if (in_array($this->Setting->get('include_abstract'),Array('yes','true')))
	{
		$ab = $this->fix_special($entry['abstract']);
		print("\\begin{quotation}");
		print("$ab\n\n");
		print("\\end{quotation}");
	}	

	print("\\vspace{0.2in}\n");

	print("\\end{minipage}\n");

	print("\n\n");
}

public function tex_entry_qr($entry,$number)
{
	$title = $this->fix_special($entry['title']);
	$location = "";
	if ($entry['format'] == 'poster' && !empty($entry['place']))
		$location = " - " . $this->fix_special($entry['place']);
	
	print("\\begin{minipage}[b]{0.90\\textwidth}\n");
	print("\\noindent{\\bf{\\large [$number$location] $title}}\\\\\n");


	$authors = $this->fix_special($entry['people']);
	$affil = $this->fix_special($entry['affil']);
	
	print("$authors\\\\");
	print("\n");
	print(" {\\sl $affil}");
	print("\n");
	
	foreach($entry['person_and_affil'] as $person)
	{ 
		$this->add_index($person['name'],$person['affil']);
	}

	print("\\vspace{0.1in}\n\n");

	print("\\vspace{0.1in}\n");
	print("\\end{minipage}\n");
	print("\\hfill\n");

	print("\\begin{minipage}[b]{0.10\\textwidth}\n");
	echo "\\includegraphics{QR/" . $entry['entry_hash'] . ".png}\n";
	echo "\\vspace{0.1in}\n";
	echo "\\end{minipage}\n";;

	print("\n\n");
}
	

public function generate_book($year)
{

	$this->tex_header();

	// orals
	print("\\begin{center}\underline{\Huge{{\bf Oral Presentations - $year}}}\\end{center}\n\n");

	$q = $this->db->query("select * from entry where format='talk' and year='$year' order by seq asc");
	$c = 1;
	foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			$entry = $this->Entry->get_entry_parts($json);
			if (in_array($this->Setting->get('include_qr'),Array('yes','true')))
					$this->tex_entry_qr($entry,$row['seq']);
			else $this->tex_entry($entry,$row['seq']);
			$c++;
		}
	print("\\pagebreak");

	//posters
	// orals
	print("\\begin{center}\underline{\Huge{{\bf Poster Presentations - $year}}}\\end{center}\n\n");

	$q = $this->db->query("select * from entry where format='poster' and year='$year' order by seq asc");
	$c = 1;
	foreach($q->result_array() as $row)
		{
			$json = $this->Entry->get_json($row['entry_hash']);
			$entry = $this->Entry->get_entry_parts($json);

			if (in_array($this->Setting->get('include_qr'),Array('yes','true')))
					$this->tex_entry_qr($entry,$row['seq']);
			else $this->tex_entry($entry,$row['seq']);
			$c++;
		}
	print("\\pagebreak");

	print("\\printindex");

	$this->tex_footer();
}


}

?>
