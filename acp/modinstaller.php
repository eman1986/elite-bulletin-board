<?php
define('IN_EBB', true);
/*
Filename: modinstaller.php
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
//check to see if the user can access this board.
echo check_ban();
//check to see if this user is able to access this area.
if (($access_level == 2) or ($stat == "Member") or ($stat == "guest") or ($access_level == 3)){
	die($cp['noaccess']);
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

#see if user has access to this portion of the script.
$permission_chk_update = access_vaildator($permission_type, 10);
if($permission_chk_update == 1){
	$addon = acp_mod_installer();
	$page = new template("../". $template_path ."/cp-modinstaller.htm");
	$page->replace_tags(array(
	"PAGETITLE" => "$cp[modlist]",
	"LANG-TEXT" => "$cp[modinstalltxt]",
	"MODINSTALL-LIST" => "$addon",
	"LANG-CLOSEWINDOW" => "$txt[closewindow]"));
	$page->output();
}else{
	$error = $cp['noaccess'];
	echo acp_error($error, "error");
}
?>
