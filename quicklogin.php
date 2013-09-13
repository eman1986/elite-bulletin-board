<?php
define('IN_EBB', true);
/*
Filename: quicklogin.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
//display login system.
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}

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
	}
	$error .= '<p class="td"><b>'.$login['offlinemsg']. '</b></p>';
	echo error($error, "general");
}

if ($stat == "guest"){
	//update guest's activity.
	echo update_whosonline_guest();
}
switch ($mode){
default:
	if ((!isset($_COOKIE['ebbuser'])) OR (!isset($_SESSION['ebb_user']))){
		$page = new template($template_path ."/quicklogin.htm");
		$page->replace_tags(array(
		"LANG-NOUSERNAME" => "$login[nouser]",
		"LANG-NOPASSWORD" => "$login[nopass]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-PASSWORD" => "$login[pass]",
		"LANG-REMEMBER" => "$login[rememberlogin]",
		"LANG-REMEMBERTXT" => "$login[remembertxt]",
        "LANG-REGISTER" => "$login[reg]",
        "LANG-FORGOT" => "$login[forgot]",
		"LANG-LOGIN" => "$login[login]"));
		$page->output();
	}else{
		$error = $login['alreadylogged'];
		echo error($error, "general");
	}
}
ob_end_flush();
?>
