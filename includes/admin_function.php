<?php
if (!defined('IN_EBB') ) {
die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: admin_function.php
Last Modified: 5/22/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

#version checker.
function versionchecker(){

	global $cp, $settings;

	#check with version detail on official website.
	$phpver = phpversion();
	
	#see if php setup has allow_url_fopen set to On or Off.
	if ((@ini_get('allow_url_fopen') == 0) or (strtolower(@ini_get('allow_url_fopen')) == 'off')) {
		$checker = $cp['updateerr'];	
	}else{
		if ($phpver < 5.1){
			$currver = file_get_contents("http://elite-board.us/updates/md5_hash.md5");  
		}else{
			$currver = file_get_contents("http://elite-board.us/updates/md5_hash.md5", FALSE,NULL,0,32);
		}
		#see if the version on file is up to date.
		if ($currver == $settings['version']){
			$checker = $cp['verok'];
		}else{
			$checker = $cp['verold'];
		}	
	}

	return ($checker);
}
#admin add log function.
function acp_log_add($action, $user, $date, $ip){

	global $db, $gmt, $time_format, $cp;

	if ((empty($user)) or (empty($action)) or (empty($date)) or (empty($ip))){
		echo "Function not used correctly!"; 
		exit(); 
	}else{
		#add info to log.
		$db->run = "insert into ebb_cplog (User, Action, Date, IP) values ('$user', '$action', '$date', '$ip')";
		$updater = $db->query();
		$db->close();
	}
}
#admin view log function.
function acp_log_view(){

	global $db, $gmt, $time_format, $cp;

	$db->run = "select User, IP, Date, Action from ebb_cplog ORDER BY Date DESC limit 5";
	$acplog_q = $db->query();
	$acp_ct = $db->num_results();
	$db->close();
	#see if there are many logged reports.
	if($acp_ct == 0){
		$acp_log = "<p class=\"td2\">$cp[noacplog]</p>"; 
	}else{
		$acp_log = '';
		while ($row = mysql_fetch_assoc ($acplog_q)) {
			#output.
			$gmttime = gmdate ($time_format, $row['Date']);
			$acplog_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			$acp_log .= "<p class=\"td2\">$row[User]($row[IP]) on $acplog_date: $row[Action]</p>";
		}
	}
	return ($acp_log);
}
#admin full view log function.
function acp_log_fullview(){

	global $db, $gmt, $time_format, $cp;

   	$db->run = "select User, IP, Date, Action from ebb_cplog ORDER BY Date DESC";
	$acplog_q = $db->query();
	$acp_ct = $db->num_results();
	$db->close();
	#see if there are many logged reports.
	if($acp_ct == 0){
		$acp_log = "<p class=\"td2\">$cp[noacplog]</p>"; 
	}else{ 
		$acp_log = '';
		while ($row = mysql_fetch_assoc ($acplog_q)) {
			#output.
			$gmttime = gmdate ($time_format, $row['Date']);
			$acplog_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			$acp_log .= "<p class=\"td2\">$row[User]($row[IP]) on $acplog_date: $row[Action]</p>";
		}
	}
	return ($acp_log);
}
#admin verify function.
function admin_verify($user, $pass){

	global $db;

	$db->run = "SELECT Username, Status FROM ebb_users WHERE Username='$user' AND Password='$pass'";
	$checkadmin_r = $db->result();
	$user_auth_chk = $db->num_results();
	$db->close();
	#see if user/password combo exists in the db.
	if($user_auth_chk == 0){
		$admin_check = false;
	}else{
		#see if user is not in a group.
		if ($checkadmin_r['Status'] !== "groupmember"){
			$admin_check = false;
		}else{
			$db->run = "SELECT gid FROM ebb_group_users where Username='$checkadmin_r[Username]'";
			$groupuser = $db->result();
			$group_auth_chk = $db->num_results();
			$db->close();
			#see if user belongs to the group.
			if ($group_auth_chk == 1){
				$db->run = "SELECT Level FROM ebb_groups where id='$groupuser[gid]'";
				$level_result = $db->result();
				$group_exists_chk = $db->num_results();
				$db->close();
				#see if the group even exists.
				if($group_exists_chk == 0){
				    $admin_check = false;
				}else{
					#set-up vars
					$access_Level = $level_result['Level'];
				}
			}else{
				$admin_check = false;
			}
		}
		#see if user has a level 1 status, if not deny access.
		if ($access_Level == 1){
			$admin_check = true;
		}else{
			$admin_check = false;
		}
	}
	return ($admin_check);
}
#admin-board manager
function admin_board($type, $bid){

	global $template_path, $title, $cp, $db, $pm, $mod;

	#check to see if type equals nothing.
	if(($type == "") and ($bid == "")){
		$btype = 1;
		$catid = '';
	}else{
		$btype = $type; 
		$catid = $bid;
	}
	#perform loop.
	$db->run = "select id, Board from ebb_boards where type='$btype' and Category='$catid' ORDER BY B_Order";
	$cat_q = $db->query();
	$db->close(); 
	#board manager header.
	$page = new template("../". $template_path ."/cp-boards_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-BOARDS" => "$cp[boardsetup]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-DELPROMPT2" => "$cp[catdelwarning]",
	"LANG-TEXT" => "$cp[boardtext]",
	"LANG-ADDBOARD" => "$cp[addnew]",
	"LANG-NEWCATEGORY" => "$cp[newcategory]",
	"LANG-NEWBOARD" => "$cp[newboard]",
	"LANG-SUBBOARD" => "$cp[newsubboard]"));
	$boardmanager = $page->output();
	while ($row = mysql_fetch_assoc ($cat_q)) {
		#see if board has any sub-boards linking to them.
		$db->run = "SELECT id FROM ebb_boards where Category='$row[id]'";
		$cat_count = $db->num_results();
		$db->close();
		if($cat_count > 0){
			if($btype == 1){
				$board_name = "<a href=\"boardcp.php?type=2&amp;bid=$row[id]\">$row[Board]</a>"; 
			}elseif ($btype == 2){
				$board_name = "<a href=\"boardcp.php?type=3&amp;bid=$row[id]\">$row[Board]</a>"; 
			}else{
				$board_name = "<a href=\"boardcp.php?type=3&amp;bid=$row[id]\">$row[Board]</a>";
			}
		}else{
			$board_name = $row['Board']; 
		}
		#output.
		$page = new template("../". $template_path ."/cp-boards.htm");
		$page->replace_tags(array(
		"BOARDNAME" => "$board_name",
		"BOARDID" => "$row[id]",
		"BOARDTYPE" => "$btype",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"CATEGORYID" => "$catid",
		"LANG-MOVEUP" => "$cp[moveup]",
		"LANG-MOVEDOWN" => "$cp[movedown]"));
		$boardmanager = $page->output();
	}
	#board manager header.
	$page = new template("../". $template_path ."/cp-boards_foot.htm");
	$boardmanager = $page->output();
	return ($boardmanager);
}
#admin-board category list.
function acpcategory_select(){

	global $search, $modify, $db;
	
	$db->run = "SELECT id, Board FROM ebb_boards where type='1'";
	$board_search = $db->query();
	$db->close();
	#start data collecting.
	$boardlist = "<select name=\"catsel\" class=\"text\" id=\"catsel\">\n
	<option value=\"\">$search[selboard]</option>";
	while ($row = mysql_fetch_assoc ($board_search)){
		#see if anything needs to be selected.
		if ($modify['Category'] == $row['id']){
			$selected = "selected=selected";
		}else{
			$selected = '';
		}
		$boardlist .= "<option value=\"$row[id]\" $selected>$row[Board]</option>";
	}
	$boardlist .= "</select>";
	return ($boardlist);
}
#admin-sub-board category list.
function acpboard_select(){

	global $search, $id, $modify, $db;

	#if $id is null, set it to 0.
	if ($id == ""){
		$bid = 0;
	}else{
		$bid = $id; 
	}
   	$db->run = "SELECT id, Board FROM ebb_boards where type='2' or type='3' and id!='$bid'";
	$board_search = $db->query();
	$db->close();

	$boardlist = "<select name=\"catsel\" class=\"text\" id=\"catsel\">
	<option value=\"\">$search[selboard]</option>";
	while ($row = mysql_fetch_assoc ($board_search)){
		#see if anything needs to be selected.
		if ($modify['Category'] == $row['id']){
			$selected = "selected=selected";
		}else{
			$selected = '';
		}
		$boardlist .= "<option value=\"$row[id]\" $selected>$row[Board]</option>";
	}
	$boardlist .= "</select>";
	return ($boardlist);
}
#read access rule.
function board_readaccess(){

	global $permission, $cp;
 
	if ($permission['B_Read'] == 5){
		$read_status = "<select name=\"readaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"0\">$cp[access_all]</option>
		</select>";
	}
	if ($permission['B_Read'] == 1){
		$read_status = "<select name=\"readaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"0\">$cp[access_all]</option>
		</select>";
	}
	if ($permission['B_Read'] == 2){
		$read_status = "<select name=\"readaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"0\">$cp[access_all]</option>
		</select>";
	}
	if ($permission['B_Read'] == 3){
		$read_status = "<select name=\"readaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"0\">$cp[access_all]</option>
		</select>";
	}
	if ($permission['B_Read'] == 0){
		$read_status = "<select name=\"readaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"0\" selected=selected>$cp[access_all]</option>
		</select>";
	}
	return ($read_status);
}
#write access rule.
function board_writeaccess(){

	global $permission, $cp;  

    if ($permission['B_Post'] == 5){
		$write_status = "<select name=\"writeaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Post'] == 1){
		$write_status = "<select name=\"writeaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Post'] == 2){
		$write_status = "<select name=\"writeaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Post'] == 3){
		$write_status = "<select name=\"writeaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Post'] == 4){
		$reply_status = "<select name=\"writeaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($write_status); 
}
#reply access rule.
function board_replyaccess(){ 

	global $permission, $cp;

	if ($permission['B_Reply'] == 5){
		$reply_status = "<select name=\"replyaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Reply'] == 1){
		$reply_status = "<select name=\"replyaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Reply'] == 2){
		$reply_status = "<select name=\"replyaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Reply'] == 3){
		$reply_status = "<select name=\"replyaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Reply'] == 4){
		$reply_status = "<select name=\"replyaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($reply_status);
}

#vote access rule.
function board_voteaccess(){

	global $permission, $cp;

	if ($permission['B_Vote'] == 5){
		$vote_status = "<select name=\"voteaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Vote'] == 1){
		$vote_status = "<select name=\"voteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Vote'] == 2){
		$vote_status = "<select name=\"voteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Vote'] == 3){
		$vote_status = "<select name=\"voteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Vote'] == 4){
		$vote_status = "<select name=\"voteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($vote_status); 
}
#poll access rule
function board_pollaccess(){

	global $permission, $cp;

	if ($permission['B_Poll'] == 5){
		$poll_status = "<select name=\"pollaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Poll'] == 1){
		$poll_status = "<select name=\"pollaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Poll'] == 2){
		$poll_status = "<select name=\"pollaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Poll'] == 3){
		$poll_status = "<select name=\"pollaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Poll'] == 4){
		$poll_status = "<select name=\"pollaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($poll_status);
}
#delete access rule.
function board_deleteaccess(){

	global $permission, $cp;

	if ($permission['B_Delete'] == 5){
		$delete_status = "<select name=\"deleteaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Delete'] == 1){
		$delete_status = "<select name=\"deleteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Delete'] == 2){
		$delete_status = "<select name=\"deleteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Delete'] == 3){
		$delete_status = "<select name=\"deleteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Delete'] == 4){
		$delete_status = "<select name=\"deleteaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	}
	return ($delete_status); 
}
#edit access rule.
function board_editaccess(){

	global $permission, $cp;

	if ($permission['B_Edit'] == 5){
		$edit_status = "<select name=\"editaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Edit'] == 1){
		$edit_status = "<select name=\"editaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Edit'] == 2){
		$edit_status = "<select name=\"editaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Edit'] == 3){
		$edit_status = "<select name=\"editaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Edit'] == 4){
		$edit_status = "<select name=\"editaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($edit_status); 
}
#important access rule.
function board_importantaccess(){

	global $permission, $cp;

	if ($permission['B_Important'] == 5){
		$important_status = "<select name=\"importantaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Important'] == 1){
		$important_status = "<select name=\"importantaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Important'] == 2){
		$important_status = "<select name=\"importantaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Important'] == 3){
		$important_status = "<select name=\"importantaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Important'] == 4){
		$important_status = "<select name=\"importantaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	} 
	return ($important_status); 
}
#attachment access rule.
function board_attachmentaccess(){

	global $permission, $cp;

	if ($permission['B_Attachment'] == 5){
		$attachment_status = "<select name=\"attachmentaccess\" class=\"text\">
		<option value=\"5\" selected=selected>$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Attachment'] == 1){
		$attachment_status = "<select name=\"attachmentaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\" selected=selected>$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Attachment'] == 2){
		$attachment_status = "<select name=\"attachmentaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\" selected=selected>$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Attachment'] == 3){
		$attachment_status = "<select name=\"attachmentaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\" selected=selected>$cp[access_users]</option>
		<option value=\"4\">$cp[access_none]</option>
		</select>";
	}
	if ($permission['B_Attachment'] == 4){
		$attachment_status = "<select name=\"attachmentaccess\" class=\"text\">
		<option value=\"5\">$cp[access_private]</option>
		<option value=\"1\">$cp[access_admin]</option>
		<option value=\"2\">$cp[access_admin_mod]</option>
		<option value=\"3\">$cp[access_users]</option>
		<option value=\"4\" selected=selected>$cp[access_none]</option>
		</select>";
	}
	return ($attachment_status); 
}
#admin prune boardlist.
function prune_boardlist(){

	global $db;

    $db->run = "SELECT id, Board FROM ebb_boards where type='2' or type='3'";
	$cat_q = $db->query();
	$db->close();
	
	$board_select = "<select name=\"boardsel\" class=\"text\">";

	while ($row = mysql_fetch_assoc ($cat_q)){
    	$board_select .= "<option value=\"$row[id]\">$row[Board]</option>";
	}
	$board_select .= "</select>";
	return ($board_select);
}
#admin-group list
function admin_grouplist(){

	global $template_path, $title, $cp, $groups, $db, $pm, $mod;

    $db->run = "select id, Name, Enrollment, Level from ebb_groups";
	$group_q = $db->query();
	$db->close();
	#grouplist manager header.
	$page = new template("../". $template_path ."/cp-groupsetup_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-GROUPSETUP" => "$cp[groupsetup]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-TEXT" => "$cp[grouptxt]",
	"LANG-NEWGROUP" => "$cp[addgroup]",
	"LANG-MANAGEPROFILE" => "$cp[manageprofile]",
	"LANG-GROUPNAME" => "$groups[name]",
	"LANG-GROUPSTATUS" => "$groups[groupstat]",
	"LANG-GROUPACCESS" => "$cp[groupaccess]",
	"LANG-GROUPMEMBERLIST" => "$groups[groupmembers]"));
	$grouplist = $page->output();
	while ($row = mysql_fetch_assoc ($group_q)) {

		if ($row['Enrollment'] == 1){
			$group_status = $groups['open'];
		}elseif($row['Enrollment'] == 0){
			$group_status = $groups['closed'];
		}else{
			$group_status = $cp['grouphidden'];
		}
		#grouplist manager header.
		$page = new template("../". $template_path ."/cp-groupsetup.htm");
		$page->replace_tags(array(
		"GROUPID" => "$row[id]",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"GROUPNAME" => "$row[Name]",
		"GROUPSTATUS" => "$group_status",
		"GROUPACCESSLEVEL" => "$row[Level]",
		"VIEWMEMBERLIST" => "$cp[viewlist]"));
		$grouplist = $page->output();
	}
	#grouplist manager footer.
	$page = new template("../". $template_path ."/cp-groupsetup_foot.htm");
	$grouplist = $page->output();
	return ($grouplist);
}
#admin-grouplist
function admin_view_group(){

	global $title, $txt, $members, $index, $groups, $id, $db, $gmt, $time_format, $template_path, $pm, $cp, $mod;

    $db->run = "select Username, gid from ebb_group_users where gid='$id' and Status='Active'";
	$query = $db->query();
	$gnum = $db->num_results();
	$db->close();
	//If no results are found.
	if ($gnum == 0){
		$page = new template("../". $template_path ."/cp-groupmemberlist_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-GROUPMEMBERLIST" => "$cp[viewlist]",
		"LANG-TEXT" => "$cp[groupmemberlist]",
		"LANG-GROUPMEMBERS" => "$groups[groupmembers]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]",
		"LANG-NOMEMBERS" => "$groups[nomembers]"));
		$groupmemberlist = $page->output();
	}else{
	#group memberlist header.
		$page = new template("../". $template_path ."/cp-groupmemberlist_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-GROUPMEMBERLIST" => "$cp[viewlist]",
		"LANG-DELPROMPT" => "$mod[condel]",
		"LANG-TEXT" => "$cp[groupmemberlist]",
		"LANG-GROUPMEMBERS" => "$groups[groupmembers]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]"));
		$groupmemberlist = $page->output();
		while ($row = mysql_fetch_assoc ($query)){

			$db->run = "select Post_Count, Date_Joined from ebb_users where Username='$row[Username]'";
			$r = $db->result();
			$db->close();
			#setup date formatting.
			$gmttime = gmdate ($time_format, $r['Date_Joined']);
			$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			#Group memberlist data.
			$page = new template("../". $template_path ."/cp-groupmemberlist.htm");
			$page->replace_tags(array(
			"GROUPID" => "$row[gid]",
			"GROUPMEMBER" => "$row[Username]",
			"LANG-REMOVEGROUPMEMBER" => "$cp[removefromgroup]",
			"LANG-PMALT" => "$pm[postpmalt]",
			"LANG-POSTCOUNT" => "$index[posts]",
			"POSTCOUNT" => "$r[Post_Count]",
			"JOINDATE" => "$join_date"));
			$groupmemberlist = $page->output();
		}
		#group memberlist footer.
		$page = new template("../". $template_path ."/cp-groupmemberlist_foot.htm");
		$groupmemberlist = $page->output();
	}
	return ($groupmemberlist);
}
#group profile select box.
function groupProfile_list(){

	global $db, $cp, $group_r;

    $db->run = "select id, profile from ebb_permission_profile";
	$gprofile_q = $db->query();
	$db->close();
	
	$gprofile_sel = "<select name=\"gprofile\" class=\"text\" id=\"gprofile\">\n";

	while ($row = mysql_fetch_assoc ($gprofile_q)){
		#auto select data.
    	if($group_r['permission_type'] == $row['id']){
			$gprofile_sel .= "<option value=\"$row[id]\" selected=selected>$row[profile]</option>\n";
		}else{
			$gprofile_sel .= "<option value=\"$row[id]\">$row[profile]</option>\n";
		}
	}
	$gprofile_sel .= "</select>";
	return ($gprofile_sel);
}
#Group Profile manager.
function group_Profile_manager(){

	global $template_path, $title, $cp, $db, $pm, $mod;

    $db->run = "select id, profile, access_level from ebb_permission_profile";
	$gprofile_q = $db->query();
	$db->close();
	#grouplist manager header.
	$page = new template("../". $template_path ."/cp-manageprofile-head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-MANAGEPROFILE" => "$cp[manageprofile]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-TEXT" => "$cp[profilemanagetxt]",
	"LANG-NEWPROFILE" => "$cp[newprofile]",
	"LANG-ADMINPROFILE" => "$cp[adminprofile]",
	"LANG-MODERATORPROFILE" => "$cp[moderatorprofile]",
	"LANG-MEMBERPROFILE" => "$cp[memberprofile]",	
	"LANG-PROFILE" => "$cp[profilename]",
	"LANG-ACCESSLEVEL" => "$cp[accesslevel]"));
	$profilemanage = $page->output();
	while ($row = mysql_fetch_assoc ($gprofile_q)) {

		#grouplist manager header.
		$page = new template("../". $template_path ."/cp-manageprofile.htm");
		$page->replace_tags(array(
		"PROFILEID" => "$row[id]",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"PROFILENAME" => "$row[profile]",
		"LANG-ACCESSLEVEL" => "$cp[accesslevel]",
		"ACCESSLEVEL" => "$row[access_level]"));
		$profilemanage = $page->output();
	}
	#grouplist manager footer.
	$page = new template("../". $template_path ."/cp-manageprofile-foot.htm");
	$profilemanage = $page->output();
	return ($profilemanage);
}
#profile action list
function group_Profile_list($type){

	global $template_path, $title, $cp, $db, $txt;
	
	#see if profile type was used.
	if($type == ""){
		$error = $cp['noprofiletype'];
		echo acp_error($error, "error");
	}
	#sql query.
    $db->run = "select id, permission from ebb_permission_actions where type='$type'";
	$permission_q = $db->query();
	$db->close();
	#new profile form header.
	$page = new template("../". $template_path ."/cp-newprofile.htm");
	$page->replace_tags(array(
	"TYPE" => "$type",
	"ERROR-PROFILE" => "$cp[profileerr]",
	"ERROR-LONGPROFILENAME" => "$cp[longprofilenameerr]",
	"ERROR-ACTIONS" => "$cp[actionerr]",
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-NEWPROFILE" => "$cp[newprofile]",
	"LANG-TEXT" => "$cp[profilemanagetxt]",
	"LANG-PROFILENAME" => "$cp[profilename]",
	"LANG-ACTIONLIST" => "$cp[availableactions]"));
	$newprofile = $page->output();
	#new profile form data.
	while ($row = mysql_fetch_assoc ($permission_q)) {
 		#get language text from db.
 		$lowercase_action = strtolower($row['permission']);
 		$action_txt = $cp[$lowercase_action];
		#grouplist manager header.
		$page = new template("../". $template_path ."/cp-profileactions.htm");
		$page->replace_tags(array(
		"LANG-ACTION" => "$action_txt",
		"LANG-ACTIONCODE" => "$lowercase_action",
		"LANG-YES" => "$txt[yes]",
		"LANG-NO" => "$txt[no]"));
		$newprofile = $page->output();
	}
	#new profile form footer.
	$page = new template("../". $template_path ."/cp-newprofile-foot.htm");
	$page->replace_tags(array(
	"LANG-SUBMIT" => "$cp[createprofile]"));
	$newprofile = $page->output();
	return ($newprofile);
}
#modify profile function.
function group_Profile_edit($gpid, $gprofilename, $type){

	global $template_path, $title, $cp, $db, $txt;
	
	#see if profile type was used.
	if(($type == "") or ($gprofilename == "") or ($gpid == "")){
		$error = $cp['noprofiletype'];
		echo acp_error($error, "error");
	}
	#new profile form header.
	$page = new template("../". $template_path ."/cp-modifyprofile.htm");
	$page->replace_tags(array(
	"TYPE" => "$type",
	"GPID" => "$gpid",
	"ERROR-PROFILE" => "$cp[profileerr]",
	"ERROR-LONGPROFILENAME" => "$cp[longprofilenameerr]",
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-MODIFYPROFILE" => "$cp[modifyprofile]",
	"LANG-TEXT" => "$cp[profilemanagetxt]",
	"LANG-PROFILENAME" => "$cp[profilename]",
	"PROFILENAME" => "$gprofilename",
	"LANG-ACTIONLIST" => "$cp[availableactions]"));
	$newprofile = $page->output();
	#new profile form data.
    $db->run = "select id, permission from ebb_permission_actions where type='$type'";
	$permission_q = $db->query();
	$db->close();
	while ($row = mysql_fetch_assoc ($permission_q)) {
		#sql query.
	    $db->run = "select permission, set_value from ebb_permission_data where profile='$gpid' and permission='$row[id]'";
		$pdata_r = $db->result();
		$db->close();
 		#get language text from db.
 		$lowercase_action = strtolower($row['permission']);
 		$action_txt = $cp[$lowercase_action];
		#see if the value is 1 or 0.
		if($pdata_r['set_value'] == 1){
			$set_value1 = "checked=checked";
			$set_value0 = "";
		}else{
			$set_value1 = "";
			$set_value0 = "checked=checked";
		}
		#grouplist manager header.
		$page = new template("../". $template_path ."/cp-profileactions-edit.htm");
		$page->replace_tags(array(
		"LANG-ACTION" => "$action_txt",
		"LANG-ACTIONCODE" => "$lowercase_action",
		"LANG-YES" => "$txt[yes]",
		"LANG-NO" => "$txt[no]",
		"VALUE1" => "$set_value1",
		"VALUE0" => "$set_value0"));
		$newprofile = $page->output();
	}
	#new profile form footer.
	$page = new template("../". $template_path ."/cp-modifyprofile-foot.htm");
	$page->replace_tags(array(
	"LANG-SUBMIT" => "$cp[modifyprofile]"));
	$newprofile = $page->output();
	return ($newprofile);
}
#group selection function.
function select_group($type){

	global $template_path, $title, $db, $cp, $groups;
	#see if user added the output type or not.
	if((!isset($type)) or (empty($type))){
		$error = $cp['nogroupsel'];
		echo acp_error($error, "error");
	}
    #sql query.
    $db->run = "select id, Name from ebb_groups";
	$select_q = $db->query();
	$db->close();
	#select group header.
	$page = new template("../". $template_path ."/cp-selectgroup_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-SELECTGROUP" => "$cp[selgroup]",
	"LANG-GROUPLIST" => "$groups[title]"));
	$sel_group = $page->output();
	while ($row = mysql_fetch_assoc ($select_q)){
    	#based on the typr of setup we will determine the link.
    	if($type == "Permission"){
    		$glink = "groupcp.php?action=grouprights&id=$row[id]";
    	}else{
    		$glink = "groupcp.php?action=pendingview&amp;id=$row[id]";    	 
    	}
		#grouplist data.
		$page = new template("../". $template_path ."/cp-selectgroup.htm");
		$page->replace_tags(array(
		"GROUPLINK" => "$glink",
		"GROUPNAME" => "$row[Name]"));
		$sel_group = $page->output();
	}
	#select group footer.
	$page = new template("../". $template_path ."/cp-selectgroup_foot.htm");
	$sel_group = $page->output();
	return($sel_group);
}
#admin-group permission
function group_permission(){

	global $title, $template_path, $id, $db, $cp;

    $db->run = "select id, Board from ebb_boards where type='2' or type='3'";
	$query = $db->query();
	$db->close();
	#group rights header.
	$page = new template("../". $template_path ."/cp-grouppermission_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-GROUPPERMISSION" => "$cp[grouppermission]",
	"LANG-TEXT" => "$cp[grouppermissiontxt]",
	"LANG-GROUPPERMISSION" => "$cp[grouppermission]",
	"LANG-BOARDNAME" => "$cp[boardname]",
	"LANG-GROUPRIGHTS" => "$cp[grouprights]"));
	$group_rights = $page->output();
	while ($row = mysql_fetch_assoc ($query)){

		#private sql.
		$db->run = "select board_id from ebb_grouplist where group_id='$id' and board_id='$row[id]' and type='2'";
		$private_n = $db->num_results();
		$db->close();
		#moderator sql.
		$db->run = "select board_id from ebb_grouplist where group_id='$id' and board_id='$row[id]' and type='1'";
		$mod_n = $db->num_results();
		$db->close();
		#See if a group has private access on the board.
		if($mod_n == 1){
			$private_link = $cp['grantprivateaccess'];
		}elseif($private_n == 0){
			$private_link = "<a href=\"groupcp.php?action=grouprights_process&amp;stat=grant&amp;gid=$id&amp;bid=$row[id]&amp;type=2\">$cp[grantprivateaccess]</a>";
		}else{
		 	$private_link = "<a href=\"groupcp.php?action=grouprights_process&amp;stat=ungrant&amp;gid=$id&amp;bid=$row[id]&amp;type=2\">$cp[ungrantprivateaccess]</a>";
		}		
		#See if a group has moderator control on the board.
		if($private_n == 1){
			$mod_link = $cp['grantaccess'];
		}elseif($mod_n == 0){
			$mod_link = "<a href=\"groupcp.php?action=grouprights_process&amp;stat=grant&amp;gid=$id&amp;bid=$row[id]&amp;type=1\">$cp[grantaccess]</a>";
		}else{
		 	$mod_link = "<a href=\"groupcp.php?action=grouprights_process&amp;stat=ungrant&amp;gid=$id&amp;bid=$row[id]&amp;type=1\">$cp[ungrantaccess]</a>";
		}
		#link value.
		$link = $private_link." - ".$mod_link;
		$page = new template("../". $template_path ."/cp-grouppermission.htm");
		$page->replace_tags(array(
		"LINK" => "$link",
		"BOARDNAME" => "$row[Board]"));
		$group_rights = $page->output();
	}
	#group rights footer.
	$page = new template("../". $template_path ."/cp-grouppermission_foot.htm");
	$group_rights = $page->output();
	return ($group_rights);
}
#admin-pending grouplist
function admin_grouppending(){
	
	global $title, $txt, $members, $index, $id, $db, $gmt, $time_format, $template_path, $pm, $cp;

	$db->run = "select Username, gid from ebb_group_users where gid='$id' and Status='Pending'";
	$query = $db->query();
	$gnum = $db->num_results();
	$db->close();
	//See if no results are found.
	if ($gnum == 0){
		$page = new template("../". $template_path ."/cp-pendinglist_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-GROUPPENDINGLIST" => "$cp[pendinglist]",
		"LANG-NOUSER" => "$cp[nousername_group]",
		"LANG-LONGUSER" => "$cp[longusername]",
		"LANG-TEXT" => "$cp[pendinglisttxt]",
		"LANG-PENDINGLIST" => "$cp[pendinglist]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]",
		"LANG-NOPENDING" => "$cp[nopending]",
		"LANG-ADDUSERTOGROUP" => "$cp[addtogroup]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-SUBMIT" => "$cp[addusergroup]",
		"ID" => "$id"));
		$pendingusers = $page->output();
	}else{
		#pendinglist header.
		$page = new template("../". $template_path ."/cp-pendinglist_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-GROUPPENDINGLIST" => "$cp[pendinglist]",
		"LANG-NOUSER" => "$cp[nousername_group]",
		"LANG-LONGUSER" => "$cp[longusername]",
		"LANG-TEXT" => "$cp[pendinglisttxt]",
		"LANG-PENDINGLIST" => "$cp[pendinglist]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]",
		"LANG-ADDUSERTOGROUP" => "$cp[addtogroup]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-SUBMIT" => "$cp[addusergroup]",
		"ID" => "$id"));
		$pendingusers = $page->output();
		while ($row = mysql_fetch_assoc ($query)){
			#get member's profile data.
			$db->run = "select Post_Count, Date_Joined from ebb_users where Username='$row[Username]'";
			$r = $db->result();
			$db->close();
			#date formatting.
			$gmttime = gmdate ($time_format, $r['Date_Joined']);
			$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			#pendlinglist data.
			$page = new template("../". $template_path ."/cp-pendinglist.htm");
			$page->replace_tags(array(
			"GROUPID" => "$row[gid]",
			"USERNAME" => "$row[Username]",
			"LANG-ACCEPTUSER" => "$cp[pendingaccept]",
			"LANG-DENYUSER" => "$cp[pendingdeny]",
			"LANG-PMALT" => "$pm[postpmalt]",
			"LANG-POSTCOUNT" => "$index[posts]",
			"POSTCOUNT" => "$r[Post_Count]",
			"JOINDATE" => "$join_date"));
			$pendingusers = $page->output();
		}
		#pendinglist footer.
		$page = new template("../". $template_path ."/cp-pendinglist_foot.htm");
		$page->replace_tags(array(
		"LANG-ADDUSERTOGROUP" => "$cp[addtogroup]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-SUBMIT" => "$cp[addusergroup]",
		"ID" => "$id"));
		$pendingusers = $page->output();
	}
	return ($pendingusers);
}
#group select tool during registration.
function group_lister(){

	global $db, $cp, $settings;

	$ustat = "<select name=\"ustat\" class=\"text\">\n";

    $db->run = "select id, Name from ebb_groups";
	$group_q = $db->query();
	$db->close();
	#see if admin set it to non-group.
    if($settings['userstat'] == 0){
		$ustat .= "<option value=\"0\" selected=selected>$cp[regmember]</option>\n";
		while ($row = mysql_fetch_assoc ($group_q)){
			$ustat .= "<option value=\"$row[id]\">$row[Name]</option>\n";
		}
	}else{
		$ustat .= "<option value=\"0\">$cp[regmember]</option>\n";
		while ($row = mysql_fetch_assoc ($group_q)){
			if($settings['userstat'] == $row['id']){
				$ustat .= "<option value=\"$row[id]\" selected=selected>$row[Name]</option>\n";
			}else{
				$ustat .= "<option value=\"$row[id]\">$row[Name]</option>\n";
			}
		}		
	}
	$ustat .= "</select>";
	return ($ustat);
}
#language select function
function acp_lang_select($langsel){

    #see if any settings are set, if not set value at 0.
	if($langsel == ""){
		die('Improper use of function.');
	}
	$lang = "<select name=\"default_lang\" class=\"text\">";
	$handle = opendir("../lang");
	while (($file = readdir($handle))) {
		if (is_file("../lang/$file") && false !== strpos($file, '.lang.php')) {
			$file = str_replace(".lang.php", "", $file);
			if ($langsel == $file){
				$lang .= "<option value=\"$file\" selected=selected>$file</option>";
			}else{
				$lang .= "<option value=\"$file\">$file</option>";
			}
		}
	}
	$lang .= "</select>";
	return ($lang);
}
#attachment whitelist.
function attachment_whitelist(){

	global $cp, $db;

	#create query.
	$db->run = "select id, ext from ebb_attachment_extlist";
	$attach_q = $db->query();
	$db->close();
	#start output.
	$attachment_list = "<select name=\"attachsel\" class=\"text\">";
	while ($row = mysql_fetch_assoc($attach_q)){
		$attachment_list .= "<option value=\"$row[id]\">$row[ext]</option>";
	}
	$attachment_list .= "</select>";
	return ($attachment_list);
}
#admin-smile listing
function admin_smilelisting(){

	global $template_path, $title, $cp, $db, $pm, $attach, $mod, $post;
	
	$db->run = "SELECT id, img_name, code FROM ebb_smiles";
	$query = $db->query();
	$db->close();
	#smile listing header.
	$page = new template("../". $template_path ."/cp-smiles_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-SMILES" => "$post[smiles]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-ADDSMILES" => "$cp[addsmiles]",
	"LANG-SMILE" => "$cp[smiletbl]",
	"LANG-CODE" => "$cp[codetbl]",
	"LANG-FILENAME" => "$attach[filename]"));
	$admin_smiles = $page->output();
	while ($row = mysql_fetch_assoc($query)){
		#smile listing data.
		$page = new template("../". $template_path ."/cp-smiles.htm");
		$page->replace_tags(array(
		"SMILEID" => "$row[id]",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"SMILEFILENAME" => "$row[img_name]",
		"SMILECODE" => "$row[code]"));
		$admin_smiles = $page->output();
	}
	$smiles = acp_smile_installer();
	#smile listing footer.
	$page = new template("../". $template_path ."/cp-smiles_foot.htm");
	$page->replace_tags(array(
	"LANG-SMILEINSTALL" => "$cp[smileinstall]",
	"SMILEINSTALL" => "$smiles"));
	$admin_smiles = $page->output();
	return ($admin_smiles);
}
#style install list function
function acp_smile_installer(){

	global $settings; 

	$handle = opendir("../install");
	$smiles = '';
	$colume = 'Board_Address';
	$settings = board_settings($colume);
	while (($file = readdir($handle))) {
		if (is_file("../install/$file") && false !== strpos($file, '.smile.php')) {
			$file2 = str_replace(".smile.php", "", $file);
			$smiles .= "<div class=\"smileinstaller\">- <a href=\"$settings[Board_Address]/install/$file\">$file2</a></div>";
		}
	}
	return ($smiles);
}

#add-on install list function
function acp_mod_installer(){

	global $settings; 

	$handle = opendir("../install");
	$addon = '';
	$colume = 'Board_Address';
	$settings = board_settings($colume);
	while (($file = readdir($handle))) {
		if (is_file("../install/$file") && false !== strpos($file, '.mod.php')) {
			$file2 = str_replace(".mod.php", "", $file);
			$addon .= "<div class=\"modinstaller\">- <a href=\"$settings[Board_Address]/install/$file\">$file2</a></div>";
		}
	}
	return ($addon);
}

#warning log listor.
function warn_log(){

	global $db, $title, $cp, $warn_log_q, $template_path, $mod;

	#warn log header.
	$page = new template("../". $template_path ."/cp-warnlog_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-WARNLOG" => "$cp[warninglist]",
	"LANG-DELCONFIRM" => "$cp[revoketext]",
	"LANG-CLEARPROMPT" => "$cp[deletewarnlogtxt]",
	"LANG-TEXT" => "$cp[warnlogtxt]",
	"LANG-DELETE" => "$cp[deletewarnlog]",
	"LANG-PROFORMEDBY" => "$cp[warnperformed]",
	"LANG-PROFORMEDTO" => "$cp[warneffecteduser]",
	"LANG-ACTION" => "$cp[warnaction]",
	"LANG-REASON" => "$cp[warnreason]"));
	$warn_log = $page->output();
	while($r = mysql_fetch_assoc($warn_log_q)){
		#get message based on action id.
		if($r['Action'] == 1){
			$action = $mod['actionraise'];
		}elseif($r['Action'] == 2){
			$action = $mod['actionlowered'];
		}elseif($r['Action'] == 3){
			$action = $mod['actionbanned'];
		}elseif($r['Action'] == 4){
			$action = $mod['actionsuspend'];
		}else{
			$action = $mod['actionblank'];
		}
		#warn log data..
		$page = new template("../". $template_path ."/cp-warnlog.htm");
		$page->replace_tags(array(
		"ID" => "$r[id]",
		"LANG-REVOKE" => "$cp[revokeaction]",
		"PERFORMEDBY" => "$r[Authorized]",
		"PERFORMEDTO" => "$r[Username]",
		"ACTION" => "$action",
		"REASON" => "$r[Message]"));
		$warn_log = $page->output();	
	}
	#warn log footer.
	$page = new template("../". $template_path ."/cp-warnlog_foot.htm");
	$warn_log = $page->output();

	return($warn_log);
}
#admin-censor listing
function admin_censorlist(){

	global $template_path, $title, $cp, $db, $pm, $mod;

    $db->run = "SELECT id, Original_Word, action FROM ebb_censor";
	$query = $db->query();
	$num = $db->num_results();
	$db->close();
	//see if theres no entry at all.
	if ($num == 0){
		$page = new template("../". $template_path ."/cp-censorlist_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-CENSORLIST" => "$cp[censor]",
		"LANG-CENSORACTION" => "$cp[censoraction]",
		"LANG-ORIGINALWORD" => "$cp[originalword]",
		"LANG-EMPTYCENSOR" => "$cp[emptycensorlist]",
		"LANG-CENSORACTIONHINT" => "$cp[censoractionhint]",
		"LANG-CENSORBAN" => "$cp[censorban]",
		"LANG-CENSORSPAM" => "$cp[censorspam]",
		"LANG-NOCENSORACTION" => "$cp[nocensoraction]",
		"LANG-NOCENSOR" => "$cp[nocensor]",
		"LANG-LONGCENSOR" => "$cp[longcensor]",
		"LANG-ADDCENSOR" => "$cp[addcensor]",
		"LANG-SUBMIT" => "$cp[submit]"));
		$admin_censorlist = $page->output();
	}else{
		#censorlist header.
		$page = new template("../". $template_path ."/cp-censorlist_head.htm");
		$page->replace_tags(array(
		"LANG-DELPROMPT" => "$mod[condel]",
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-CENSORLIST" => "$cp[censor]",
		"LANG-CENSORACTION" => "$cp[censoraction]",
		"LANG-ORIGINALWORD" => "$cp[originalword]",
		"LANG-NOCENSOR" => "$cp[nocensor]",
		"LANG-LONGCENSOR" => "$cp[longcensor]",
		"LANG-NOCENSORACTION" => "$cp[nocensoraction]"));
		$admin_censorlist = $page->output();
		while ($row = mysql_fetch_assoc($query)){
			#output action.
			if($row['action'] == 1){
				$censor_action = $cp['censorban'];
			}else{
				$censor_action = $cp['censorspam'];			
			}
			#censorlist data.
			$page = new template("../". $template_path ."/cp-censorlist.htm");
			$page->replace_tags(array(
			"CENSORID" => "$row[id]",
			"LANG-DELETE" => "$pm[del]",
			"ORGINALWORD" => "$row[Original_Word]",
			"DESIREDACTION" => "$censor_action"));
			$admin_censorlist = $page->output();
		}
		#censorlist footer.
		$page = new template("../". $template_path ."/cp-censorlist_foot.htm");
		$page->replace_tags(array(
		"LANG-CENSORLIST" => "$cp[censor]",
		"LANG-ADDCENSOR" => "$cp[addcensor]",
		"LANG-CENSORACTION" => "$cp[censoraction]",
		"LANG-CENSORACTIONHINT" => "$cp[censoractionhint]",
		"LANG-CENSORBAN" => "$cp[censorban]",
		"LANG-CENSORSPAM" => "$cp[censorspam]",
		"LANG-SUBMIT" => "$cp[submit]"));
		$admin_censorlist = $page->output();
	}
	return ($admin_censorlist);
}
#admin-rank listing
function admin_ranklisting(){

	global $template_path, $title, $cp, $db, $pm, $index, $mod;

    $db->run = "SELECT id, Name, Post_req FROM ebb_ranks";
	$query = $db->query();
	$db->close();
	#rank listing header.
	$page = new template("../". $template_path ."/cp-rank_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-RANK" => "$cp[ranks]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-ADDRANK" => "$cp[addrank]",
	"LANG-RANKNAME" => "$cp[rankname]",
	"LANG-POSTRULE" => "$cp[postrule]"));
    $admin_rank = $page->output();
	while ($row = mysql_fetch_assoc($query)){
		#rank listing header.
		$page = new template("../". $template_path ."/cp-rank.htm");
		$page->replace_tags(array(
		"RANKID" => "$row[id]",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"RANKNAME" => "$row[Name]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"POSTREQUIREMENTS" => "$row[Post_req]"));
	    $admin_rank = $page->output();
	}
	#rank listing footer.
	$page = new template("../". $template_path ."/cp-rank_foot.htm");
    $admin_rank = $page->output();
	return ($admin_rank);
}
#admin-inactive user listing
function inactive_users(){

	global $template_path, $txt, $title, $members, $inactive_q, $cp, $user_ct, $time_format, $gmt;
	
	#if there aren't any users on the list.
	if($user_ct == 0){
		$page = new template("../". $template_path ."/cp-activateusers_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-ACTIVATEUSER" => "$cp[activateacct]",
		"LANG-STYLENAME" => "$cp[stylename]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-JOINDATE" => "$members[joindate]",
		"LANG-NOINACTIVEUSER" => "$cp[noinactiveusers]"));
		$inactive_list = $page->output();
	}else{
		#inactive userlist header.	 
		$page = new template("../". $template_path ."/cp-activateusers_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-ACTIVATEUSER" => "$cp[activateacct]",
		"LANG-STYLENAME" => "$cp[stylename]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-JOINDATE" => "$members[joindate]"));
		$inactive_list = $page->output();
		while($r = mysql_fetch_assoc($inactive_q)){
        	#date formatting.
			$gmttime = gmdate ($time_format, $r['Date_Joined']);
			$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			#inactive userlist data.	 
			$page = new template("../". $template_path ."/cp-activateusers.htm");
			$page->replace_tags(array(
			"USERID" => "$r[id]",
			"LANG-ACCEPTUSER" => "$cp[pendingaccept]",
			"LANG-DENYUSER" => "$cp[pendingdeny]",
			"USERNAME" => "$r[Username]",
			"JOINDATE" => "$join_date"));
			$inactive_list = $page->output();
		}
		#inactive userlist footer.	 
		$page = new template("../". $template_path ."/cp-activateusers_foot.htm");
		$inactive_list = $page->output();
	}
	return ($inactive_list);
}
#admin-style listing
function admin_stylelisting(){

	global $template_path, $title, $cp, $db, $pm, $mod;

	$db->run = "SELECT id, Name FROM ebb_style";
	$query = $db->query();
	$db->close();
	#style listing header.
	$page = new template("../". $template_path ."/cp-style_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-DELPROMPT" => "$mod[condel]",
	"LANG-STYLES" => "$cp[managestyle]",
	"LANG-STYLENAME" => "$cp[stylename]"));
	$admin_style = $page->output();
	while ($row = mysql_fetch_assoc($query)){
		#style listing data.
		$page = new template("../". $template_path ."/cp-style.htm");
		$page->replace_tags(array(
		"STYLEID" => "$row[id]",
		"LANG-MODIFY" => "$cp[modify]",
		"LANG-DELETE" => "$pm[del]",
		"STYLENAME" => "$row[Name]"));
		$admin_style = $page->output();
	}
	$styler =  acp_style_installer();
	#style listing footer.
	$page = new template("../". $template_path ."/cp-style_foot.htm");
	$page->replace_tags(array(
	"LANG-STYLESINSTALLER" => "$cp[styleinstall]",
	"STYLESINSTALLER" => "$styler"));
	$admin_style = $page->output();
	return ($admin_style);
}
#style install list function
function acp_style_installer(){

	global $settings; 
	$styler = '';
	$handle = opendir("../install");
	#settings.
	$colume = 'Board_Address';
	$settings = board_settings($colume);
	while (($file = readdir($handle))) {
		if (is_file("../install/$file") && false !== strpos($file, '.style.php')) {
			$file2 = str_replace(".style.php", "", $file);
			$styler .= "<div class=\"styleinstaller\"><a href=\"$settings[Board_Address]/install/$file\">$file2</a></div>";
		}
	}
	return ($styler);
}
#admin-ban listing IP's
function admin_banlist_ip(){

	global $cp, $db;

   	$db->run = "SELECT id, ban_item FROM ebb_banlist where ban_type='IP'";
	$query = $db->query();
	$num = $db->num_results();
	$db->close();

	$admin_banlist_ip = "<select name=\"ipsel\" class=\"text\">";

	if ($num == 0){
		$admin_banlist_ip .= "<option value=\"\">$cp[nobanlistip]</option>";
	}else{
		while ($row = mysql_fetch_assoc($query)){
			$admin_banlist_ip .= "<option value=\"$row[id]\">$row[ban_item]</option>";
		}
	}
	$admin_banlist_ip .= "</select>";
	return ($admin_banlist_ip);
}
#admin-ban listing Email
function admin_banlist_email(){

	global $cp, $db;

   	$db->run = "SELECT id, ban_item FROM ebb_banlist where ban_type='Email'";
	$query = $db->query();
	$num = $db->num_results();
	$db->close();

	$admin_banlist_email = "<select name=\"emailsel\" class=\"text\">";
	if ($num == 0){
		$admin_banlist_email .= "<option value=\"\">$cp[nobanlistemail]</option>";
	}else{
		while ($row = mysql_fetch_assoc($query)){
			$admin_banlist_email .= "<option value=\"$row[id]\">$row[ban_item]</option>";
		}
	}
	$admin_banlist_email .= "</select>";
	return ($admin_banlist_email);
}
#admin-username blacklist
function admin_blacklist(){

	global $cp, $db;

   	$db->run = "SELECT id, blacklisted_username FROM ebb_blacklist";
	$query = $db->query();
	$num = $db->num_results();
	$db->close();
    
	$username_blacklist = "<select name=\"blkusersel\" class=\"text\">";
	if ($num == 0){
		$username_blacklist .= "<option value=\"\">$cp[noblacklistednames]</option>";
	}else{
		while ($row = mysql_fetch_assoc($query)){
			$username_blacklist .= "<option value=\"$row[id]\">$row[blacklisted_username]</option>";
		}
	}
	$username_blacklist .= "</select>";
	return ($username_blacklist);
}
?>
