<?php
session_start();

if (phpversion() < "5.3") {
    exit('Elite Bulletin Board Requires at least PHP 5.3.0');
}

//delete old session.
session_regenerate_id(true);

if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: header.php
Last Modified: 9/11/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
if (phpversion() >= "5.4") {
	error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT); //to remove all DEPRECATED & STRICT STANDARDS errors in production for PHP 5.4 users.
}elseif (phpversion() >= "5.3") {
	error_reporting(E_ALL ^ E_DEPRECATED); //to remove all DEPRECATED errors in production for PHP 5.3 users.
}

#see if config file is already written.
$config_path = 'config.php';
$file_size = filesize($config_path);
if($file_size == 0){
    header("Location: ". isSecure() ? "https://" : "http://" . $_SERVER["SERVER_NAME"] . "/install");
}

#load functions
require_once "config.php";
require_once FULLPATH."/includes/function.php";
require_once FULLPATH."/includes/FluentPDO/FluentPDO.php";
require_once FULLPATH."/includes/template_function.php";
require_once FULLPATH."/includes/posting_function.php";
require_once FULLPATH."/includes/user_function.php";

// setup our database object.
$pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
$db = new FluentPDO($pdo);

//update online data.
$time = time();
$timeout = $time - 120;

//delete any old entries
$db->deleteFrom('ebb_online')->where('time < ?', $timeout);

#user check
if ((isset($_COOKIE['ebbuser']) && ($_COOKIE['ebbpass'])) OR (isset($_SESSION['ebb_user'])) && ($_SESSION['ebb_pass'])){
	#get username value.
	if(isset($_SESSION['ebb_user'])){
		$logged_user = var_cleanup($_SESSION['ebb_user']);
		$chkpwd = var_cleanup($_SESSION['ebb_pass']);
	}else{
		$logged_user = var_cleanup($_COOKIE['ebbuser']);
		$chkpwd = var_cleanup($_COOKIE['ebbpass']);
	}
	$chk_user = user_check($logged_user, $chkpwd);
	if($chk_user == 1){
		#set the columes needed for now.
		$columes = 'Status, Style, Time_format, Language, Time_Zone, last_visit, suspend_length, suspend_time';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		#set-up vars
		$stat = $userpref['Status'];
		$template = $userpref['Style'];
		$time_format = $userpref['Time_format'];
		$lang = $userpref['Language'];
		$gmt = $userpref['Time_Zone'];
		$last_visit = $userpref['last_visit'];
		$suspend_length = $userpref['suspend_length'];
		$suspend_date = $userpref['suspend_time'];
		//check to see if user is part of a group.
		if ($stat == "groupmember"){
			$db->run = "SELECT gid FROM ebb_group_users where Username='$logged_user' AND Status='Active'";
			$groupuser = $db->result();
			$group_auth_chk = $db->num_results();
			$db->close();
			if ($group_auth_chk == 1){
				$db->run = "SELECT id, Name, Level, permission_type FROM ebb_groups where id='$groupuser[gid]'";
				$level_result = $db->result();
				$db->close();
				#set-up vars
				$access_level = $level_result['Level'];
				$permission_type = $level_result['permission_type'];
			}else{
				die('INVALID GROUP ID');
			}
		}elseif($stat == "Member"){
			$level_result = 3;
			$access_level = 3;
			$permission_type = 4;
		}else{
			$logged_user = '';
			$level_result = 0;
			$access_level = 0;
			$permission_type = 0;
		}
	}else{
		$error = "INVALID COOKIE OR SESSION!";
		echo error($error, "error");
	}
}else{
	$stat = "guest";
	$access_level = 0;
	$logged_user = '';
	$level_result['Level'] = 0;
	$level_result['id'] = 0;
	$permission_type = 0;
	#call board setting function.
	$colume = 'Default_Style, Default_Language, Default_Time, Default_Zone, activation';
	$settings = board_settings($colume);
	#set-up vars
	$template = $settings['Default_Style'];
	$lang = $settings['Default_Language'];
	$time_format = $settings['Default_Time'];
	$gmt = $settings['Default_Zone'];
	$active_type = $settings['activation'];
}
#call board setting function.
$colume = 'Site_Title, Site_Address, Board_Address, Board_Status, Board_Email, Off_Message';
$settings = board_settings($colume);
#settings
$title = $settings['Site_Title'];
$address = $settings['Site_Address'];
$board_address = $settings['Board_Address'];
$board_status = $settings['Board_Status'];
$board_email = $settings["Board_Email"];
$off_msg = $settings["Off_Message"];
#template
$theme = theme($template);
#set-up vars
$template_path = $theme['Temp_Path'];
#template loading
require FULLPATH."/template.php";
#language loading
require FULLPATH."/lang/".$lang.".lang.php";