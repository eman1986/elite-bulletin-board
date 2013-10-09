<?php
define('IN_EBB', true);
/*
Filename: index.php
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

switch($action){
case 'info':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 9);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$acptitle = $cp['php_info'];
	$helpTitle = $help['phpinfotitle'];
	$helpBody = $help['phpinfobody'];
break;
default:
	$acptitle = $cp['title'];
	$helpTitle = $help['acptitle'];
	$helpBody = $help['acpbody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$acptitle",
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
#update board version if update file exist.
if (file_exists("../install/update.php")){
	header("Location: $board_address/install/update.php");
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
switch ( $action ){
case 'info':
	ob_start();
	phpinfo();
	$string = ob_get_contents();
	$string = strchr($string, '</style>');
	$string = str_replace('</style>','',$string);
	$string = str_replace('class="p"','',$string);
	$string = str_replace('class="e"','class="td2"',$string);
	$string = str_replace('class="v"','class="td1"',$string);
	$string = str_replace('class="h"','class="td1"',$string);
	$string = str_replace('class="center"','',$string);
	ob_end_clean();
	#output html.
	$page = new template("../". $template_path ."/cp-phpinfo.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-PHPINFO" => "$cp[php_info]",
	"LANG-TEXT" => "$cp[phpinfo_detail]",
	"PHPINFO" => "$string"));
	$page->output();
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("Viewed PHP Info Page", "$logged_user", "$acp_date", "$ip");
	ob_end_flush();
break;
default:
	#see if user has access to this portion of the script.
	$permission_chk_update = access_vaildator($permission_type, 10);
	$permission_chk_srvinfo = access_vaildator($permission_type, 9);
	$permission_chk_log = access_vaildator($permission_type, 11);
	#display ACP header.
	$page = new template("../". $template_path ."/cp-mainmenu.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-WELCOME" => "$cp[welcome]",
	"LANG-BOARDMENU" => "$cp[boardmenu]",
	"LANG-USERMENU" => "$cp[usermenu]",
	"LANG-GENERALMENU" => "$cp[generalmenu]",
	"LANG-GROUPMENU" => "$cp[groupmenu]",
	"LANG-STYLEMENU" => "$cp[stylemenu]",
	"LANG-SETTINGS" => "$cp[settings]",
	"LANG-MANAGE" => "$cp[manage]",
	"LANG-BOARDSETUP" => "$cp[boardsetup]",
	"LANG-SMILES" => "$post[smiles]",
	"LANG-NEWSLETTER" => "$cp[newsletter]",  
	"LANG-USERSETTINGS" => "$cp[usersettings]",
	"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
	"LANG-MAILSETTINGS" => "$cp[mailsettings]",
	"LANG-COOKIESETTINGS" => "$cp[cookiesettings]", 
	"LANG-ATTACHMENTSETTINGS" => "$cp[attachmentsettings]",
	"LANG-GROUPSETUP" => "$cp[groupsetup]",
	"LANG-GROUPPERMISSION" => "$cp[grouppermission]",
	"LANG-PENDINGLIST" => "$cp[pendinglist]",
	"LANG-RANKS" => "$cp[ranks]",
	"LANG-CREATESTYLE" => "$cp[createstyle]",
	"LANG-BAN" => "$cp[banlist]",
	"LANG-BLACKLISTUSERS" => "$cp[blacklist]",
	"LANG-ACTIVATE" => "$cp[activateacct]",
	"LANG-USERWARN" => "$cp[warninglist]",
	"LANG-CENSOR" => "$cp[censor]",
	"LANG-PRUNE" => "$cp[prune]",
	"LANG-USERPRUNE" => "$cp[userprune]"));
	$page->output();
	#check permissions.	
	if($permission_chk_update == 1){
		#call board setting function.
		$colume = 'version';
		$settings = board_settings($colume);
		#get latest version of EBB.
		$checker = versionchecker();
		#output.
		$page = new template("../". $template_path ."/cp-updatechk.htm");
		$page->replace_tags(array(
		"LANG-VERSIONDETAIL" => "$cp[verdetails]",
		"LANG-CHECKVERSION" => "$checker",
		"LANG-VERSIONDETAILS" => "$cp[versiondetails]",
		"LANG-MODINSTALLER" => "$cp[modlist]"));
		$page->output();	
	}
	#see if user can see the server information.
	if($permission_chk_srvinfo == 1){
		#get php version.
		$php_version = phpversion();
		#get mysql version.
		$mysql_version = mysql_get_server_info();
		$mysql_ver_info = substr($mysql_version, 0, strpos($mysql_version, "-"));
		#output.
		$page = new template("../". $template_path ."/cp-serverinformation.htm");
		$page->replace_tags(array(
		"LANG-SERVERINFO" => "$cp[server_info]",
		"LANG-PHPVERSION" => "$cp[php_ver]",
		"PHPVERSION" => "$php_version",
		"LANG-MYSQLVERSION" => "$cp[mysql_ver]",
		"MYSQLVERSION" => "$mysql_ver_info",
		"LANG-PHPINFO" => "$cp[php_info]"));
		$page->output();
	}
	#see if user has access to this portion of the script.
	if($permission_chk_log == 1){
		#load admin log.
		$acp_log = acp_log_view();
		#output.
		$page = new template("../". $template_path ."/cp-lastlogactions.htm");
		$page->replace_tags(array(
		"LANG-ACPLOG" => "$cp[acp_log]",
		"ACPLOG" => "$acp_log",
		"LANG-VIEWLOG" => "$cp[acp_full]"));
		$page->output();	
	}
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
