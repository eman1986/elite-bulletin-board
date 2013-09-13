<?php
define('IN_EBB', true);
/*
Filename: viewboard.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
include "includes/topic_function.php";
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	die($txt['nobid']);
}else{
	$bid = var_cleanup($_GET['bid']); 
}
//check to see if board exists or not and if it doesn't kill the program
$db->run = "select id from ebb_boards WHERE id='$bid'";
$checkboard = $db->num_results();
$db->close();
if ($checkboard == 0){
	die($viewboard['doesntexist']);
}
#get board name.
$db->run = "select Board, type from ebb_boards WHERE id='$bid'";
$rules = $db->result();
$db->close();
#see if board is a category board.
if($rules['type'] == 1){
	#send user to main page.
	header("Location: index.php");
}
#make title variable.
$pagetitle = $viewboard['title']." - ".$rules['Board'];
#output page header.
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$pagetitle",
  "LANG-HELP-TITLE" => "$help[nohelptitle]",
  "LANG-HELP-BODY" => "$help[nohelpbody]"));
$page->output();
//check to see if the install file is stil on the user's server.
$setupexist = checkinstall();
if ($setupexist == 1){
	if ($access_level == 1){
		$error = $txt['installadmin'];
		echo error($error, "error");
	}else{
		$error = $txt['install'];
		echo error($error, "general");
	}
}
//check to see if this user is able to access this board.
echo check_ban();
//check to see if the board is on or off.
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

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
	$page = new template($template_path ."/top-admin.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-CP" => "$menu[cp]",
	"LANG-NEWPOSTS" => "$index[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-CLOSE" => "$txt[closewindow]",
	"LANG-QUICKSEARCH" => "$search[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$search[advsearch]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",
	"LANG-PROFILE" => "$menu[profile]"));
	$page->output();
	//update user's activity.
	echo update_whosonline_reg($logged_user);
}
if (($stat == "Member") OR ($access_level == 2) OR ($access_level == 3)){
	$page = new template($template_path ."/top-logged.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-NEWPOSTS" => "$index[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-CLOSE" => "$txt[closewindow]",
	"LANG-QUICKSEARCH" => "$search[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$search[advsearch]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",
	"LANG-PROFILE" => "$menu[profile]"));
	$page->output();
	//update user's activity.
	echo update_whosonline_reg($logged_user);
}
if ($stat == "guest"){
	$page = new template($template_path ."/top-guest.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcomeguest]",
	"LANG-LOGIN" => "$txt[login]",
	"LANG-REGISTER" => "$txt[register]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",));
	$page->output();
	//update guest's activity.
	echo update_whosonline_guest();
}
//record user comming in here
$read_ct = read_board_stat($bid, $logged_user);
if (($read_ct == 0) AND ($stat !== "guest")){
	$db->run = "insert into ebb_read_board (Board, user) values('$bid', '$logged_user')";
	$db->query();
	$db->close();
}
#check to see if any sub-boards exist.
$db->run = "SELECT id FROM ebb_boards WHERE type='3' and Category='$bid'";
$subboard_chk = $db->num_results();
$db->close();
if($subboard_chk == 1){
	$subboard_row = viewboard_subboard($bid);
}
//check for the posting rule.
$db->run = "select B_Read, B_Post, B_Reply, B_Poll, B_Vote, B_Edit, B_Delete, B_Attachment, B_Important from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
#see if guest is viewing board.
if ($stat == "guest"){
	//guest has no power to post anything at all.
	$posting = '';
	#guest has no group rights.
	$checkgroup = 0;
	$checkmod = 0;
}else{
	#see if user is a non-group user.
	if($stat == "Member"){
		$checkgroup = 0;
		$checkmod = 0;	
	}else{
		#get group access information.
		$checkgroup = group_validate($bid, $level_result['id'], 2);
		$checkmod = group_validate($bid, $level_result['id'], 1);
	}
	//see if user can post.
	$post_chk = permission_check($board_rule['B_Post']);
	$permission_chk_post = access_vaildator($permission_type, 37);
	//see if user can post poll topics
	$post_poll = permission_check($board_rule['B_Poll']);
	$permission_chk_poll = access_vaildator($permission_type, 35);
	//determine rules.
	if ($post_chk == 0){
		$posting = "<img src=\"$template_path/images/locked.gif\" alt=\"\" /><br /><br />";
	}elseif(($permission_chk_post == 0) and ($checkgroup == 1)){
		$posting = "<img src=\"$template_path/images/locked.gif\" alt=\"\" /><br /><br />";
    }else{
		//determine poll rule.
		if ($post_poll == 1){
			$posting = "<a href=\"Post.php?mode=New_Topic&amp;bid=$bid\"><img src=\"$template_path/images/newtopic.gif\" border=\"0\" alt=\"$viewboard[addnewtopic]\" /></a>&nbsp;<a href=\"Post.php?mode=New_Poll&amp;bid=$bid\"><img src=\"$template_path/images/newpoll.gif\" border=\"0\" alt=\"$viewboard[addnewpoll]\" /></a><br /><br />";
		}elseif(($permission_chk_poll == 1) and ($checkgroup == 1)){
			$posting = "<a href=\"Post.php?mode=New_Topic&amp;bid=$bid\"><img src=\"$template_path/images/newtopic.gif\" border=\"0\" alt=\"$viewboard[addnewtopic]\" /></a>&nbsp;<a href=\"Post.php?mode=New_Poll&amp;bid=$bid\"><img src=\"$template_path/images/newpoll.gif\" border=\"0\" alt=\"$viewboard[addnewpoll]\" /></a><br /><br />";
		}else{
			//this user can't make a poll.
			$posting = "<a href=\"Post.php?mode=New_Topic&amp;bid=$bid\"><img src=\"$template_path/images/newtopic.gif\" border=\"0\" alt=\"$viewboard[addnewtopic]\" /></a><br /><br />";
		}
	}
}
//output the rules of this board.
$board_policy = board_policy();
//start pagenation.
$count = 0;
$count2 = 0;
//pagination
if(!isset($_GET['pg'])){
    $pg = 1;
}else{
    $pg = var_cleanup($_GET['pg']);
}
#call board setting function.
$colume = 'per_page';
$settings = board_settings($colume);
// Figure out the limit for the query based on the current page number.
$from = (($pg * $settings['per_page']) - $settings['per_page']);
// Figure out the total number of results in DB:
$db->run = "select bid, last_update, Topic, author, Posted_User, Post_Link, tid, Views, Type, important, Locked from ebb_topics WHERE bid='$bid' ORDER BY important DESC, last_update DESC LIMIT $from, $settings[per_page]";
$query = $db->query();
$db->close();
$db->run = "select bid, last_update, Topic, author, Posted_User, Post_Link, tid, Views, Type, important, Locked from ebb_topics WHERE bid='$bid' ORDER BY important DESC, last_update DESC";
$num = $db->num_results();
$db->close();
#output pagination.
$pagenation = pagination("bid=$bid&amp;");
#display topics
$board = board_listing();
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
