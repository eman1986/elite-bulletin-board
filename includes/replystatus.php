<?php
define('IN_EBB', true);
/*
Filename: replystatus.php
Last Modified: 7/21/2008

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "../config.php";
include "../header.php";

#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	die($txt['notid']);
}else{
	$tid = var_cleanup($_GET['tid']); 
}
#get current time.
$curtime = time(); // add - 30 to it if needed later.

#get last updated time infor from requested topic. 
$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE tid='$tid'";
$replyStat = $db->result();
$topic_chk = $db->num_results();
$db->close();

#see if topic exist first.
if($topic_chk == 0){
	die($viewtopic['doesntexist']);
}else{
	#see if someone beat this person to the punch.
	if($replyStat['last_update'] >= $curtime){
		echo  $replyStat['Posted_User']. "&nbsp;has posted a <a href=\"viewtopic.php?$replyStat[Post_Link]\" target=\"_blank\">reply</a> to this topic.";
	}else{
		//nothing yet...check back later....	
	}
}
?>
