<?php

header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"talks.rtf\""); 

echo $this->Entry->get_talk_list_rtf();

?>