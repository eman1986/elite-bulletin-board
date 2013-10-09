<?php
define('IN_EBB', true);
/*
Filename: groupcp.php
Last Modified: 9/5/2012

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
case 'new_group':
case 'new_group_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['addgroup'];
	$helpTitle = $help['newgrouptitle'];
	$helpBody = $help['newgroupbody'];
break;
case 'group_modify':
case 'modify_group_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['modifygroup'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'group_delete':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['delete'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'group_memberlist':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['modifygroup'];
	$helpTitle = $help['acpgrouplisttitle'];
	$helpBody = $help['acpgrouplistbody'];
break;
case 'groupmember_remove':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['removefromgroup'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'grouppermission':
case 'grouprights':
case 'grouprights_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['grouppermission'];
	$helpTitle = $help['grouprightstitle'];
	$helpBody = $help['grouprightsbody'];
break;
case 'pendinglist':
case 'pendingview':
case 'pending_stat':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['pendinglist'];
	$helpTitle = $help['pendinglisttitle'];
	$helpBody = $help['pendinglistbody'];
break;
case 'group_adduser':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['addtogroup'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'manageprofile':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['manageprofile'];
	$helpTitle = $help['groupprofiletitle'];
	$helpBody = $help['groupprofilebody'];
break;
case 'new_profile':
case 'new_profile_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['newprofile']; 
	$helpTitle = $help['groupprofiletitle'];
	$helpBody = $help['groupprofilebody'];
break;
case 'profile_modify':
case 'profile_modify_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['modifyprofile'];
	$helpTitle = $help['groupprofiletitle'];
	$helpBody = $help['groupprofilebody'];
break;
case 'profile_delete':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['deleteprofile'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
default:
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 3);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$groupcptitle = $cp['groupmenu'].' - '.$cp['groupsetup'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$groupcptitle",
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
case 'new_group':
	$gprofile_sel = groupProfile_list();
	$page = new template("../". $template_path ."/cp-newgroup.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-CREATEGROUP" => "$cp[addgroup]",
	"LANG-NOGROUPNAME" => "$cp[nogroupname]",
	"LANG-NOGROUPDESCRIPTION" => "$cp[nogroupdescription]",
	"LANG-LONGGROUPDESCRIPTION" => "$cp[longgroupdescription]",
	"LANG-NOGROUPSTAT" => "$cp[statusnotset]",
	"LANG-NOGROUPLEVEL" => "$cp[noaccessset]",
	"LANG-GROUPNAME" => "$groups[name]",
	"LANG-GROUPDESCRIPTION" => "$groups[description]",
	"LANG-GROUPSTATUS" => "$groups[groupstat]",
	"LANG-OPEN" => "$groups[open]",
	"LANG-CLOSE" => "$groups[closed]",
	"LANG-HIDDEN" => "$cp[grouphidden]",
	"LANG-GROUPACCESS" => "$cp[groupaccess]",
	"LANG-SEL-LEVEL" => "$cp[sel_level]",
	"LANG-LEVEL-1" => "$cp[level1]",
	"LANG-LEVEL-2" => "$cp[level2]",
	"LANG-LEVEL-3" => "$cp[level3]",
	"LANG-GROUPPROFILE" => "$cp[groupprofile]",
	"LANG-GROUPPROFILEHINT" => "$cp[groupprofilehnt]",
	"GROUPPROFILE" => "$gprofile_sel",
	"LANG-ADDGROUP" => "$cp[addgroupbtn]"));
	$page->output();
break;
case 'new_group_process':
	//get data from form.
	$group = var_cleanup($_POST['group']);
	$description = var_cleanup($_POST['description']);
	$status = var_cleanup($_POST['status']);
	$groupaccess = var_cleanup($_POST['groupaccess']);
	$gprofile = var_cleanup($_POST['gprofile']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#get profile status.
	$db->run = "select access_level from ebb_permission_profile where id='$gprofile'";
	$gprofile_level = $db->result();
	$db->close();
	//do some error checking.
	if ($group == ""){
		$errormsg .= $cp['nogroupname']."\n\n";
		$error = 1;
	}
	if ($description == ""){
		$errormsg .= $cp['nogroupdescription']."\n\n";
		$error = 1;
	}
	if ($status == ""){
		$errormsg .= $cp['statusnotset']."\n\n";
		$error = 1;
	}
	if ($groupaccess == ""){
		$errormsg .= $cp['noaccessset']."\n\n";
		$error = 1;
	}
	if($gprofile == ""){
		$errormsg .= $cp['nogprofilesel']."\n\n";
		$error = 1;
	}
	if(strlen($group) > 30){
		$errormsg .= $cp['longgroupname']."\n\n";
		$error = 1;
	}
	if(strlen($description) > 255){
		$errormsg .= $cp['longgroupdescription']."\n\n";
		$error = 1; 
	}
	if($gprofile_level['access_level'] != $groupaccess){
		$errormsg .= $cp['invalidprofilecho']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate"); 
	}else{
		//process query.
		$db->run = "insert into ebb_groups (Name, Description, Enrollment, Level, permission_type) values ('$group', '$description', '$status', '$groupaccess', '$gprofile')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Created New Group: $group", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: groupcp.php");
	}
break;
case 'group_modify':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($group_chk == 0){
		$error = $cp['groupnotexist']; 
		echo acp_error($error, "error");
	}
	#get group data.
	$db->run = "SELECT Name, Description, Enrollment, Level, permission_type FROM ebb_groups WHERE id='$id'";
	$group_r = $db->result();
	$db->close();
	$gprofile_sel = groupProfile_list();
	#get membership type setting.
	if ($group_r['Enrollment'] == 0){
		$group_status = "<input type=\"radio\" name=\"status\" value=\"0\" checked=checked />$groups[closed] <input type=\"radio\" name=\"status\" value=\"1\" />$groups[open]  <input type=\"radio\" name=\"status\" value=\"2\" />$cp[grouphidden]";
	}elseif ($group_r['Enrollment'] == 1){
		$group_status = "<input type=\"radio\" name=\"status\" value=\"0\" />$groups[closed] <input type=\"radio\" name=\"status\" value=\"1\" checked=checked />$groups[open] <input type=\"radio\" name=\"status\" value=\"2\" />$cp[grouphidden]";
	}else{
		$group_status = "<input type=\"radio\" name=\"status\" value=\"0\" />$groups[closed] <input type=\"radio\" name=\"status\" value=\"1\" />$groups[open] <input type=\"radio\" name=\"status\" value=\"2\" checked=checked />$cp[grouphidden]";
	}
	#get level setting
	if ($group_r['Level'] == 1){
			$access_status = "<select name=\"groupaccess\" class=\"text\" id=\"grouplevel\">
		<option value=\"1\" selected=selected>$cp[level1]</option>
		<option value=\"2\">$cp[level2]</option>
		<option value=\"3\">$cp[level3]</option>
		</select>";
	}elseif($group_r['Level'] == 2){
		$access_status = "<select name=\"groupaccess\" class=\"text\" id=\"grouplevel\">
		<option value=\"1\">$cp[level1]</option>
		<option value=\"2\" selected=selected>$cp[level2]</option>
		<option value=\"3\">$cp[level3]</option>
		</select>";
	}else{
		$access_status = "<select name=\"groupaccess\" class=\"text\" id=\"grouplevel\">
		<option value=\"1\">$cp[level1]</option>
		<option value=\"2\">$cp[level2]</option>
		<option value=\"3\" selected=selected>$cp[level3]</option>
		</select>";
	}
	#output it.
	$page = new template("../". $template_path ."/cp-modifygroup.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-MODIFYGROUP" => "$cp[modifygroup]",
	"LANG-NOGROUPNAME" => "$cp[nogroupname]",
	"LANG-NOGROUPDESCRIPTION" => "$cp[nogroupdescription]",
	"LANG-LONGGROUPDESCRIPTION" => "$cp[longgroupdescription]",
	"LANG-NOGPROFILE" => "$cp[nogprofilesel]",
	"ID" => "$id",
	"LANG-GROUPNAME" => "$groups[name]",
	"GROUPNAME" => "$group_r[Name]",
	"LANG-GROUPDESCRIPTION" => "$groups[description]",
	"GROUPDESCRIPTION" => "$group_r[Description]",
	"LANG-GROUPSTATUS" => "$groups[groupstat]",
	"GROUPSTATUS" => "$group_status",
	"LANG-GROUPACCESS" => "$cp[groupaccess]",
	"GROUPACCESS" => "$access_status",
	"LANG-GROUPPROFILE" => "$cp[groupprofile]",
	"LANG-GROUPPROFILEHINT" => "$cp[groupprofilehnt]",
	"GROUPPROFILE" => "$gprofile_sel"));
	$page->output();
break;
case 'modify_group_process':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($group_chk == 0){
		$error = $cp['groupnotexist']; 
		echo acp_error($error, "error");
	}
	//get data from form.
	$group = var_cleanup($_POST['group']);
	$description = var_cleanup($_POST['description']);
	$status = var_cleanup($_POST['status']);
	$groupaccess = var_cleanup($_POST['groupaccess']);
	$gprofile = var_cleanup($_POST['gprofile']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#get profile status.
	$db->run = "select access_level from ebb_permission_profile where id='$gprofile'";
	$gprofile_level = $db->result();
	$db->close();
	//do some error checking.
	if ($group == ""){
		$errormsg .= $cp['nogroupname']."\n\n";
		$error = 1;
	}
	if ($description == ""){
		$errormsg .= $cp['nogroupdescription']."\n\n";
		$error = 1;
	}
	if ($status == ""){
		$errormsg .= $cp['statusnotset']."\n\n";
		$error = 1;
	}
	if ($groupaccess == ""){
		$errormsg .= $cp['noaccessset']."\n\n";
		$error = 1;
	}
	if($gprofile == ""){
		$errormsg .= $cp['nogprofilesel']."\n\n";
		$error = 1;
	}
	if(strlen($group) > 30){
		$errormsg .= $cp['longgroupname']."\n\n";
		$error = 1;
	}
	if(strlen($description) > 255){
		$errormsg .= $cp['longgroupdescription']."\n\n";
		$error = 1; 
	}
	if($gprofile_level['access_level'] != $groupaccess){
		$errormsg .= $cp['invalidprofilecho']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query.
		$db->run = "update ebb_groups SET Name='$group', Description='$description', Enrollment='$status', Level='$groupaccess', permission_type='$gprofile' where id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Group: $group", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: groupcp.php?action=groupsetup");
	}
break;
case 'group_delete':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($group_chk == 0){
		$error = $cp['groupnotexist']; 
		echo acp_error($error, "error");
	}
	//see if user is trying to remove the default groups, if so cancel that action!
	if (($id == 1) OR ($id == 2)){
		$error = $cp['nodelgroup'];
		echo acp_error($error, "error");
	}
	#make sure no one is a member of this group first.
	$db->run = "select gid from ebb_group_users where gid='$id'";
	$group_usr_chk = $db->num_results();
	$db->close();
	if($group_usr_chk == 1){
		$error = $cp['userexistgroup'];
		echo acp_error($error, "error");
	}
	//proces query.
	$db->run = "delete from ebb_groups where id='$id'";
	$db->query();
	$db->close();
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("deleted a Group", "$logged_user", "$acp_date", "$ip");
	//bring user back
	header("Location: groupcp.php");
break;
case 'group_memberlist':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($group_chk == 0){
		$error = $cp['groupnotexist']; 
		echo acp_error($error, "error");
	}
	#display group memberlist.
	$groupmemberlist = admin_view_group();
break;
case 'groupmember_remove':
	#see if user added the Group ID or not.
	if((!isset($_GET['gid'])) or (empty($_GET['gid']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$gid = var_cleanup($_GET['gid']);
	}
	#see if user added the username or not.
	if((!isset($_GET['u'])) or (empty($_GET['u']))){
		$error = $login['nouser'];
		echo acp_error($error, "error");
	}else{
		$u = var_cleanup($_GET['u']);
	}
	//check to see if this move will remove all level 1 users.
	$db->run = "select gid from ebb_group_users where gid='$gid'";
	$admin_num_chk = $db->num_results();
	$db->close();
	if (($gid == 1) AND ($admin_num_chk == 1)){
		$error = $cp['nouserdelete'];
		echo acp_error($error, "error");
	}
	//proces query.
	$db->run = "delete from ebb_group_users where gid='$gid' AND Username='$u'";
	$db->query();
	$db->close();
	//change user back to regular status.
	$db->run = "update ebb_users SET Status='Member' where Username='$u'";
	$db->query();
	$db->close();
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("demoted a Group User: $u", "$logged_user", "$acp_date", "$ip");
	//bring user back
	header("Location: groupcp.php?action=group_memberlist&id=$gid");
break;
case 'grouppermission':
	$sel_group = select_group("Permission");
break;
case 'grouprights':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#dispaly group permission list.
	$group_rights = group_permission();
break;
case 'grouprights_process':
	#check for access type.
	if((!isset($_GET['type'])) or (empty($_GET['type']))){
		$error = $cp['noaccesstype'];
		echo acp_error($error, "error");
	}else{
		$type = var_cleanup($_GET['type']);
	}
	#check for access command.
	if((!isset($_GET['stat'])) or (empty($_GET['stat']))){
		$error = $cp['nogroupcmd'];
		echo acp_error($error, "error");
	}else{
		$stat = var_cleanup($_GET['stat']);
	}
	#check for Group ID.
	if((!isset($_GET['gid'])) or (empty($_GET['gid']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$gid = var_cleanup($_GET['gid']);
	}
	#check for Board ID.
	if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
		$error = $txt['nobid'];
		echo acp_error($error, "error");
	}else{
		$bid = var_cleanup($_GET['bid']);
	}
	#see what to do based on command.
	if($stat == "grant"){
		$db->run = "insert into ebb_grouplist (group_id, board_id, type) values('$gid', '$bid', '$type')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Granted Group Access to Board", "$logged_user", "$acp_date", "$ip");
	}else{
		$db->run = "delete from ebb_grouplist where group_id='$gid' and board_id='$bid'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Removed Access to Board", "$logged_user", "$acp_date", "$ip");
	}
	//bring user back
	header("Location: groupcp.php?action=grouprights&id=$gid");
break;
case 'pendinglist':
	$sel_group = select_group("Pendinglist");
break;
case 'pendingview':
	#see if user added the Group ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nogid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($group_chk == 0){
		$error = $cp['groupnotexist']; 
		echo acp_error($error, "error");
	}
	$pendingusers = admin_grouppending();
break;
case 'pending_stat':
	$accept = var_cleanup($_GET['accept']);
	$gid = var_cleanup($_GET['gid']);
	$u = var_cleanup($_GET['u']);
	#error check.
	if (($accept == "") or ($gid == "") or ($u == "")){
		header("Location: groupcp.php"); 
	}
	if($accept == 1){
		//proces query.
		$db->run = "update ebb_group_users SET Status='Active' where gid='$gid' AND Username='$u'";
		$db->query();
		$db->close();
		//change user to group status.
		$db->run = "update ebb_users SET Status='groupmember' where Username='$u'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Granted User Group Status: $u", "$logged_user", "$acp_date", "$ip");
	}else{
		//proces query.
		$db->run = "delete from ebb_group_users where gid='$gid' AND Username='$u'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Denied User Group Status: $u", "$logged_user", "$acp_date", "$ip");
	}
	//bring user back
	header("Location: groupcp.php");
break;
case 'group_adduser':
	$user = var_cleanup($_POST['user']);
	$id = var_cleanup($_POST['id']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	#check for existing user.
	$db->run = "select Username from ebb_users where Username='$user'";
	$usr_chk = $db->num_results();
	$db->close();
	#check for existing group.
	$db->run = "select id from ebb_groups where id='$id'";
	$group_chk = $db->num_results();
	$db->close();
	if($usr_chk == 0){
		$error = $userinfo['usernotexist'];
		echo acp_error($error, "error");
	}
	if($group_chk == 0){
		$error = $cp['groupnotexist'];
		echo acp_error($error, "error");
	}
	if($user == ""){
		$errormsg = $cp['nousername_group']."\n\n";
		$error = 1;
	}
	if(strlen($user) > 25){
		$errormsg .= $cp['longusername']."\n\n";
		$error = 1; 
	}
	if($id == ""){
		$errormsg .= $txt['nogid']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//proces query.
		$db->run = "insert into ebb_group_users (Status, gid, Username) values('Active', '$id', '$user')";
		$db->query();
		$db->close();
		//change user to group status.
		$db->run = "update ebb_users SET Status='groupmember' where Username='$user'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Granted User Group Status: $user", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: groupcp.php");
	}
break;
case 'manageprofile':
	$profilemanage = group_Profile_manager();
break;
case 'new_profile':
	#see if type was defined.
	if((isset($_GET['type'])) or (!empty($_GET['type']))){
		$type = var_cleanup($_GET['type']);
	}else{
		$error = $cp['noprofiletype']; 
		echo acp_error($error, "error");
	}
	#output new profile form.
	$newprofile = group_Profile_list($type);
break;
case 'new_profile_process':
	#see if type was defined.
	if((isset($_GET['type'])) or (!empty($_GET['type']))){
		$type = var_cleanup($_GET['type']);
	}else{
		$error = $cp['noprofiletype']; 
		echo acp_error($error, "error");
	}
	#perform action based on profile type.
	if($type == 1){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$manage_boards = var_cleanup($_POST['manage_boards']);
		$prune_boards = var_cleanup($_POST['prune_boards']);
		$manage_groups = var_cleanup($_POST['manage_groups']);
		$mass_email = var_cleanup($_POST['mass_email']);
		$word_censor = var_cleanup($_POST['word_censor']);
		$manage_smiles = var_cleanup($_POST['manage_smiles']);
		$modify_settings = var_cleanup($_POST['modify_settings']);
		$manage_styles = var_cleanup($_POST['manage_styles']);
		$view_phpinfo = var_cleanup($_POST['view_phpinfo']);
		$check_updates = var_cleanup($_POST['check_updates']);
		$see_acp_log = var_cleanup($_POST['see_acp_log']);
		$clear_acp_log = var_cleanup($_POST['clear_acp_log']);
		$manage_banlist = var_cleanup($_POST['manage_banlist']);
		$manage_users = var_cleanup($_POST['manage_users']);
		$prune_users = var_cleanup($_POST['prune_users']);
		$manage_blacklist = var_cleanup($_POST['manage_blacklist']);
		$manage_ranks = var_cleanup($_POST['manage_ranks']);
		$manage_warnlog = var_cleanup($_POST['manage_warnlog']);
		$activate_users = var_cleanup($_POST['activate_users']);	
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($manage_boards == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_boards']."\n\n";
			$error = 1;	
		}
		if($prune_boards == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['prune_boards']."\n\n";
			$error = 1;	
		}
		if($manage_groups == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_groups']."\n\n";
			$error = 1;	
		}
		if($mass_email == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['mass_email']."\n\n";
			$error = 1;	
		}
		if($word_censor == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['word_censor']."\n\n";
			$error = 1;	
		}
		if($manage_smiles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_smiles']."\n\n";
			$error = 1;	
		}
		if($modify_settings == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['modify_settings']."\n\n";
			$error = 1;	
		}
		if($manage_styles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_styles']."\n\n";
			$error = 1;	
		}
		if($view_phpinfo == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_phpinfo']."\n\n";
			$error = 1;	
		}
		if($check_updates == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['check_updates']."\n\n";
			$error = 1;	
		}
		if($see_acp_log == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['see_acp_log']."\n\n";
			$error = 1;	
		}
		if($clear_acp_log == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['clear_acp_log']."\n\n";
			$error = 1;	
		}
		if($manage_banlist == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_banlist']."\n\n";
			$error = 1;	
		}
		if($manage_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_users']."\n\n";
			$error = 1;	
		}
		if($manage_blacklist == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_blacklist']."\n\n";
			$error = 1;	
		}
		if($manage_ranks == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_ranks']."\n\n";
			$error = 1;	
		}
		if($manage_warnlog == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_warnlog']."\n\n";
			$error = 1;	
		}
		if($activate_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['activate_users']."\n\n";
			$error = 1;	
		}
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//add profile to profile table.
			$db->run = "insert into ebb_permission_profile (profile, access_level) values('$profilename', '$type')";
			$db->query();
			$db->close();
			#get profile ID.
			$db->run = "select id from ebb_permission_profile ORDER BY id DESC limit 1";
			$gprofile_id = $db->result();
			$db->close();
			//add values into data table for profile.
			$db->run = "insert into ebb_permission_data (profile, permission, set_value) values('$gprofile_id[id]', '1', '$manage_boards'),
('$gprofile_id[id]', '2', '$prune_boards'),
('$gprofile_id[id]', '3', '$manage_groups'),
('$gprofile_id[id]', '4', '$mass_email'),
('$gprofile_id[id]', '5', '$word_censor'),
('$gprofile_id[id]', '6', '$manage_smiles'),
('$gprofile_id[id]', '7', '$modify_settings'),
('$gprofile_id[id]', '8', '$manage_styles'),
('$gprofile_id[id]', '9', '$view_phpinfo'),
('$gprofile_id[id]', '10', '$check_updates'),
('$gprofile_id[id]', '11', '$see_acp_log'),
('$gprofile_id[id]', '12', '$clear_acp_log'),
('$gprofile_id[id]', '13', '$manage_banlist'),
('$gprofile_id[id]', '14', '$manage_users'),
('$gprofile_id[id]', '15', '$prune_users'),
('$gprofile_id[id]', '16', '$manage_blacklist'),
('$gprofile_id[id]', '17', '$manage_ranks'),
('$gprofile_id[id]', '18', '$manage_warnlog'),
('$gprofile_id[id]', '19', '$activate_users'),
('$gprofile_id[id]', '20', '1'),
('$gprofile_id[id]', '21', '1'),
('$gprofile_id[id]', '22', '1'),
('$gprofile_id[id]', '23', '1'),
('$gprofile_id[id]', '24', '1'),
('$gprofile_id[id]', '25', '1'),
('$gprofile_id[id]', '26', '1'),
('$gprofile_id[id]', '27', '1'),
('$gprofile_id[id]', '28', '1'),
('$gprofile_id[id]', '29', '1'),
('$gprofile_id[id]', '30', '1'),
('$gprofile_id[id]', '31', '1'),
('$gprofile_id[id]', '32', '1'),
('$gprofile_id[id]', '33', '1'),
('$gprofile_id[id]', '34', '1'),
('$gprofile_id[id]', '35', '1'),
('$gprofile_id[id]', '36', '1'),
('$gprofile_id[id]', '37', '1'),
('$gprofile_id[id]', '38', '1'),
('$gprofile_id[id]', '39', '1')";
			$db->query();
			$db->close();			
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Added New Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}		
	}elseif($type == 2){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$edit_topics = var_cleanup($_POST['edit_topics']);
		$delete_topics = var_cleanup($_POST['delete_topics']);
		$lock_topics = var_cleanup($_POST['lock_topics']);
		$move_topics = var_cleanup($_POST['move_topics']);
		$view_ips = var_cleanup($_POST['view_ips']);
		$warn_users = var_cleanup($_POST['warn_users']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($edit_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['edit_topics']."\n\n";
			$error = 1;	
		}
		if($delete_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['delete_topics']."\n\n";
			$error = 1;	
		}
		if($lock_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['lock_topics']."\n\n";
			$error = 1;	
		}
		if($move_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['move_topics']."\n\n";
			$error = 1;	
		}
		if($view_ips == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_ips']."\n\n";
			$error = 1;	
		}
		if($warn_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['warn_users']."\n\n";
			$error = 1;	
		}	
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//add profile to profile table.
			$db->run = "insert into ebb_permission_profile (profile, access_level) values('$profilename', '$type')";
			$db->query();
			$db->close();
			#get profile ID.
			$db->run = "select id from ebb_permission_profile ORDER BY id DESC limit 1";
			$gprofile_id = $db->result();
			$db->close();
			//add values into data table for profile.
			$db->run = "insert into ebb_permission_data (profile, permission, set_value) values('$gprofile_id[id]', '20', '$edit_topics'),
('$gprofile_id[id]', '21', '$delete_topic'),
('$gprofile_id[id]', '22', '$lock_topics'),
('$gprofile_id[id]', '23', '$move_topics'),
('$gprofile_id[id]', '24', '$view_ips'),
('$gprofile_id[id]', '25', '$warn_users'),
('$gprofile_id[id]', '26', '1'),
('$gprofile_id[id]', '27', '1'),
('$gprofile_id[id]', '28', '1'),
('$gprofile_id[id]', '29', '1'),
('$gprofile_id[id]', '30', '1'),
('$gprofile_id[id]', '31', '1'),
('$gprofile_id[id]', '32', '1'),
('$gprofile_id[id]', '33', '1'),
('$gprofile_id[id]', '34', '1'),
('$gprofile_id[id]', '35', '1'),
('$gprofile_id[id]', '36', '1'),
('$gprofile_id[id]', '37', '1'),
('$gprofile_id[id]', '38', '1'),
('$gprofile_id[id]', '39', '1')";
			$db->query();
			$db->close();			
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Added New Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}
	}elseif($type == 3){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$attach_files = var_cleanup($_POST['attach_files']);
		$pm_access = var_cleanup($_POST['pm_access']);
		$search_board = var_cleanup($_POST['search_board']);
		$download_files = var_cleanup($_POST['download_files']);
		$custom_titles = var_cleanup($_POST['custom_titles']);
		$view_profile = var_cleanup($_POST['view_profile']);
		$use_avatars = var_cleanup($_POST['use_avatars']);
		$use_signatures = var_cleanup($_POST['use_signatures']);
		$join_groups = var_cleanup($_POST['join_groups']);
		$create_poll = var_cleanup($_POST['create_poll']);
		$vote_poll = var_cleanup($_POST['vote_poll']);
		$new_topic = var_cleanup($_POST['new_topic']);
		$reply = var_cleanup($_POST['reply']);
		$important_topic = var_cleanup($_POST['important_topic']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($attach_files == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['attach_files']."\n\n";
			$error = 1;	
		}
		if($pm_access == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['pm_access']."\n\n";
			$error = 1;	
		}
		if($search_board == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['search_board']."\n\n";
			$error = 1;	
		}
		if($download_files == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['download_files']."\n\n";
			$error = 1;	
		}
		if($custom_titles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['custom_titles']."\n\n";
			$error = 1;	
		}
		if($view_profile == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_profile']."\n\n";
			$error = 1;	
		}
		if($use_avatars == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['use_avatars']."\n\n";
			$error = 1;	
		}
		if($use_signatures == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['use_signatures']."\n\n";
			$error = 1;	
		}
		if($join_groups == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['join_groups']."\n\n";
			$error = 1;	
		}
		if($create_poll == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['create_poll']."\n\n";
			$error = 1;	
		}
		if($vote_poll == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['vote_poll']."\n\n";
			$error = 1;	
		}
		if($new_topic == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['new_topic']."\n\n";
			$error = 1;	
		}
		if($reply == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['reply']."\n\n";
			$error = 1;	
		}
		if($important_topic == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['important_topic']."\n\n";
			$error = 1;	
		}
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//add profile to profile table.
			$db->run = "insert into ebb_permission_profile (profile, access_level) values('$profilename', '$type')";
			$db->query();
			$db->close();
			#get profile ID.
			$db->run = "select id from ebb_permission_profile ORDER BY id DESC limit 1";
			$gprofile_id = $db->result();
			$db->close();
			//add values into data table for profile.
			$db->run = "insert into ebb_permission_data (profile, permission, set_value) values('$gprofile_id[id]', '26', '$attach_files'),
('$gprofile_id[id]', '27', '$pm_access'),
('$gprofile_id[id]', '28', '$search_board'),
('$gprofile_id[id]', '29', '$download_files'),
('$gprofile_id[id]', '30', '$custom_titles'),
('$gprofile_id[id]', '31', '$view_profile'),
('$gprofile_id[id]', '32', '$use_avatars'),
('$gprofile_id[id]', '33', '$use_signatures'),
('$gprofile_id[id]', '34', '$join_groups'),
('$gprofile_id[id]', '35', '$create_poll'),
('$gprofile_id[id]', '36', '$vote_poll'),
('$gprofile_id[id]', '37', '$new_topic'),
('$gprofile_id[id]', '38', '$reply'),
('$gprofile_id[id]', '39', '$important_topic')";
			$db->query();
			$db->close();			
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Added New Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}
	}
break;
case 'profile_modify':
	#see if type was defined.
	if((isset($_GET['id'])) or (!empty($_GET['id']))){
		$id = var_cleanup($_GET['id']);
	}else{
		$error = $cp['noprofileid']; 
		echo acp_error($error, "error");
	}
	#get profile name and access type.
    $db->run = "select id, profile, access_level from ebb_permission_profile where id='$id'";
	$gprofile_r = $db->result();
	$db->close();	
	#output new profile form.
	$newprofile = group_Profile_edit($gprofile_r['id'], $gprofile_r['profile'], $gprofile_r['access_level']);
break;
case 'profile_modify_process':
	#see if type was defined.
	if((isset($_GET['type'])) or (!empty($_GET['type']))){
		$type = var_cleanup($_GET['type']);
	}else{
		$error = $cp['noprofiletype']; 
		echo acp_error($error, "error");
	}
	
	#see if GPID was defined.
	if((isset($_GET['gpid'])) or (!empty($_GET['gpid']))){
		$gpid = var_cleanup($_GET['gpid']);
	}else{
		$error = $cp['noprofileid'];
		echo acp_error($error, "error");
	}
	
	#perform action based on profile type.
	if($type == 1){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$manage_boards = var_cleanup($_POST['manage_boards']);
		$prune_boards = var_cleanup($_POST['prune_boards']);
		$manage_groups = var_cleanup($_POST['manage_groups']);
		$mass_email = var_cleanup($_POST['mass_email']);
		$word_censor = var_cleanup($_POST['word_censor']);
		$manage_smiles = var_cleanup($_POST['manage_smiles']);
		$modify_settings = var_cleanup($_POST['modify_settings']);
		$manage_styles = var_cleanup($_POST['manage_styles']);
		$view_phpinfo = var_cleanup($_POST['view_phpinfo']);
		$check_updates = var_cleanup($_POST['check_updates']);
		$see_acp_log = var_cleanup($_POST['see_acp_log']);
		$clear_acp_log = var_cleanup($_POST['clear_acp_log']);
		$manage_banlist = var_cleanup($_POST['manage_banlist']);
		$manage_users = var_cleanup($_POST['manage_users']);
		$prune_users = var_cleanup($_POST['prune_users']);
		$manage_blacklist = var_cleanup($_POST['manage_blacklist']);
		$manage_ranks = var_cleanup($_POST['manage_ranks']);
		$manage_warnlog = var_cleanup($_POST['manage_warnlog']);
		$activate_users = var_cleanup($_POST['activate_users']);	
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($manage_boards == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_boards']."\n\n";
			$error = 1;	
		}
		if($prune_boards == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['prune_boards']."\n\n";
			$error = 1;	
		}
		if($manage_groups == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_groups']."\n\n";
			$error = 1;	
		}
		if($mass_email == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['mass_email']."\n\n";
			$error = 1;	
		}
		if($word_censor == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['word_censor']."\n\n";
			$error = 1;	
		}
		if($manage_smiles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_smiles']."\n\n";
			$error = 1;	
		}
		if($modify_settings == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['modify_settings']."\n\n";
			$error = 1;	
		}
		if($manage_styles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_styles']."\n\n";
			$error = 1;	
		}
		if($view_phpinfo == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_phpinfo']."\n\n";
			$error = 1;	
		}
		if($check_updates == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['check_updates']."\n\n";
			$error = 1;	
		}
		if($see_acp_log == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['see_acp_log']."\n\n";
			$error = 1;	
		}
		if($clear_acp_log == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['clear_acp_log']."\n\n";
			$error = 1;	
		}
		if($manage_banlist == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_banlist']."\n\n";
			$error = 1;	
		}
		if($manage_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_users']."\n\n";
			$error = 1;	
		}
		if($manage_blacklist == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_blacklist']."\n\n";
			$error = 1;	
		}
		if($manage_ranks == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_ranks']."\n\n";
			$error = 1;	
		}
		if($manage_warnlog == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['manage_warnlog']."\n\n";
			$error = 1;	
		}
		if($activate_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['activate_users']."\n\n";
			$error = 1;	
		}
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//update profile name.
			$db->run = "UPDATE ebb_permission_profile SET profile='$profilename' WHERE id='$gpid'";
			$db->query();
			$db->close();
			//update values from data table.
			$db->run = "update ebb_permission_data SET set_value='$manage_boards' where profile='$gpid' and permission='1'";
			$db->query();
			$db->close();	
			$db->run = "update ebb_permission_data SET set_value='$prune_boards' where profile='$gpid' and permission='2'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_groups' where profile='$gpid' and permission='3'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$mass_email' where profile='$gpid' and permission='4'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$word_censor' where profile='$gpid' and permission='5'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_smiles' where profile='$gpid' and permission='6'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$modify_settings' where profile='$gpid' and permission='7'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_styles' where profile='$gpid' and permission='8'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$view_phpinfo' where profile='$gpid' and permission='9'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$check_updates' where profile='$gpid' and permission='10'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$see_acp_log' where profile='$gpid' and permission='11'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$clear_acp_log' where profile='$gpid' and permission='12'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_banlist' where profile='$gpid' and permission='13'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_users' where profile='$gpid' and permission='14'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$prune_users' where profile='$gpid' and permission='15'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_blacklist' where profile='$gpid' and permission='16'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_ranks' where profile='$gpid' and permission='17'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$manage_warnlog' where profile='$gpid' and permission='18'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$activate_users' where profile='$gpid' and permission='19'";
			$db->query();
			$db->close();		
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Modified Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}		
	}elseif($type == 2){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$edit_topics = var_cleanup($_POST['edit_topics']);
		$delete_topics = var_cleanup($_POST['delete_topics']);
		$lock_topics = var_cleanup($_POST['lock_topics']);
		$move_topics = var_cleanup($_POST['move_topics']);
		$view_ips = var_cleanup($_POST['view_ips']);
		$warn_users = var_cleanup($_POST['warn_users']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($edit_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['edit_topics']."\n\n";
			$error = 1;	
		}
		if($delete_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['delete_topics']."\n\n";
			$error = 1;	
		}
		if($lock_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['lock_topics']."\n\n";
			$error = 1;	
		}
		if($move_topics == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['move_topics']."\n\n";
			$error = 1;	
		}
		if($view_ips == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_ips']."\n\n";
			$error = 1;	
		}
		if($warn_users == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['warn_users']."\n\n";
			$error = 1;	
		}	
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			#update profile name.
			$db->run = "UPDATE ebb_permission_profile SET profile='$profilename' WHERE id='$gpid'";
			$db->query();
			$db->close();
			//update values from data table.
			$db->run = "update ebb_permission_data SET set_value='$edit_topics' where profile='$gpid' and permission='20'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$delete_topics' where profile='$gpid' and permission='21'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$lock_topics' where profile='$gpid' and permission='22'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$move_topics' where profile='$gpid' and permission='23'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$view_ips' where profile='$gpid' and permission='24'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$warn_users' where profile='$gpid' and permission='25'";
			$db->query();
			$db->close();			
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Modified Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}
	}elseif($type == 3){
		#define variables.
		$profilename = var_cleanup($_POST['profilename']);
		$attach_files = var_cleanup($_POST['attach_files']);
		$pm_access = var_cleanup($_POST['pm_access']);
		$search_board = var_cleanup($_POST['search_board']);
		$download_files = var_cleanup($_POST['download_files']);
		$custom_titles = var_cleanup($_POST['custom_titles']);
		$view_profile = var_cleanup($_POST['view_profile']);
		$use_avatars = var_cleanup($_POST['use_avatars']);
		$use_signatures = var_cleanup($_POST['use_signatures']);
		$join_groups = var_cleanup($_POST['join_groups']);
		$create_poll = var_cleanup($_POST['create_poll']);
		$vote_poll = var_cleanup($_POST['vote_poll']);
		$new_topic = var_cleanup($_POST['new_topic']);
		$reply = var_cleanup($_POST['reply']);
		$important_topic = var_cleanup($_POST['important_topic']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($profilename)){
			$errormsg = $cp['profileerr']."\n\n";
			$error = 1;	
		}
		if(strlen($profilename) > 30){
			$errormsg .= $cp['longprofilenameerr']."\n\n";
			$error = 1;		
		}
		if($attach_files == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['attach_files']."\n\n";
			$error = 1;	
		}
		if($pm_access == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['pm_access']."\n\n";
			$error = 1;	
		}
		if($search_board == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['search_board']."\n\n";
			$error = 1;	
		}
		if($download_files == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['download_files']."\n\n";
			$error = 1;	
		}
		if($custom_titles == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['custom_titles']."\n\n";
			$error = 1;	
		}
		if($view_profile == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['view_profile']."\n\n";
			$error = 1;	
		}
		if($use_avatars == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['use_avatars']."\n\n";
			$error = 1;	
		}
		if($use_signatures == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['use_signatures']."\n\n";
			$error = 1;	
		}
		if($join_groups == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['join_groups']."\n\n";
			$error = 1;	
		}
		if($create_poll == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['create_poll']."\n\n";
			$error = 1;	
		}
		if($vote_poll == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['vote_poll']."\n\n";
			$error = 1;	
		}
		if($new_topic == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['new_topic']."\n\n";
			$error = 1;	
		}
		if($reply == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['reply']."\n\n";
			$error = 1;	
		}
		if($important_topic == ""){
			$errormsg .= $cp['actionerr'].":&nbsp;".$cp['important_topic']."\n\n";
			$error = 1;	
		}
		#see if any errors occured and if so report it.	
		if($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			#update profile name.
			$db->run = "UPDATE ebb_permission_profile SET profile='$profilename' WHERE id='$gpid'";
			$db->query();
			$db->close();
			//update values from data table.
			$db->run = "update ebb_permission_data SET set_value='$attach_files' where profile='$gpid' and permission='26'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$pm_access' where profile='$gpid' and permission='27'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$search_board' where profile='$gpid' and permission='28'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$download_files' where profile='$gpid' and permission='29'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$custom_titles' where profile='$gpid' and permission='30'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$view_profile' where profile='$gpid' and permission='31'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$use_avatars' where profile='$gpid' and permission='32'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$use_signatures' where profile='$gpid' and permission='33'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$join_groups' where profile='$gpid' and permission='34'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$create_poll' where profile='$gpid' and permission='35'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$vote_poll' where profile='$gpid' and permission='36'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$new_topic' where profile='$gpid' and permission='37'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$reply' where profile='$gpid' and permission='38'";
			$db->query();
			$db->close();
			$db->run = "update ebb_permission_data SET set_value='$important_topic' where profile='$gpid' and permission='39'";
			$db->query();
			$db->close();			
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Modified Group Profile: $profilename", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: groupcp.php?action=manageprofile");
		}
	}
break;
case 'profile_delete':
	#see if id was defined.
	if((isset($_GET['id'])) or (!empty($_GET['id']))){
		$id = var_cleanup($_GET['id']);
	}else{
		$error = $cp['noprofileid']; 
		echo acp_error($error, "error");
	}
	#see if any groups use the profile.
	$db->run = "select id from ebb_groups where permission_type='$id'";
	$chk_group = $db->num_results();
	$db->close();
	if($chk_group > 0){
		$error = $cp['inuseprofile'];
		echo acp_error($error, "error");
	}
	#see if profile is a system-based profile.
	$db->run = "select profile, system from ebb_permission_profile where id='$id'";
	$chk_system = $db->result();
	$db->close();
	if($chk_system['system'] == 1){
		$error = $cp['reservedprofile'];
		echo acp_error($error, "error");
	}
	#delete profile from db.	
	$db->run = "delete ebb_permission_profile where id='$id'";
	$db->query();
	$db->close();
	#delete all profile action data associated with the profile.
	$db->run = "delete ebb_permission_data where profile='$id'";
	$db->query();
	$db->close();
	#log this action in the db.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("Deleted Group Profile: $chk_system[profile]", "$logged_user", "$acp_date", "$ip");
	//bring user back
	header("Location: groupcp.php?action=manageprofile");	
break;
default:
	$grouplist = admin_grouplist();
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
