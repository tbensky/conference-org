<?php

class Misc extends CI_Model 
{
	public function nav_dropdown()
	{

		$menu = site_url("start/admin");
		$seeall = site_url("start/seeall");

		echo anchor("start/admin","Return",Array("class" => "btn btn-primary btn-xs"));
		return;
		echo <<<EOT
		<div class="dropdown">
		  <button class="btn btn-warning btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action
		  <span class="caret"></span></button>
		  <ul class="dropdown-menu">
		    <li><a href="$menu">Main Menu</a></li>
		    <li><a href="$seeall">Preview all</a></li>
		  </ul>
		</div>
EOT;
	}


}


?>