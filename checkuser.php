<?php
define('IN_EBB', true);
/*
Filename: checkuser.php
Last Modified: 8/9/2009

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "&nbsp;",
  "LANG-HELP-TITLE" => "$help[nohelptitle]",
  "LANG-HELP-BODY" => "$help[nohelpbody]"));
$page->output();
//check to see if this user is able to access this board.
echo check_ban();
//output top
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
}else{
	//user is logged in, so place them elsewhere.
	header("Location: index.php");
}
//process login.
$username = var_cleanup($_POST['username']);
$password = var_cleanup($_POST['password']);
$IP = $_SERVER['REMOTE_ADDR'];
if(isset($_POST['auto_login'])){
	$auto_login = var_cleanup($_POST['auto_login']);
}else{
	$auto_login = 0; 
}
$redirect = var_cleanup($_POST['redirect']);
if((empty($username)) AND (empty($password))){
	$error = $login['blank'];
	echo error($error, "error");
}else{
	$pass = md5($password.PWDSALT);

	$db->run = "SELECT Status, Username, active FROM ebb_users WHERE Username='$username' AND Password='$pass'";
	$checklogin_ct = $db->num_results();
	$checklogin_r = $db->result();
	$db->close();
	//see if login is correct.
	if ($checklogin_ct == 1){
		//see if user is marked as inactive.
		if($checklogin_r['active'] == 0){
			$error = $auth['inactiveuser'];
			echo error($error, "error");
		}
		#call board setting function.
		$colume = 'Board_Status';
		$settings = board_settings($colume);
		//check to see if the board is on or off.
		if ($settings['Board_Status'] == 0){
			#check to see user is an admin.
			if ($checklogin_r['Status'] == "groupmember"){
				$db->run = "SELECT gid FROM ebb_group_users where Username='$checklogin_r[Username]' AND Status='Active'";
				$groupuser = $db->result();
				$group_auth_chk = $db->num_results();
				$db->close();
				#see if user belongs to the group.
				if ($group_auth_chk == 1){
					$db->run = "SELECT Level FROM ebb_groups where id='$groupuser[gid]'";
					$level_result = $db->result();
					$db->close();
					#set-up vars
					$access_level = $level_result['Level'];
				}
			}
			if ($access_level == 1){
				#user is an admin, let them log in.
				$currtime = time() + (2592000);
				//add cookie to users computer
				setcookie("ebbuser", $username, $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
				setcookie("ebbpass", $pass, $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
				//remove user's IP from who's online list.
				$db->run = "delete from ebb_online where ip='$IP'";
				$db->query();
				$db->close();
				//brng user back to where they were.
				header("Location: $redirect");
			}else{
				#user is not an admin, tell them they can't login.
				$error = $login['offlinemsg']; 
				echo error($error, "error");
			}
		}else{
			//decide which method to use to remember login.
			if($auto_login == 1){
				$currtime = time() + (2592000);
				//add cookie to users computer
				setcookie("ebbuser", $username, $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
				setcookie("ebbpass", $pass, $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
			}else{
				//create a session.
				$_SESSION['ebb_user'] = $username;
				$_SESSION['ebb_pass'] = $pass;
			}
			//reset the incorrect login number.
			$db->run = "UPDATE ebb_users SET failed_attempts='0' WHERE Username='$username'";
			$db->query();
			$db->close();
			//remove user's IP from who's online list.
			$db->run = "delete from ebb_online where ip='$IP'";
			$db->query();
			$db->close();
			//brng user back to where they were.
			header("Location: $redirect");
		}
	}else{
		//get number of failed tries from this user.
		$db->run = "SELECT failed_attempts FROM ebb_users WHERE Username='$username'";
		$failed = $db->result();
		$db->close();
		//see if logout time equals 5.
		if($failed['failed_attempts'] == 5){
			$db->run = "UPDATE ebb_users SET active='0' WHERE Username='$username'";
			$db->query();
			$db->close();
			$error = $auth['lockeduser'];
			echo error($error, "error");
		}else{
			//add a value to missed login.
			$increase_failed = $failed['failed_attempts'] + 1;
			$db->run = "UPDATE ebb_users SET failed_attempts='$increase_failed' WHERE Username='$username'";
			$db->query();
			$db->close();
			//display error msg.
			$error = $auth['nomatch'];
			echo error($error, "error");
		}
	}
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
