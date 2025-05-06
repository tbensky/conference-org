<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Start extends CI_Controller {

	
	function __construct() 
	{
        parent::__construct();
        $this->load->model("Entry");
        $this->load->model("Book");
        $this->load->model("Misc");
        $this->load->model("Setting");
    }


	public function index()
	{
			$bounce = $this->Setting->get("bounce_to_program");
			if ($bounce == "true")
			{
					header("Location: https://conference.csm.calpoly.edu/index.php/start/mobile/menu/all/all");
					return;
			}
			$this->load->view('header');
			$this->load->view('start');
			$this->load->view('footer');
	}


	public function incoming()
	{
		$json = $this->input->post("json");
		$hash = md5($json['title'] . $json['abstract'] . time());
		$this->db->query("insert into entry values(NULL,?,?,?,?,?,?,?,?,now(),0,?,?,?)",
												Array(
														$hash,
														$json['year'],
														$json['format'],
														$json['title'],
														$json['abstract'],
														json_encode($json['people']),
														$json['talk_avail'],
														$json['poster_avail'],
														"","",""
													));

		$a = anchor("start/view/$hash","your submission link",Array('target' => '_blank'));
		$b = urlencode($a);
		$c = site_url("start/view/$hash");
		$qr = "<img src='https://quickchart.io/qr?format=png&size=300&text=" . $b . "&choe=UTF-8 title='Link to your submission' />";
		echo <<<EOT
		<div class="alert alert-success">
		<center>
		<h3>
  		<strong>Success!</strong> Your abstract has been received.
  		<br/>
  		<br/>
  		We do not send out confirmation emails.<br/>Please use this QR code or link as a record of your submisson:
  		<br/>
  		<br/>
  		$qr
  		</h3>
  		<input type=text value="$c" size=100>
  		<h3>Thank you!</h3>
  		If needed, save a screenshot of this page.  Direct any questions about your abstract to Tom Bensky at <a href=mailto:tbensky@calpoly.edu>tbensky@calpoly.edu</a>.
  		</center>
		</div>
EOT;
	}

	

	public function preview()
	{
		$json = $this->input->post("json");

		if (empty($json['people']))
			return;

		echo $this->Entry->gen_preview($json);
	}

	function admin()
	{
		$this->load->view('header');

		$this->load->view('admin');
		$this->load->view('footer');

	}

	function crud($format)
	{
		$this->load->view('header');

		$year = $this->Setting->get('year');

		$this->Entry->reset_poster_order();
		$crud = new grocery_CRUD();
		$crud->where("year = '" . $year . "'");
		if ($format != 'all')
			$crud->where("format = '" . $format . "'");
		$crud->set_table('entry');
		$crud->columns("seq","year","format","title","abstract","people","talk_avail","poster_avail");
		$crud->unset_texteditor("year","format","title","abstract","people","talk_avail","poster_avail","time_group","time","place");
		$output = $crud->render();


		$this->load->view('crud',$output);
		$this->load->view('footer');

	}

	function crud_edit($entry_hash)
	{
		$this->load->view('header');

		$crud = new grocery_CRUD();
		$crud->set_table('entry');
		$crud->where("entry_hash='$entry_hash'");
		$crud->columns("year","format","title","abstract","people","talk_avail","poster_avail");
		$crud->unset_texteditor("year","format","title","abstract","people","talk_avail","poster_avail","time_group","time","place");

		$output = $crud->render();


		$this->load->view('crud',$output);
		$this->load->view('footer');

	}

	public function view($entry_hash)
	{
		$this->load->view('header');
		$this->load->view('view',Array('entry_hash' => $entry_hash));
		$this->load->view('footer');
	}

	public function seeall()
	{
		$this->load->view('header');
		$this->load->view('seeall');
		$this->load->view('footer');
	}

	public function search($year)
	{
		$this->load->view('header');
		$this->load->view('search',['year' => $year]);
		$this->load->view('footer');
	}

	public function mobile($type,$room,$time_group)
	{
		$this->load->view('header');
		$this->load->view('mobile',Array("type" => $type,"room" => $room,"time_group" => $time_group));
		$this->load->view('footer');
	}

	public function mobile_highlight($type,$room,$time_group,$entry_hash)
	{
		$this->load->view('header');
		$this->load->view('mobile',Array("type" => $type,"room" => $room,"time_group" => $time_group,"entry_hash" => $entry_hash));
		$this->load->view('footer');
	}

	public function findemails()
	{
		$this->load->view('header');
		$this->load->view('findemails');
		$this->load->view('footer');
	}

	public function incoming_emails()
	{
		$email = $_POST;

		$this->load->view('header');
		$this->load->view('fix_emails',Array('email' => $email));
		$this->load->view('footer');
	}

	public function getemails($how)
    {
        $this->load->view('header');
        $this->load->view('getemails',Array('how' => $how));
        $this->load->view('footer');
    }


    public function book($year)
    {
    	$this->Entry->reset_poster_order();
    	header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"csm$year.tex\""); 
		$this->load->view('book',Array('year' => $year));
    }

	public function download_all()
	{
		$year = $this->Setting->get("year");
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"bcsm$year.csv\""); 
		$this->load->view('downloadall',Array('year' => $year));
	}

    public function order_talks($year)
    {
        $this->load->view('header');
        $this->load->view('order_talks',Array("year" => $year));
        $this->load->view('footer');
    }

     public function order_posters($year)
    {
        $this->load->view('header');
        $this->load->view('order_posters',["year" => $year]);
        $this->load->view('footer');
    }

     public function qrscript()
    {
        $this->load->view('header');
        $this->load->view('qrscript');
        $this->load->view('footer');
    }

     public function names_and_depts($what)
    {
    	header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"student_$what.csv\""); 
        $this->load->view('names_and_depts',Array('who' => 'Student','what' => $what));
    }

    public function order_talks_rtf()
    {
        $this->load->view('order_talks_rtf');
    }

    public function settings()
    {
        $this->load->view('header');

        $crud = new grocery_CRUD();
		$crud->set_table('setting');
		$crud->unset_texteditor("name","value");
		$output = $crud->render();

        $this->load->view('settings',$output);
        $this->load->view('footer');
    }

    public function get_talk_list($year)
    {
    	echo $this->Entry->get_talk_list($year);
    }

     public function move_talk($entry_hash,$delta)
    {
    	$q = $this->db->query("select seq from entry where entry_hash=? and format='talk'",$entry_hash);
    	$row = $q->row_array();
    	$seq = $row['seq'];
    	$new = $seq + $delta;

    	$this->db->query("update entry set seq = $new where entry_hash=? and format='talk'",$entry_hash);
    	$this->db->query("update entry set seq = $seq where seq = $new and entry_hash != ? and format='talk'",$entry_hash);
    }

     public function reset_talk_list()
    {
		$year = $this->Setting->get("year");
    	$q = $this->db->query("select * from entry where format='talk' and year=? order by entry_id asc",[$year]);
    	$i = 1;

    	foreach($q->result_array() as $row)
    	{
    		$this->db->query("update entry set seq=$i where entry_hash=?",$row['entry_hash']);
    		$i++;
    	}
    }

     public function save_tp($entry_id)
    {
    	$time = $this->input->post("time");
    	$place = $this->input->post("place");
    	$time_group = $this->input->post("time_group");
    	$this->db->query("update entry set place=?,time=?,time_group=? where entry_id=?",Array($place,$time,$time_group,$entry_id));
    }


	public function get_poster_list($year)
    {
		echo $this->Entry->get_poster_list($year);
    }

	public function move_poster($entry_hash,$delta)
    {
    	$q = $this->db->query("select seq from entry where entry_hash=? and format='poster'",$entry_hash);
    	$row = $q->row_array();
    	$seq = $row['seq'];
    	$new = $seq + $delta;

    	$this->db->query("update entry set seq = $new where entry_hash=? and format='poster'",$entry_hash);
    	$this->db->query("update entry set seq = $seq where seq = $new and entry_hash != ? and format='poster'",$entry_hash);
    }


	public function group_move_poster($year,$cmd)
    {
		$nums = explode("-",$cmd);
		$after = $nums[0];
		$to_move = [];
		for($i=1;$i<count($nums);$i++)
			$to_move[$i-1] = $nums[$i];
		$len = count($to_move);

		//take out entries to be moved
		foreach($to_move as $entry)
			$this->db->query("update entry set seq=-seq where year=? and format='poster' and seq = ?",[$year,$entry]);

		//open up the hole
		$this->db->query("update entry set seq=seq+? where year=? and format='poster' and seq > ?",[$len,$year,$after]);

		//put to move entries into the hole
		$new_seq = $after + 1;
		foreach($to_move as $entry)
		{
			$this->db->query("update entry set seq=? where year=? and format='poster' and seq = ?",[$new_seq,$year,-$entry]);
			echo $this->db->last_query() . "<br/>";
			$new_seq++;
		}

		//get them all renumbered
		$seq = 1;
		$q = $this->db->query("select * from entry where format='poster' and year=? order by seq asc",[$year]);
		foreach($q->result_array() as $row)
		{
			$this->db->query("update entry set seq=? where year=? and format='poster' and entry_hash = ?",[$seq,$year,$row['entry_hash']]);
			$seq++;
		}

	}




	public function tshirts($year)
	{
		$this->load->view('header');
        $this->load->view('tshirts',Array("year" => $year));
        $this->load->view('footer');

	}

	public function dept($year)
	{
		$this->load->view('header');
        $this->load->view('dept',Array("year" => $year));
        $this->load->view('footer');
	}


	public function concise_posters($year)
	{
		$this->load->view('header');
        $this->load->view('concise_posters',Array("year" => $year));
        $this->load->view('footer');

	}


	public function manual_order_posters($year)
	{
		$this->load->view('header');
        $this->load->view('manual_order_posters',Array("year" => $year));
        $this->load->view('footer');

	}

	public function stats($year)
	{
		$this->load->view('header');
        $this->load->view('stats',Array("year" => $year));
        $this->load->view('footer');

	}

		public function posterupload()
	{
		$this->load->view('header');
        $this->load->view('posterupload');
        $this->load->view('footer');

	}




	
}
