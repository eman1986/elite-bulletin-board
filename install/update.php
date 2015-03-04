<?php
define('IN_EBB', true);
/*
Filename: update.php
Last Modified: 03/04/2015

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "../config.php";
require "../header.php";
require "../includes/admin_function.php";

#version data.
$versionKey = 'ef645aba8f54125e65eb5092ecb74931';
$newVer = '2.1.26';

$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "v$newVer Updater"));

$page->output();

//check to see if the user can access this board.
echo check_ban();
//check to see if this user is able to access this area.
if (($access_level == 2) or ($stat == "Member") or ($stat == "guest") or ($access_level == 3)){
	header("Location: $board_address/index.php");
}

if(isset($_GET['mode'])){
	$mode = $_GET['mode'];
}else{
	$mode = '';
}
//output top
if ($access_level == 1){
	#total of new PM messages.
	$db->run = "select Read_Status from ebb_pm WHERE Reciever='$logged_user' and Folder='Inbox' and Read_Status=''";
	$new_pm = $db->num_results();
	$db->close();
	if($new_pm == 0){
		$pm_msg = $menu['nonewpm'];	
	}else{
		$pm_msg = $new_pm.$menu['newpm'];
	}
	#output.
	$page = new template("../". $template_path ."/top-acp.htm");
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
}else{
	die('ACCESS DENIED!!!!');
}

#see if user confirm login.
if (isset($_SESSION['ebbacpu']) and (isset($_SESSION['ebbacpp']) and (isset($_SESSION['ebbacp_expire'])))) {

	#see if session expired.
	if ($_SESSION['ebbacp_expire'] <= time()) {
		unset($_SESSION['ebbacp_expire']);
		unset($_SESSION['ebbacpu']);
		unset($_SESSION['ebbacpp']);

		#go to login page.
		header("Location: $board_address/acp/acp_login.php");
	} else {
		#see if cookie value belongs to a user on the roster.
		$chk_user = user_check(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		$admin_check = admin_verify(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		if(($chk_user == 0) or ($admin_check == false)){
			$error = "INVALID COOKIE OR SESSION!";
			echo acp_error($error, "error");
		}
	}
} else {
	#go to login page.
	header("Location: $board_address/acp/acp_login.php");
}

#start installer.
switch($mode){
case 'sqldump':
	#inserts sql data
	$db->run = "UPDATE ebb_settings SET version='$versionKey'";
	$db->query();
	$db->close();
	
	#finish updater.
	header("Location: update.php?mode=finalize");
break; 
case 'finalize':

	#deletes installer(if set correctly).
	$delinstall = @unlink ('update.php');
	@unlink('index.php');
	
	//did everything get removed?
	if (($delinstall) AND ($delinstall2)){
		echo "<p class=\"td1\"><b>Deleting update file...Success!</b></p>
		<p class=\"td1\">Your board is now up to date.</p>
		<p class=\"td1\"><a href=\"../acp/index.php\">Go To ACP</a></p>";
	}else{
		echo "<p><b>Deleting update file...Failed! didn't CHMOD folder or file 777 or 755</b>.</p>
		<p class=\"td1\">You'll have to delete the updater yourself, until then you won't have access to the ACP.<br /><br />
		Your board is now up to date.</p>
		<p class=\"td1\"><a href=\"../acp/index.php\">Go To ACP</a></p>";
	}
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("Updated Board to v$newVer", $logged_user, $acp_date, $ip);
break;
default:
	echo '<p class="td1"><b>v'.$newVer.' Updater</b></p>
	<p class="td1">A new release was detected and all updated files were added.</p>
	<p class="td1">To continue with this installer click <a href="update.php?mode=sqldump">here</a>.</p>';
	#check base folder.
	if(!is_writable("../install/")){
		echo '<p style="color: red"><b>The install folder doesn\'t has the correct permissions. Chmod the file 777 or 755(ask your host for which one to use) to allow the updater run more smoothly.</p>'; 
	}else{                                       
		echo '<p style="color: green">The install folder has the correct permissions, this will allow the updater run more smoothly.</p>'; 
	}
	
	if(!is_writable("update.php")){
		echo '<p style="color: red"><b>The updater doesn\'t has the correct permissions. Chmod the file 777 or 755(ask your host for which one to use) to allow the installer run more smoothly.</p>'; 
	}else{                                       
		echo '<p style="color: green">The updater has the correct permissions, this will allow the installer run more smoothly.</p>'; 
	}
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "Powered By"));
$page->output();
?>
