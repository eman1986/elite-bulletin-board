<?php
define('IN_EBB', true);
/*
Filename: acp_login.php
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
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$cp[title]",
  "LANG-HELP-TITLE" => "$help[acplogintitle]",
  "LANG-HELP-BODY" => "$help[acploginbody]"));

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

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
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
}
#see if user confirm login.
if (isset($_SESSION['ebbacpu']) and (isset($_SESSION['ebbacpp']) and (isset($_SESSION['ebbacp_expire'])))) {

	#see if session expired.
	if ($_SESSION['ebbacp_expire'] <= time()) {
		//in this case do nothing, we're already at the login page.
	} else {
		#see if cookie value belongs to a user on the roster.
		$chk_user = user_check(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		$admin_check = admin_verify(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		if(($chk_user == 0) or ($admin_check == false)){
			$error = "INVALID COOKIE OR SESSION!";
			echo acp_error($error, "error");
		}

		#go to login page.
		header("Location: index.php");
	}
}
//display admin CP
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
switch ( $action ){
case 'auth':
	//process login.
	$password = var_cleanup($_POST['password']);
	if (empty($password)) {
		$error = $login['blank'];
		echo acp_error($error, "error");
	}else{
		#encrypt password.
		$pass = md5($password.PWDSALT);
		#run sql query to find a match.
		$db->run = "SELECT Status, Username FROM ebb_users WHERE Username='$logged_user' AND Password='$pass'";
		$checklogin_ct = $db->num_results();
		$checklogin_r = $db->result();
		$db->close();
		//see if login is correct.
		if ($checklogin_ct == 1){
			#check to see user is an admin.
			if ($checklogin_r['Status'] == "groupmember"){
				$db->run = "SELECT gid FROM ebb_group_users where Username='$checklogin_r[Username]'";
				$groupuser = $db->result();
				$group_auth_chk = $db->num_results();
				$db->close();
				#see if user belongs to the group.
				if ($group_auth_chk == 1){
					$db->run = "SELECT Level FROM ebb_groups where id='$groupuser[gid]'";
					$level_result = $db->result();
					$db->close();
					#set-up vars
					$access_Level = $level_result['Level'];
				}
			}
			
			//validate login info.
			if ($access_Level == 1){
				#user is an admin, let them log in. set to last an hour only.
				$expire = time()+3600;
				
				$_SESSION['ebbacpu'] = $logged_user;
				$_SESSION['ebbacpp'] = $pass;
				$_SESSION['ebbacp_expire'] = $expire;
				
				//reset the incorrect login number.
				$db->run = "UPDATE ebb_users SET failed_attempts='0' WHERE Username='$logged_user'";
				$db->query();
				$db->close();
				
				#log action in database.
				$acp_date = time();
				$ip = $_SERVER['REMOTE_ADDR'];
				echo acp_log_add("Logged into AdminCP", $logged_user, $acp_date, $ip);
				//brng user back to where they were.
				header("Location: index.php");
			}else{
				#log action in database.
				$acp_date = time();
				$ip = $_SERVER['REMOTE_ADDR'];
				echo acp_log_add("non-admin user attempted to Login into AdminCP", $logged_user, $acp_date, $ip);	 
				#user is not an admin, tell them they can't login.
				header("Location: $board_address/index.php");
			} 
		}else{
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Failed Logging into AdminCP", $logged_user, $acp_date, $ip);
			//get number of failed tries from this user.
			$db->run = "SELECT failed_attempts FROM ebb_users WHERE Username='$logged_user'";
			$failed = $db->result();
			$db->close();
			//see if logout time equals 5.
			if($failed['failed_attempts'] == 5){
				$db->run = "UPDATE ebb_users SET active='0' WHERE Username='$logged_user'";
				$db->query();
				$db->close();
				
				#log action in database.
				$acp_date = time();
				$ip = $_SERVER['REMOTE_ADDR'];
				echo acp_log_add("User exceeded login tries, they are now locked.", $logged_user, $acp_date, $ip);				
				
				//log user out, their locked out anyways.
				header("Location: $board_address/logout.php");
			}else{
				//add a value to missed login.
				$increase_failed = $failed['failed_attempts'] + 1;
				$db->run = "UPDATE ebb_users SET failed_attempts='$increase_failed' WHERE Username='$logged_user'";
				$db->query();
				$db->close();
				//display error msg.
				$error = $auth['nomatch'];
				echo acp_error($error, "error");
			}
		}
	}
break;
default:
$page = new template("../". $template_path ."/cp-login.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "LANG-TITLE" => "$login[login]",
  "LANG-NOPASSWORD" => "$login[nopass]",
  "TEXT" => "$login[text]",
  "LANG-USERNAME" => "$txt[username]",
  "USERNAME" => "$logged_user",
  "LANG-PASSWORD" => "$login[pass]",
  "LANG-LOGIN" => "$login[login]"));
$page->output();
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
