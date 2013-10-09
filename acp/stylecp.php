<?php
define('IN_EBB', true);
/*
Filename: stylecp.php
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
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
#get title.
switch($action){
case 'add_style':
case 'style_add_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 8);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$stylecptitle = $cp['stylemenu'].' - '.$cp['addstyle'];
	$helpTitle = $help['addstyletitle'];
	$helpBody = $help['addstylebody'];
break;
case 'style_edit':
case 'style_modify':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 8);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$stylecptitle = $cp['stylemenu'].' - '.$cp['updatestyle'];
	$helpTitle = $help['addstyletitle'];
	$helpBody = $help['addstylebody'];
break;
case 'style_delete':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 8);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$stylecptitle = $cp['stylemenu'].' - '.$cp['delstyle'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
default:
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 8);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$stylecptitle = $cp['stylemenu'].' - '.$cp['managestyle'];
	$helpTitle = $help['stylemanagetitle'];
	$helpBody = $help['stylemanagebody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$stylecptitle",
  "LANG-HELP-TITLE" => "$helpTitle",
  "LANG-HELP-BODY" => "$helpBody"));

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
	#total of new PM messages.
	$db->run = "select Read_Status from ebb_pm WHERE Reciever='$logged_user' and Read_Status=''";
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

switch($action){
case 'add_style':
	$page = new template("../". $template_path ."/cp-newstyle.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-ADDSTYLE" => "$cp[addstyle]",
	"LANG-NOSTYLENAME" => "$cp[nostylename]",
	"LANG-NOTEMPPATH" => "$cp[notemppath]",
	"LANG-TEXT" => "$cp[addstyletxt]",
	"LANG-STYLENAME" => "$cp[stylename]",
	"LANG-TEMPLATEPATH" => "$cp[temppath]",
	"LANG-TEMPLATERULE" => "$cp[temprule]"));
	$page->output();
break;
case 'style_add_process':
	$style_name = var_cleanup($_POST['style_name']);
	$template_path = var_cleanup($_POST['template_path']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error check
	if (empty($style_name)){
		$errormsg .= $cp['nostylename']."\n\n";
		$error = 1;
	}
	if (empty($template_path)){
		$errormsg .= $cp['notemppath']."\n\n";
		$error = 1;
	}
	if(strlen($style_name) > 50){
		$errormsg .= $cp['longstylename']."\n\n";
		$error = 1;
	}
	if(strlen($template_path) > 80){
		$errormsg .= $cp['longtemppath']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_style (Name, Temp_Path) values ('$style_name', '$template_path')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Added New Style: $style_name", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: stylecp.php");
	}
break;
case 'style_edit':
	#see if user added the Style ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "SELECT Name, Temp_Path FROM ebb_style WHERE id='$id'";
	$modify_style = $db->result();
	$style_chk = $db->num_results();
	$db->close();
	#see if style exist.
	if($style_chk == 0){
		$error = $cp['stylenotexist'];
		echo acp_error($error, "error");	 
	}else{	
		$page = new template("../". $template_path ."/cp-modifystyle.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-MODIFYSTYLE" => "$cp[updatestyle]",
		"LANG-NOSTYLENAME" => "$cp[nostylename]",
		"LANG-NOTEMPPATH" => "$cp[notemppath]",
		"LANG-TEXT" => "$cp[addstyletxt]",
		"ID" => "$id",
		"LANG-STYLENAME" => "$cp[stylename]",
		"STYLENAME" => "$modify_style[Name]",
		"LANG-TEMPLATEPATH" => "$cp[temppath]",
		"LANG-TEMPLATERULE" => "$cp[temprule]",
		"TEMPLATEPATH" => "$modify_style[Temp_Path]"));
		$page->output();
	}
break;
case 'style_modify':
	#see if user added the Style ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "SELECT id FROM ebb_style WHERE id='$id'";
	$style_chk = $db->num_results();
	$db->close();
	#see if style exist.
	if($style_chk == 0){
		$error = $cp['stylenotexist'];
		echo acp_error($error, "error");	 
	}
	$style_name = var_cleanup($_POST['style_name']);
	$template_path = var_cleanup($_POST['template_path']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error check
	if (empty($style_name)){
		$errormsg = $cp['nostylename']."\n\n";
		$error = 1;;
	}
	if (empty($template_path)){
		$errormsg .= $cp['notemppath']."\n\n";
		$error = 1;
	}
	if(strlen($style_name) > 50){
		$errormsg .= $cp['longstylename']."\n\n";
		$error = 1;
	}
	if(strlen($template_path) > 80){
		$errormsg .= $cp['longtemppath']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "Update ebb_style SET Name='$style_name', Temp_Path='$template_path' WHERE id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Style: $style_name", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: stylecp.php");
	}
break;
case 'style_delete':
	#see if user added the Style ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#see if any users are currently using the requested style.
	$db->run = "SELECT Style FROM ebb_users WHERE Style='$id'";
	$ustyle_chk = $db->num_results();
	$db->close();
	if($ustyle_chk > 0){
		$error = $cp['delstylewarning'];
		echo acp_error($error, "error");
	}		
	#see if style exist.
	$db->run = "SELECT id FROM ebb_style WHERE id='$id'";
	$style_chk = $db->num_results();
	$db->close();
	if($style_chk == 0){
		$error = $cp['stylenotexist'];
		echo acp_error($error, "error");	 
	}else{
		//process query
		$db->run = "DELETE FROM ebb_style WHERE id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Deleted Style", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: stylecp.php");
	}
break; 
default:
	$admin_style = admin_stylelisting();
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
