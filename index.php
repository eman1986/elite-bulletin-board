<?php
define('IN_EBB', true);
/*
Filename: index.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
require "header.php";

$page = new \ebb\template("header", $template_path);
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$index[title]"));

$page->output();
//check to see if the install file is still on the user's server.
$setupexist = checkinstall();
if ($setupexist){
	if ($access_level == 1){
		$error = $txt['installadmin'];
		echo error($error, "error");
	}else{
		$error = $txt['install'];
		echo error($error, "general");
		exit();
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
	#output.
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
#call board setting function.
$colume = 'Announcement_Status, Announcements';
$settings = board_settings($colume);
//show announcement, if admins wants them on.
if ($settings['Announcement_Status'] == 1){

	$string = nl2p(smiles(BBCode($settings['Announcements'])));
	//load template
	$page = new template($template_path ."/announcement.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-ANNOUNCEMENT" => "$index[announcements]",
	"LANG-TICKER" => "$index[ticker_txt]",
	"ANNOUNCEMENT" => "$string"));
	$page->output();
}
#display board listings.
$board_row = index_board();
#get board stats.
$b_stats = board_stats();
$new_user = newuser();
//load board stat-icon
$page = new template($template_path ."/boardstat.htm");
$page->replace_tags(array(
  "LANG-BOARDSTAT" => "$index[boardstatus]",
  "LANG-ICONGUIDE" => "$index[iconguide]",
  "LANG-NEWESTMEMBER" => "$index[newestmember]",
  "NEWESTMEMBER" => "$new_user[Username]",
  "TOTAL-TOPIC" => "$b_stats[1]",
  "LANG-TOTALTOPIC" => "$index[topics]",
  "TOTAL-POST" => "$b_stats[2]",
  "LANG-TOTALPOST" => "$index[posts]",
  "TOTAL-USER" => "$b_stats[0]",
  "LANG-TOTALUSER" => "$index[membernum]",
  "LANG-NEWPOST" => "$index[newpost]",
  "LANG-OLDPOST" => "$index[oldpost]"));
$page->output();

//grab total online currently
$db->run = "select DISTINCT Username from ebb_online where ip=''";
$online_logged_count = $db->num_results();
$db->close();
$db->run = "select DISTINCT ip from ebb_online where Username=''";
$online_guest_count = $db->num_results();
$db->close();
//call the whos online function
$online = whosonline();
//output who's online.
$page = new template($template_path ."/whosonline.htm");
$page->replace_tags(array(
  "LANG-WHOSONLINE" => "$index[whosonline]",
  "LANG-ONLINEKEY" => "$index[onlinekey]",
  "LOGGED-ONLINE" => "$online_logged_count",
  "LANG-LOGGED-ONLINE" => "$index[membernum]",
  "GUEST-ONLINE" => "$online_guest_count",
  "LANG-GUEST-ONLINE" => "$index[guestonline]",
  "WHOSONLINE"=> "$online"));

$page->output();
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page-> output();
ob_end_flush();