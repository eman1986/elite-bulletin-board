<?php
define('IN_EBB', true);
/*
Filename: acp_log.php
Last Modified: 5/22/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "../config.php";
require "../header.php";
require "../includes/admin_function.php";
#see if user has access to this portion of the script.
$permission_chk_view = access_vaildator($permission_type, 11);
$permission_chk_clear = access_vaildator($permission_type, 12);
if($permission_chk_view == 0){
	die($cp['noaccess']);
}
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}	
$page = new template("../". $template_path ."/acp_logheader.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$cp[acp_title]"));

$page->output();
//check to see if the install file is stil on the user's server.
$setupexist = checkinstall();
if ($setupexist == 1){
	$error = $txt['installadmin'];
	echo acp_error($error, "error");
}
//check to see if the user can access this board.
echo check_ban();
//check to see if this user is able to access this area.
if (($access_level == 2) or ($stat == "Member") or ($stat == "guest") or ($access_level == 3)){
	header("Location: $board_address/index.php");
}
#see if user confirm login.
if (isset($_SESSION['ebbacpu']) and (isset($_SESSION['ebbacpp']) and (isset($_SESSION['ebbacp_expire'])))) {

	#see if session expired.
	if ($_SESSION['ebbacp_expire'] <= time()) {
		unset($_SESSION['ebbacp_expire']);
		unset($_SESSION['ebbacpu']);
		unset($_SESSION['ebbacpp']);
	
		#go to login page.
		header("Location: acp_login.php");
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
	header("Location: acp_login.php");
}

//display admin CP
switch($mode){
case 'clear':
	if($permission_chk_clear == 0){
		$error = $cp['noaccess'];
		echo acp_error($error, "error");
	}else{
		#clear log tabel.
		$db->run = "TRUNCATE TABLE `ebb_cplog`";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Cleared Audit Log", "$logged_user", "$acp_date", "$ip");
		//close window.
		echo "<script type=\"text/javascript\">javascript:self.close();</script>";
	}
break;
default:
	$acp_log = acp_log_fullview();
	if($permission_chk_clear == 1){
		$page = new template("../". $template_path ."/cp-acplog_clear.htm");
		$page->replace_tags(array(
		"LANG-CLEARLIST" => "$cp[acp_lclear]",
		"LANG-DELPROMPT" => "$cp[confirmacpclear]",
		"LANG-ACPLOG" => "$cp[acp_title]",
		"ACPLOG" => "$acp_log",
		"LANG-CLOSE" => "$txt[closewindow]"));
		$page->output();
	}else{
		$page = new template("../". $template_path ."/cp-acp_log.htm");
		$page->replace_tags(array(
		"LANG-ACPLOG" => "$cp[acp_title]",
		"ACPLOG" => "$acp_log",
		"LANG-CLOSE" => "$txt[closewindow]"));
		$page->output();
	}
}
ob_end_flush();
?>
