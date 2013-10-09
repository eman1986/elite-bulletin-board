<?php
define('IN_EBB', true);
/*
Filename: topicreview.php
Last Modified: 1/23/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
include "includes/topic_function.php";
#get header details.
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "&nbsp;"));

$page->output();
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	$error = $txt['nobid'];
	echo error($error, "error");
}else{
	$bid = var_cleanup($_GET['bid']);
}
#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	$error = $txt['notid'];
	echo error($error, "error");
}else{
	$tid = var_cleanup($_GET['tid']); 
}
#get board.
$db->run = "select Smiles, BBcode, Image FROM ebb_boards WHERE id='$bid'";
$checkboard = $db->num_results();
$b_name = $db->result();
$db->close();
#get topic.
$db->run = "select author, Body, Topic, Original_Date, disable_smiles, disable_bbcode FROM ebb_topics WHERE tid='$tid'";
$checktopic = $db->num_results();
$topic_r = $db->result();
$db->close();
#see if board and topic exist.
if (($checkboard == 0) or ($checktopic == 0)){
	$error = $viewtopic['doesntexist'];
	echo error($error, "error");
}
#get variables.
$allowsmile = $b_name['Smiles'];
$allowbbcode = $b_name['BBcode'];
$allowimg = $b_name['Image'];
//get date
$gmttime = gmdate ($time_format, $topic_r['Original_Date']);
$post_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
//set bbcode for replies.
$string = $topic_r['Body'];
$msg = $string;
//see if user wish to disable smiles in post.
if($topic_r['disable_smiles'] == 0){
	if ($allowsmile == 1){
		$msg = smiles($msg);
	}
}
//see if user wish to disable bbcode in post.
if($topic_r['disable_bbcode'] == 0){
	if ($allowimg == 1){
		$msg = BBCode($msg, true);
	}
	if ($allowbbcode == 1){
		$msg = BBCode($msg);
	}
}
$msg = language_filter($msg, 1);
$msg = nl2br($msg);
#output.
$page = new template($template_path ."/topicreview.htm");
$page->replace_tags(array(
  "TOPICSUBJECT" => "$topic_r[Topic]",
  "LANG-POSTEDON" => "$viewtopic[postedon]",
  "POSTEDON" => "$post_date",
  "AUTHOR" => "$topic_r[author]",
  "BODY" => "$msg"));
$page->output();

#get replies if any.
$db->run = "select pid from ebb_posts WHERE tid='$tid'";
$reply_ct = $db->num_results();
$db->close();
if($reply_ct > 0){
	$db->run = "select author, Body, Original_Date, disable_smiles, disable_bbcode from ebb_posts WHERE tid='$tid'";
	$query = $db->query();
	$db->close();
	while ($row = mysql_fetch_assoc($query)) {
		//get date
		$gmttime = gmdate ($time_format, $row['Original_Date']);
		$post_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		//set bbcode for replies.
		$string = $row['Body'];
		$re_msg = $string;
		//see if user wish to disable smiles in post.
		if($row['disable_smiles'] == 0){
			if ($allowsmile == 1){
				$re_msg = smiles($re_msg);
			}
		}
		//see if user wish to disable bbcode in post.
		if($row['disable_bbcode'] == 0){
			if ($allowimg == 1){
				$re_msg = BBCode($re_msg, true);
			}
			if ($allowbbcode == 1){
				$re_msg = BBCode($re_msg);
			}
		}
		$re_msg = language_filter($re_msg, 1);
		$re_msg = nl2br($re_msg);
		#output
		$page = new template($template_path ."/topicreview.htm");
		$page->replace_tags(array(
		"TOPICSUBJECT" => "&nbsp;",
		"LANG-POSTEDON" => "$viewtopic[postedon]",
		"POSTEDON" => "$post_date",
		"AUTHOR" => "$row[author]",
		"BODY" => "$re_msg"));
		$page->output();
	}
}
//display footer
$page = new template($template_path ."/topicreview-foot.htm");
$page->output();
?>
