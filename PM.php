<?php
define('IN_EBB', true);
/*
Filename: PM.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
require "phpmailer/class.phpmailer.php";

if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
#get title information.
switch($action){
case 'write':
case 'write_process':
	$pmtitle = $pm['PostPM'];
	$helpTitle = $help['pmcreatetitle'];
	$helpBody = $help['pmcreatebody'];
break;
case 'read':
	$pmtitle = $pm['readpm'];
	$helpTitle = $help['pmreadtitle'];
	$helpBody = $help['pmreadbody'];
break;
case 'reply':
case 'reply_process':
	$pmtitle = $pm['replypm'];
	$helpTitle = $help['pmcreatetitle'];
	$helpBody = $help['pmcreatebody'];
break;
case 'delete':
	$pmtitle = $pm['delpm'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'ban':
case 'ban_process':
	$pmtitle = $pm['banusertitle'];
	$helpTitle = $help['pmbantitle'];
	$helpBody = $help['pmbanbody'];
break;
case 'banlist':
	$pmtitle = $pm['banlist'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'del_ban':
	$pmtitle = $pm['delbanuser'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
default:
	$pmtitle = $menu['pm'];
	$helpTitle = $help['pmtitle'];
	$helpBody = $help['pmbody'];
}
#output header.
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$pmtitle",
  "LANG-HELP-TITLE" => "$helpTitle",
  "LANG-HELP-BODY" => "$helpBody"));

$page->output();
#see if user can access PM.
$permission_chk_pm = access_vaildator($permission_type, 27);
if($permission_chk_pm == 0){
	$error = $pm['pm_access'];
	echo error($error, "error");
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
	}else{
		$error .= "<p class=\"td\">[<a href=\"login.php\">$txt[login]</a>]</p>"; 
	}
	echo error($error, "general");
	#terminate program after message appears.
	exit();
}

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
	$page = new template($template_path ."/top-admin.htm");
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
if (($stat == "Member") OR ($access_level == 2) OR ($access_level == 3)){
	$page = new template($template_path ."/top-logged.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
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
if ($stat == "guest"){
	header("Location: index.php");
}

#call board setting function.
$colume = 'PM_Quota, Archive_Quota, mail_type, per_page';
$settings = board_settings($colume);
#set some posting rules.
$allowsmile = 1;
$allowbbcode = 1;
$allowimg = 0;
switch ($action){
case 'write':
	if(isset($_GET['user'])){
		$user = var_cleanup($_GET['user']);
	}else{
		$user = '';
	}
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	$page = new template($template_path ."/pm-postpm.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[pm]",
	"LANG-POSTPM" => "$pm[PostPM]",
	"LANG-NOSUBJECT" => "$pm[nosubject]",
	"LANG-NOBODY" => "$pm[nomessage]",
	"LANG-NOSENDER" => "$pm[nosend]",
	"LANG-LONGSUBJECT" => "$pm[longsubject]",
	"LANG-LONGSENDER" => "$pm[longuser]",	
	"BBCODE" => "$bbcode",
	"LANG-SMILES" => "$post[moresmiles]",
	"SMILES" => "$smile",
	"LANG-USERNAME" => "$txt[username]",
	"USERNAME" => "$logged_user",
	"LANG-TO" => "$pm[send]",
	"TO" => "$user",
	"LANG-SUBJECT" => "$pm[subject]",
	"LANG-SENDPM" => "$pm[sendpm]"));
	$page->output();
  break;
  case 'write_process':
	//get the values from the form.
	$send = var_cleanup($_POST['send']);
	$subject = var_cleanup($_POST['subject']);
	$message = var_cleanup($_POST['message']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	
	#SQL to determine if user entered is valid.
	$db->run = "select Username from ebb_users WHERE Username='$send' LIMIT 1";
	$usr_chk = $db->num_results();
	$db->close();
	
	#error check.
	if (empty($send)){
		$errormsg = $pm['nosend']."\n\n";
		$error = 1;
	}
	if($usr_chk == 0){
	    $errormsg .= $login['invaliduser']."\n\n";
	    $error = 1;
	}
	if (empty($subject)){
		$errormsg .= $pm['nosubject']."\n\n";
		$error = 1;
	}
	if (empty($message)){
		$errormsg .= $pm['nomessage']."\n\n";
		$error = 1;
	}
	if(strlen($send) > 25){
		$errormsg .= $pm['longuser']."\n\n";
		$error = 1;
	}
	if(strlen($subject) > 25){
		$errormsg .= $pm['longsubject']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//check settings.
		$values = 'Status';
		#call user function.
		$userpref = user_settings($send, $values);
		if($userpref['Status'] == "groupmember"){
			$db->run = "SELECT gid FROM ebb_group_users where Username='$send'";
			$groupuser = $db->result();
			$group_auth_chk = $db->num_results();
			$db->close();
			if ($group_auth_chk == 1){
				$db->run = "SELECT permission_type FROM ebb_groups where id='$groupuser[gid]'";
				$level_result = $db->result();
				$db->close();
				#set-up var
				$send_permission_type = $level_result['permission_type'];
			}else{
				die('INVALID GROUP ID');
			}
		}else{
			$send_permission_type = 4;
		}
		$permission_chk_pm = access_vaildator($send_permission_type, 27);
		if($permission_chk_pm == 0){
			$error = $pm['pm_access_user'];
			echo error($error, "error");
		}
		//check to see if the from user's inbox is full.
		$db->run = "SELECT id FROM ebb_pm WHERE Reciever='$send' and Folder='Inbox'";
		$check_inbox = $db->num_results();
		$db->close();
		if ($check_inbox == $settings['PM_Quota']){
			$error = $pm['overquota'];
			echo error($error, "error");
		}
		//check to see if this user is on the ban list.
		$db->run = "SELECT id FROM ebb_pm_banlist WHERE Banned_User='$logged_user' and Ban_Creator='$send'";
		$check_ban_r = $db->num_results();
		$db->close();
		if ($check_ban_r == 1){
			$error = $pm['blocked'];
			echo error($error, "general");
		}else{
			$time = time();
			//process query
			$db->run = "insert into ebb_pm (Sender, Reciever, Subject, Folder, Message, Date) values ('$logged_user', '$send', '$subject', 'Inbox', '$message', '$time')";
			$db->query();
			$db->close();
			//email user if they have decided
			$db->run = "SELECT PM_Notify, Email FROM ebb_users WHERE Username='$send'";
			$notify = $db->result();
			$db->close();
			if ($notify['PM_Notify'] == 1){
				//get pm id.
				$db->run = "SELECT id FROM ebb_pm ORDER BY id DESC limit 1";
				$pm_id_result = $db->result();
				$db->close();
				//grab values from PM message.
				$db->run = "select Reciever, Sender, Subject, id from ebb_pm where id='$pm_id_result[id]'";
				$pm_data = $db->result();
				$db->close();
				//pull-up language mail file.
				require "lang/".$lang.".email.php";
				$pm_message = pm_notify();
				//get ready to send mail.
				if($settings['mail_type'] == 0){
					#call smtp class file.
					require "includes/smtp.php";
					//call up the smtp class.
					$mailer = new ebbmail;
					$mailer->ebbmail();
					//setup the subject of this newsletter.
					$mailer->Subject = $pm['pmsubject'];
					//get body of newsletter.
					$mailer->Body = $pm_message;
					//add users to the email list
					$mailer->AddBCC($notify['Email']);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					@mail($notify['Email'], $pm['pmsubject'], $pm_message, $headers);
				}
			}
			//bring user back
			header("Location: PM.php");
		}
	}
break;
case 'read':
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nopmid'];
		echo error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']); 
	}
	$db->run = "select id, Read_Status, Sender, Reciever, Folder, Subject, Message, Date from ebb_pm WHERE id='$id'";
	$pm_r = $db->result();
	$chk_pm = $db->num_results();
	$db->close();
	#see if pm message exist.
	if($chk_pm == 0){
		$error = $pm['pm404'];
		echo error($error, "error"); 
	}
	//see if pm message belong to the right user.
	if ($pm_r['Reciever'] !== $logged_user){
		$error = $pm['accessdenied'];
		echo error($error, "error"); 
	}
	//mark as read
	if (empty($pm_r['Read_status'])){
		$db->run = "update ebb_pm SET Read_Status='old' where id='$id'";
		$db->query();
		$db->close();
	}
	//bbcode & other formating processes.
	$string = $pm_r['Message'];
	$string = smiles($string);
	$string = BBCode($string, true);
	$string = language_filter($string, 1);
	$string = nl2br($string);
	//get the date
	$gmttime = gmdate ($time_format, $pm_r['Date']);
	$readpm_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
	$db->run = "select Sig from ebb_users WHERE Username = '$pm_r[Sender]'";
	$user = $db->result();
	$db->close();
	//get sig.
	if(empty($user['Sig'])){
		$sig = "";
	}else{
		$pmsig = nl2br(smiles(BBCode(language_filter($user['Sig'], 1), true))); 
		$sig = "_____________<br />".$pmsig;
	}
	#output html.
	if($pm_r['Folder'] == "Archive"){
		$page = new template($template_path ."/pm-read-archive.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"LANG-READPM" => "$pm[readpm]",
		"LANG-DELPROMPT" => "$pm[confirmdelete]",
		"LANG-MOVEPROMPT" => "$pm[moveconfirm]",
		"LANG-REPLY" => "$pm[replyalt]",
		"ID" => "$pm_r[id]",
		"LANG-FROM" => "$pm[from]",
		"FROM" => "$pm_r[Sender]",
		"LANG-BANUSER" => "$pm[banuser]",
		"LANG-TO" => "$pm[to]",
		"TO" => "$pm_r[Reciever]",
		"LANG-DATE" => "$pm[date]",
		"DATE" => "$readpm_date",
		"LANG-SUBJECT" => "$pm[subject]",
		"SUBJECT" => "$pm_r[Subject]",
		"PM-MESSAGE" => "$string",
		"SIGNATURE" => "$sig",
		"LANG-DELETEPM" => "$pm[delpm]",
		"LANG-MOVEPM" => "$pm[movemsg]"));
		$page->output();	
	}else{
		$page = new template($template_path ."/pm-read.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"LANG-READPM" => "$pm[readpm]",
		"LANG-DELPROMPT" => "$pm[confirmdelete]",
		"LANG-MOVEPROMPT" => "$pm[moveconfirm]",
		"LANG-REPLY" => "$pm[replyalt]",
		"ID" => "$pm_r[id]",
		"LANG-FROM" => "$pm[from]",
		"FROM" => "$pm_r[Sender]",
		"LANG-BANUSER" => "$pm[banuser]",
		"LANG-TO" => "$pm[to]",
		"TO" => "$pm_r[Reciever]",
		"LANG-DATE" => "$pm[date]",
		"DATE" => "$readpm_date",
		"LANG-SUBJECT" => "$pm[subject]",
		"SUBJECT" => "$pm_r[Subject]",
		"PM-MESSAGE" => "$string",
		"SIGNATURE" => "$sig",
		"LANG-DELETEPM" => "$pm[delpm]",
		"LANG-MOVEPM" => "$pm[movemsg]"));
		$page->output();
	}
break;
case 'reply':
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nopmid'];
		echo error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']); 
	}  
	$db->run = "Select Sender, Subject From ebb_pm WHERE id='$id'";
	$reply = $db->result();
	$db->close();
	
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	$page = new template($template_path ."/pm-replypm.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[pm]",
	"LANG-REPLYPM" => "$pm[replypm]",
	"LANG-NOSENDER" => "$pm[nosend]",
	"LANG-NOBODY" => "$pm[nomessage]",
	"LANG-LONGSENDER" => "$pm[longuser]",
	"BBCODE" => "$bbcode",
	"LANG-SMILES" => "$post[moresmiles]",
	"SMILES" => "$smile",
	"LANG-USERNAME" => "$txt[username]",
	"USERNAME" => "$logged_user",
	"LANG-TO" => "$pm[send]",
	"TO" => "$reply[Sender]",
	"LANG-SUBJECT" => "$pm[subject]",
	"SUBJECT" => "$reply[Subject]",
	"LANG-SENDPM" => "$pm[reply]"));
	$page->output();
break;
case 'reply_process':
	//get the value from the form.
	$reply_send = var_cleanup($_POST['send']);
	$reply_message = var_cleanup($_POST['message']);
	$re_subject = var_cleanup($_POST['subject']);

	#SQL to determine if user entered is valid.
	$db->run = "select Username from ebb_users WHERE Username='$reply_send' LIMIT 1";
	$usr_chk = $db->num_results();
	$db->close();

	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error-check.
	if (empty($reply_send)){
		$errormsg = $pm['nosend']."\n\n";
		$error = 1;
	}
	if($usr_chk == 0){
	    $errormsg .= $login['invaliduser']."\n\n";
	    $error = 1;
	}
	if (empty($reply_message)){
		$errormsg .= $pm['nomessage']."\n\n";
		$error = 1;
	}
	if(strlen($reply_send) > 25){
		$errormsg .= $pm['longuser']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//check settings.
		$values = 'Status';
		#call user function.
		$userpref = user_settings($reply_send, $values);
		if($userpref['Status'] == "groupmember"){
			$db->run = "SELECT gid FROM ebb_group_users where Username='$reply_send'";
			$groupuser = $db->result();
			$group_auth_chk = $db->num_results();
			$db->close();
			if ($group_auth_chk == 1){
				$db->run = "SELECT permission_type FROM ebb_groups where id='$groupuser[gid]'";
				$level_result = $db->result();
				$db->close();
				#set-up var
				$send_permission_type = $level_result['permission_type'];
			}else{
				die('INVALID GROUP ID');
			}
		}else{
			$send_permission_type = 4;
		}
		$permission_chk_pm = access_vaildator($send_permission_type, 27);
		if($permission_chk_pm == 0){
			$error = $pm['pm_access_user'];
			echo error($error, "error");
		}
		//check to see if the from user's inbox is full.
		$db->run = "SELECT id FROM ebb_pm WHERE Reciever='$reply_send' and Folder='Inbox'";
		$check_inbox = $db->num_results();
		$db->close();
		if ($check_inbox == $settings['PM_Quota']){
			$error = $pm['overquota'];
			echo error($error, "error");
		}
		//check to see if this user is on the ban list.
		$db->run = "SELECT id FROM ebb_pm_banlist WHERE Banned_User='$logged_user' and Ban_Creator='$reply_send'";
		$check_ban_r = $db->num_results();
		$db->close();
		if ($check_ban_r == 1){
			$error = $pm['blocked'];
			echo error($error, "general");
		}	
		//process query
		$time = time();
		$db->run = "insert into ebb_pm (Sender, Reciever, Subject, Folder, Message, Date) values ('$logged_user', '$reply_send', '$re_subject', 'Inbox', '$reply_message', '$time')";
		$db->query();
		$db->close();
		//email user if they requested it.
		$db->run = "SELECT PM_Notify, Email FROM ebb_users WHERE Username='$reply_send'";
		$notify = $db->result();
		$db->close();
		if ($notify['PM_Notify'] == 1){
			//get pm id.
			$db->run = "SELECT id FROM ebb_pm ORDER BY id DESC limit 1";
			$pm_id_result = $db->result();
			$db->close();
			//grab values from PM message.
			$db->run = "select Reciever, Sender, Subject, id from ebb_pm where id='$pm_id_result[id]'";
			$pm_data = $db->result();
			$db->close();
			//pull-up language mail file.
			require "lang/".$lang.".email.php";
			$pm_message = pm_notify();
			//get ready to send mail.
			if($settings['mail_type'] == 0){
				#call smtp class file.
				require "includes/smtp.php";
				//call up the smtp class.
				$mailer = new ebbmail;
				$mailer->ebbmail();
				//setup the subject of this newsletter.
				$mailer->Subject = $pm['pmsubject'];
				//get body of newsletter.
				$mailer->Body = $pm_message;
				//add users to the email list
				$mailer->AddBCC($notify['Email']);
				//send out the email.
				$mailer->Send();
				//clear the list to prevent any double emails.
				$mailer->ClearAllRecipients();
			}else{
				//create a From: mailheader
				$headers = "From: $title <$board_email>";
				@mail($notify['Email'], $pm['pmsubject'], $pm_message, $headers);
			}
		}
		//bring user back
		header("Location: PM.php");
	}
break;
case 'movemsg':
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nopmid'];
		echo error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']); 
	}
	$db->run = "select Reciever from ebb_pm WHERE id='$id'";
	$pm_r = $db->result();
	$db->close();
	//see if pm message belong to the right user.
	if ($pm_r['Reciever'] !== $logged_user){
		$error = $pm['accessdenied'];
		echo error($error, "error"); 
	}
	#see if user has enough space to save message.
	$db->run = "SELECT id FROM ebb_pm WHERE Reciever='$logged_user' and Folder='Archive'";
	$check_archive = $db->num_results();
	$db->close();
	if ($check_archive == $settings['Archive_Quota']){
		$error = $pm['overquota'];
		echo error($error, "error");	
	}else{
		//process query
		$db->run = "UPDATE ebb_pm SET Folder='Archive' Where id='$id'";
		$db->query();
		$db->close();
		//bring user back
		header("Location: PM.php");
	}
break;
case 'delete':
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nopmid'];
		echo error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']); 
	}
	$db->run = "select Reciever from ebb_pm WHERE id='$id'";
	$pm_r = $db->result();
	$db->close();
	//see if pm message belong to the right user.
	if ($pm_r['Reciever'] !== $logged_user){
		$error = $pm['accessdenied'];
		echo error($error, "error"); 
	}
	//process query
	$db->run = "DELETE FROM ebb_pm Where id='$id'";
	$db->query();
	$db->close();
	//bring user back
	header("Location: PM.php");
break;
case 'ban':
	if(!isset($_GET['ban_user'])){
		$ban_user = ''; 
	}else{
		$ban_user = var_cleanup($_GET['ban_user']);
	}
	$page = new template($template_path ."/pm-banuser.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[pm]",
	"LANG-BANUSER" => "$pm[banusertitle]",
	"LANG-NOUSERNAME" => "$pm[blankfield]",
	"LANG-LONGUSERNAME" => "$pm[longbanuser]",
	"TEXT" => "$pm[text]",
	"LANG-USERNAME" => "$txt[username]",
	"USERNAME" => "$logged_user",
	"LANG-BAN" => "$pm[usertoban]",
	"BAN" => "$ban_user",
	"LANG-SUBMIT" => "$pm[banuser]"));
	$page->output();
  break;
  case 'ban_process':
	$banned_user = var_cleanup($_POST['banned_user']);

	#SQL to determine if user entered is valid.
	$db->run = "select Username from ebb_users WHERE Username='$banned_user' LIMIT 1";
	$usr_chk = $db->num_results();
	$db->close();

	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if ($banned_user == ""){
		$errormsg = $pm['blankfield']."\n\n";
		$error = 1;
	}
	if($usr_chk == 0){
	    $errormsg .= $login['invaliduser']."\n\n";
	    $error = 1;
	}
	if(strlen($banned_user) > 25){
		$errormsg .= $pm['longbanuser']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_pm_banlist (Banned_User, Ban_Creator) values('$banned_user', '$logged_user')";
		$db->query();
		$db->close();
		//bring user back
		header("Location: PM.php?action=banlist");
	}
break;
case 'banlist':
	$banlist = view_banlist();
break;
case 'del_ban':
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nopmid'];
		echo error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']); 
	}
	$db->run = "select Ban_Creator from ebb_pm_banlist WHERE id='$id'";
	$ban_r = $db->result();
	$db->close();
	//see if user banned the user they wish to delete.
	if ($ban_r['Ban_Creator'] !== $logged_user){
		$error = $pm['accessdenied'];
		echo error($error, "error"); 
	}
	//process query
	$db->run = "DELETE FROM ebb_pm_banlist WHERE id='$id'";
	$db->query();
	$db->close();
	//bring user back
	header("Location: PM.php?action=banlist");
  break;
  default:
  	#get current folder location.
	if(!empty($_GET['folder'])){
		$pmFolder = var_cleanup($_GET['folder']);
	}else{
		$pmFolder = "Inbox";
	}
	#see if folder name are valid.
	if (($pmFolder == "Inbox") or ($pmFolder == "Outbox") or ($pmFolder == "Archive")){
		
		//pagination
		$count = 0;
		$count2 = 0;
		if(!isset($_GET['pg'])){
		    $pg = 1;
		}else{
		    $pg = var_cleanup($_GET['pg']);
		}
		// Figure out the limit for the query based on the current page number.
		$from = (($pg * $settings['per_page']) - $settings['per_page']);
		// Figure out the total number of results in DB:
		if($pmFolder == "Outbox"){
			$db->run = "select id, Subject, Sender, Date, Read_Status from ebb_pm WHERE Sender='$logged_user' and Read_Status='' ORDER BY Date DESC LIMIT $from, $settings[per_page]";
			$query = $db->query();
			$db->close();
			$db->run = "select id from ebb_pm WHERE Sender='$logged_user' and Read_Status=''";
			$num = $db->num_results();
			$db->close();
		}else{
			$db->run = "select id, Subject, Sender, Date, Read_Status from ebb_pm WHERE Reciever='$logged_user' and Folder='$pmFolder' ORDER BY Date DESC LIMIT $from, $settings[per_page]";
			$query = $db->query();
			$db->close();
			$db->run = "select id from ebb_pm WHERE Reciever='$logged_user' and Folder='$pmFolder'";
			$num = $db->num_results();
			$db->close();
		}
		#output pagination.
		$pagenation = pagination('');
		//output im inbox.
		$inbox = pm_inbox($pmFolder);
	}else{
 		$error = $pm['invalidfolder'];
		echo error($error, "error");
	}
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
