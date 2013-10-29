<?php
define('IN_EBB', true);
/*
Filename: index.php
Last Modified: 10/24/2013

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
  "PAGETITLE" => "$lang[title]"));

$page->output();
//check to see if the install file is still on the user's server.
//$setupexist = checkinstall();
// if ($setupexist){
//    if ($access_level == 1){
//        $error = $lang['installadmin'];
//        echo error($error, "error");
//    }else{
//        $error = $lang['install'];
//        echo error($error, "general");
//        exit();
//    }
// }

//check to see if the board is on or off.
if ($board_status == 0){
	$offline_msg = nl2br($off_msg);
	$error = $offline_msg;
	if ($access_level == 1){
		$error .= "<p class=\"td\">[<a href=\"acp/index.php\">$lang[cp]</a>]</p>";
	}else{
		$error .= "<p class=\"td\">[<a href=\"login.php\">$lang[login]</a>]</p>";
	}
	echo error($error, "general");
	#terminate program after message appears.
	exit();
}

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
    $page = new \ebb\template("top-admin", $template_path);
    $page->replace_tags(array(
        "TITLE" => $title,
        "LANG-WELCOME" => $lang['welcome'],
        "LOGGEDUSER" => $logged_user,
        "LANG-LOGOUT" => $lang['logout'],
        "NEWPM" => $pm_msg,
        "LANG-CP" => $lang['cp'],
        "LANG-NEWPOSTS" => $lang['newposts'],
        "ADDRESS" => $address,
        "LANG-HOME" => $lang['home'],
        "LANG-SEARCH" => $lang['search'],
        "LANG-CLOSE" => $lang['closewindow'],
        "LANG-QUICKSEARCH" => $lang['quicksearch'],
        "LANG-ADVANCEDSEARCH" => $lang['advsearch'],
        "LANG-FAQ" => $lang['faq'],
        "LANG-MEMBERLIST" => $lang['members'],
        "LANG-GROUPLIST"=> $lang['groups'],
        "LANG-PROFILE" => $lang['profile']));
    $page->output();
}
if ($access_level == 2 || $access_level == 3) {
	$page = new \ebb\template("top-logged", $template_path);
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$lang[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$lang[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-NEWPOSTS" => "$lang[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$lang[home]",
	"LANG-SEARCH" => "$lang[search]",
	"LANG-CLOSE" => "$lang[closewindow]",
	"LANG-QUICKSEARCH" => "$lang[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$lang[advsearch]",
	"LANG-FAQ" => "$lang[faq]",
	"LANG-MEMBERLIST" => "$lang[members]",
	"LANG-GROUPLIST"=> "$lang[groups]",
	"LANG-PROFILE" => "$lang[profile]"));
	$page->output();
}
if ($access_level == 0) {
    $page = new \ebb\template("top-guest", $template_path);
    $page->replace_tags(array(
    "TITLE" => "$title",
    "LANG-WELCOME" => "$lang[welcomeguest]",
    "LANG-LOGIN" => "$lang[login]",
    "LANG-REGISTER" => "$lang[register]",
    "ADDRESS" => "$address",
    "LANG-HOME" => "$lang[home]",
    "LANG-SEARCH" => "$lang[search]",
    "LANG-FAQ" => "$lang[faq]",
    "LANG-MEMBERLIST" => "$lang[members]",
    "LANG-GROUPLIST"=> "$lang[groups]",));
    $page->output();
}

//@TODO rebuild this if we go jQuery.
//show announcement, if admins wants them on.
if ($boardPref->getPreferenceValue('infobox_status') == 1) {
    $string = nl2p(smiles(BBCode("Hi TESTING!")));
    //load template
    $page = new \ebb\template("announcement", $template_path);
    $page->replace_tags(array(
    "TITLE" => "$title",
    "LANG-ANNOUNCEMENT" => "$lang[announcements]",
    "LANG-TICKER" => "$lang[ticker_txt]",
    "ANNOUNCEMENT" => "$string"));
    $page->output();
}
#display board listings.
$board_row = index_board();
#get board stats.
//$b_stats = board_stats();
$new_user = newuser();
//load board stat-icon
$page = new \ebb\template("boardstat", $template_path);
$page->replace_tags(array(
  "LANG-BOARDSTAT" => "$lang[boardstatus]",
  "LANG-ICONGUIDE" => "$lang[iconguide]",
  "LANG-NEWESTMEMBER" => "$lang[newestmember]",
  "NEWESTMEMBER" => "$new_user[Username]",
  //"TOTAL-TOPIC" => "$b_stats[1]",
  "LANG-TOTALTOPIC" => "$lang[topics]",
  //"TOTAL-POST" => "$b_stats[2]",
  "LANG-TOTALPOST" => "$lang[posts]",
  //"TOTAL-USER" => "$b_stats[0]",
  "LANG-TOTALUSER" => "$lang[membernum]",
  "LANG-NEWPOST" => "$lang[newpost]",
  "LANG-OLDPOST" => "$lang[oldpost]"));
$page->output();

//grab total online currently
//$db->run = "select DISTINCT Username from ebb_online where ip=''";
//$online_logged_count = $db->num_results();
//$db->close();
//$db->run = "select DISTINCT ip from ebb_online where Username=''";
//$online_guest_count = $db->num_results();
//$db->close();
//call the whos online function
//$online = whosonline();
//output who's online.
$page = new \ebb\template("whosonline", $template_path);
$page->replace_tags(array(
  "LANG-WHOSONLINE" => "$lang[whosonline]",
  "LANG-ONLINEKEY" => "$lang[onlinekey]",
  "LOGGED-ONLINE" => "0",
  "LANG-LOGGED-ONLINE" => "$lang[membernum]",
  "GUEST-ONLINE" => "0",
  "LANG-GUEST-ONLINE" => "$lang[guestonline]",
  "WHOSONLINE"=> ""));

$page->output();
//display footer
$page = new \ebb\template("footer", $template_path);
$page->replace_tags(array(
  "LANG-POWERED" => "$lang[poweredby]"));
$page-> output();
ob_end_flush();