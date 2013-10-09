<?php
define('IN_EBB', true);
/*
Filename: print.php
Last Modified: 5/22/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
//see if board is offline.
if ($board_status == 0){
	$offline_msg = nl2br($off_msg);
	$error = $offline_msg;
	if ($access_level == 1){
		$error .= "<p class=\"td\">[<a href=\"acp/index.php\">$menu[cp]</a>]</p>";
	}else{
		$error .= "<p class=\"td\">[<a href=\"login.php\">$txt[login]</a>]</p>"; 
	}
	echo error($error, "general");
	#terminate program after message appears.
	exit();
}
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	die($txt['nobid']);
}else{
	$bid = var_cleanup($_GET['bid']); 
}
#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	die($txt['notid']);
}else{
	$tid = var_cleanup($_GET['tid']); 
}
//topic & board query.
$db->run = "select Original_Date, Body, Topic, author FROM ebb_topics WHERE tid='$tid'";
$checkboard = $db->num_results();
$t_name = $db->result();
$db->close();
$db->run = "select id FROM ebb_boards WHERE id='$bid'";
$checktopic = $db->num_results();
$db->close();
//check to see if topic exists or not and if it doesn't kill the program.
if (($checkboard == 0) or ($checktopic == 0)){
	$error = $viewtopic['doesntexist'];
	echo error($error, "error");
}
//convert over any thing extra in the topic.
$string = $t_name['Body'];
$msg = $string;
$msg = smiles($msg);
$msg = BBCode_print($msg);
$msg = language_filter($msg, 1);
$msg = nl2br($msg);
//get topic details.
$gmttime = gmdate ($time_format, $t_name['Original_Date']);
$topic_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
//get replies, if any.
$db->run = "select Original_Date, Body, author from ebb_posts WHERE tid='$tid'";
$query = $db->query();
$checkreply = $db->num_results();
$db->close();

//see if any replies exists.
if ($checkreply > 0) {
    $print_reply = reply_listing_print();
} else {
    $print_reply = null;
}
//output
$allsmile = showall_smiles();
$page = new template($template_path ."/print.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "LANG-TITLE" => "$viewtopic[ptitle]",
  "SUBJECT" => "$t_name[Topic]",
  "TOPIC-DATE" => "$topic_date",
  "AUTHOR" => "$t_name[author]",
  "TOPIC" => "$msg",
  "REPLY" => "$print_reply"));
$page->output();
?>
