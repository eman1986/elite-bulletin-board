<?php
define('IN_EBB', true);
/*
Filename: manage.php
Last Modified: 6/7/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
require "phpmailer/class.phpmailer.php";

if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
#get page title.
switch($mode){
case 'viewip':
	#See if user can access this portion of the page.
	$permission_chk_vip = access_vaildator($permission_type, 24);
	if($permission_chk_vip == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'].' - '.$mod['ipinfo'];
	$helpTitle = $help['ipinfotitle'];
	$helpBody = $help['ipinfobody'];
break;
case 'dnslookup':
	#See if user can access this portion of the page.
	$permission_chk_vip = access_vaildator($permission_type, 24);
	if($permission_chk_vip == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'].' - '.$mod['getdns'];
	$helpTitle = $help['dnslookuptitle'];
	$helpBody = $help['dnslookupbody'];
break;
case 'warn':
case 'warn_process':
	#See if user can access this portion of the page.
	$permission_chk_warn = access_vaildator($permission_type, 25);
	if($permission_chk_warn == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'].' - '.$mod['warnuser'];
	$helpTitle = $help['warnusertitle'];
	$helpBody = $help['warnuserbody'];
break;
case 'move':
case 'move_process':
	#See if user can access this portion of the page.
	$permission_chk_move = access_vaildator($permission_type, 23);
	if($permission_chk_move == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'].' - '.$mod['movetopic'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'delete':
case 'delete_process':
	#See if user can access this portion of the page.
	$permission_chk_del = access_vaildator($permission_type, 21);
	if($permission_chk_del == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'].' - '.$delete['title'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'lock':
case 'unlock':
	#See if user can access this portion of the page.
	$permission_chk_lock = access_vaildator($permission_type, 22);
	if($permission_chk_lock == 0){
		die($txt['accessdenied']);
	}
	$modtitle = $mod['title'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
default:
	$modtitle = $mod['title'];
}
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$modtitle",
  "LANG-HELP-TITLE" => "$helpTitle",
  "LANG-HELP-BODY" => "$helpBody"));

$page->output();
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
if ($access_level == 2){
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
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	$error = $txt['nobid'];
	echo error($error, "error");
}else{
	$bid = var_cleanup($_GET['bid']); 
}
#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	$error = $txt['notid'];
	echo error($error, "error");
}else{
	$tid = var_cleanup($_GET['tid']); 
}
if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
	$pid = '';
}else{
	$pid = var_cleanup($_GET['pid']);
}
//check to see if this user is able to access this area.
$checkmod = group_validate($bid, $level_result['id'], 1);
//if user has no right to be here, send them to the index page.
if (($stat == "Member") OR ($access_level == 3) OR ($stat == "guest") or ($checkmod == 0)){
	header("Location: index.php");
}
switch ($mode){
case 'viewip':
	#see if the user supplied an IP Address.
	if((!isset($_GET['ip'])) or (empty($_GET['ip']))){
		$error = $mod['noip'];
		echo error($error, "error");
	}else{
		$ip = var_cleanup($_GET['ip']);
	}
	#see if a user added the user's name.
	if((!isset($_GET['u'])) or (empty($_GET['u']))){
		$error = $login['nouser'];
		echo error($error, "error");
	}else{
		$u = var_cleanup($_GET['u']);
	}
	//get number of users this ip matches.
	$iplist = ip_checker();
	//get other ips the poster used before.
	$ipcheck = other_ip_check();
	//output html.
	$page = new template($template_path ."/mod-viewip.htm");
	$page->replace_tags(array(
	"BID" => "$bid",
	"TID" => "$tid",
	"TITLE" => "$title",
	"LANG-TITLE" => "$mod[title]",
	"LANG-IPINFO" => "$mod[ipinfo]",
	"LANG-IP" => "$mod[topicip]",
	"IP" => "$ip",
	"LANG-DNSLOOKUP" => "$mod[getdns]",
	"LANG-USERNAME" => "$mod[ipusermatch]",
	"USERNAME" => "$iplist",
	"LANG-TOTALCOUNT" => "$mod[totalcount]",
	"TOTALCOUNT" => "$ipcheck"));
	$page->output();
break;
case 'dnslookup':
	#see if the user supplied an IP Address.
	if((!isset($_GET['ip'])) or (empty($_GET['ip']))){
		$error = $mod['noip'];
		echo error($error, "error");
	}else{
		$ip = var_cleanup($_GET['ip']);
	}
	#get DNS info.
	$dnslookup = gethostbyaddr($ip);	
	//output html.
	$page = new template($template_path ."/mod-dnslookup.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$mod[title]",
	"LANG-DNSLOOKUP" => "$mod[getdns]",
	"DNSLOOKUP" => "$dnslookup"));
	$page->output();
break;
case 'warn':
	#see if username was declared, if not terminate any further outputting.
	if((!isset($_GET['user'])) or (empty($_GET['user']))){
		$error = $cp['nousernameentered'];
		echo error($error, "error");
	}else{
		$user = var_cleanup($_GET['user']); 
	}
	#see if username exist on db.
	$db->run = "Select Status, username, suspend_length FROM ebb_users WHERE Username='$user'";
	$user_chk = $db->num_results();
	$user_r = $db->result();
	$db->close();
	if($user_chk == 0){
		$error = $userinfo['usernotexist'];
		echo error($error, "error");	
	}else{
		#see if user is an administrator and the user setting the ban is a lower in rank.
		if ($user_r['Status'] == "groupmember"){
			$db->run = "SELECT gid FROM ebb_group_users where Username='$user'";
			$groupuser = $db->result();
			$group_auth_chk = $db->num_results();
			$db->close();
			if ($group_auth_chk == 1){
				$db->run = "SELECT Level FROM ebb_groups where id='$groupuser[gid]'";
				$level_result = $db->result();
				$db->close();
			}
			#see if user has power to warn the user.
			if(($level_result['Level'] == 1) and ($access_level == 2)){
				$error = $mod['nocontrol'];
				echo error($error, "error");
			}
		}	
		//warning form.
		$page = new template($template_path ."/mod-warnuser.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$mod[title]",
		"LANG-NOWARN" => "$mod[nowarn]",
		"LANG-NOREASON" => "$mod[noreason]",
		"LANG-LONGREASON" => "$mod[longreason]",
		"LANG-NOCONTACTERR" => "$mod[nocontacterr]",
		"LANG-TEXT" => "$mod[warntxt]",
		"LANG-WARNOPTION" => "$mod[warnopt]",
		"LANG-RAISEWARN" => "$mod[raisewarn]",
		"LANG-LOWERWARN" => "$mod[lowerwarn]",
		"LANG-WARNREASON" => "$mod[warnreason]",
		"LANG-SUSPENDIONLENGTH" => "$mod[suspensionlength]",
		"LANG-SUSPENDHINT" => "$mod[suspendhint]",
		"SUSPENDIONLENGTH" => "$user_r[suspend_length]",		
		"LANG-CONTACTOPTION" => "$mod[contactopt]",
		"LANG-NOCONTACT" => "$mod[nocontact]",
		"LANG-PMCONTACT" => "$mod[pmcontact]",
		"LANG-EMAILCONTACT" => "$form[email]",
		"LANG-CONTACT-TEXT" => "$mod[contacttxt]",
		"LANG-SUBMIT" => "$mod[warnuser]",
		"BID" => "$bid",
		"TID" => "$tid",
		"USER" => "$user"));
		$page->output();
  	}
break;
case 'warn_process':
	#call board setting function.
	$colume = 'warning_threshold, mail_type';
	$settings = board_settings($colume);
	#Form values.
	$warnopt = var_cleanup($_POST['warnopt']);
	$reason = var_cleanup($_POST['reason']);
	$suspend = var_cleanup($_POST['suspend']);
	$contactopt = var_cleanup($_POST['contactopt']);
	$body = var_cleanup($_POST['body']);
	$user = var_cleanup($_POST['user']);
	#time variable for suspension.
	$time = time();
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if(empty($user)){
		$errormsg = $login['nouser']."\n\n";
		$error = 1;
	}
	#get user's current warning level.
	$db->run = "SELECT Status, warning_level, Email, Language FROM ebb_users WHERE Username='$user'";
	$warn_r = $db->result();
	$db->close();
	#see if user is an administrator and the user setting the ban is a lower in rank.
	if ($warn_r['Status'] == "groupmember"){
		$db->run = "SELECT gid FROM ebb_group_users where Username='$user'";
		$groupuser = $db->result();
		$group_auth_chk = $db->num_results();
		$db->close();
		if ($group_auth_chk == 1){
			$db->run = "SELECT Level FROM ebb_groups where id='$groupuser[gid]'";
			$level_result = $db->result();
			$db->close();
		}
		#see if user has power to warn the user.
		if(($level_result['Level'] == 1) and ($access_level == 2)){
			$error = $mod['nocontrol'];
			echo error($error, "error");
		}
	}
	#see if warning level is already at the threshold point.
	if($warn_r['warning_level'] == $settings['warning_threshold']){
		$errormsg .= $mod['alreadybanned']."\n\n";
		$error = 1;
	}
	if($warnopt == ""){
		$errormsg .= $mod['nowarn']."\n\n";
		$error = 1;
	}
	if(empty($reason)){
		$errormsg .= $mod['noreason']."\n\n";
		$error = 1;
	}
	if(strlen($reason) > 255){
		$errormsg .= $mod['longreason']."\n\n";
		$error = 1;
	}
	if(($contactopt != "None") and ($body == "")){
		$errormsg .= $mod['nocontacterr']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		#add reason of warning set to db.
		if($warnopt == 10){
			$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '1', '$reason')";
			$db->query();
			$db->close();		
		}else{
			$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '2', '$reason')";
			$db->query();
			$db->close();
		}
		#update user's warning level based on form result.
		$warn_adjust = $warn_r['warning_level'] + $warnopt;
		if($warn_adjust == $settings['warning_threshold']){
			if($warn_r['Status'] == "groupmember"){
				#delete user from any special groups they belong to.
				$db->run = "delete from ebb_group_users where Username='$user'";
				$db->query();
				$db->close();				
			}
			#set user as banned.
			$db->run = "update ebb_users set Status='Banned', warning_level='$warn_adjust' where Username='$user'";
			$db->query();
			$db->close();
			#log this action to warning log.
			$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '3', '$reason')";
			$db->query();
			$db->close();
		}else{
			$db->run = "update ebb_users set warning_level='$warn_adjust' where Username='$user'";
			$db->query();
			$db->close();		
		}
		#see if mod requested to contact user.
		if(($contactopt == "PM") or ($contactopt == "Email")){
			#get subject.
			$contact_subject = $mod['contactsubject']. $title;
			if($contactopt == "PM"){
				//create PM Message.
				$db->run = "insert into ebb_pm (Sender, Reciever, Subject, Message, Date) values ('$title', '$user', '$contact_subject', '$body', '$time')";
				$db->query();
				$db->close();
				#email user to alert them of the pm message.
				//get pm id.
				$db->run = "SELECT id FROM ebb_pm ORDER BY id DESC limit 1";
				$pm_id_result = $db->result();
				$db->close();
				//grab values from PM message.
				$db->run = "select Reciever, Sender, Subject, id from ebb_pm where id='$pm_id_result[id]'";
				$pm_data = $db->result();
				$db->close();
				//pull-up language mail file.
				require "lang/".$warn_r['Language'].".email.php";
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
					$mailer->AddBCC($warn_r['Email']);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					@mail($warn_r['Email'], $pm['pmsubject'], $pm_message, $headers);
				}
				#see if user has to be suspended.
				if($suspend > 0){
					$db->run = "update ebb_users set suspend_length='$suspend', suspend_time='$time' where Username='$user'";
					$db->query();
					$db->close();
					#log this action to warning log.
					$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '4', '$reason')";
					$db->query();
					$db->close();
				}
				#redirect back to topic.
				header("Location: viewtopic.php?bid=$bid&tid=$tid");
			}
			if($contactopt == "Email"){
				#send out an email.
				if($settings['mail_type'] == 0){
					#call smtp class file.
					require "includes/smtp.php";
					//call up the smtp class.
					$mailer = new ebbmail;
					$mailer->ebbmail();
					//setup the subject of this newsletter.
					$mailer->Subject = $contact_subject;
					//get body of newsletter.
					$mailer->Body = $body;
					//add users to the email list
					$mailer->AddBCC($warn_r['Email']);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					@mail($warn_r['Email'], $contact_subject, $body, $headers);
				}
				#see if user has to be suspended.
				if($suspend > 0){
					$db->run = "update ebb_users set suspend_length='$suspend', suspend_time='$time' where Username='$user'";
					$db->query();
					$db->close();				
					#log this action to warning log.
					$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '4', '$reason')";
					$db->query();
					$db->close();
				}
				#redirect back to topic.
				header("Location: viewtopic.php?bid=$bid&tid=$tid");
			}
		}else{
			#see if user has to be suspended.
			if($suspend > 0){
				$db->run = "update ebb_users set suspend_length='$suspend', suspend_time='$time' where Username='$user'";
				$db->query();
				$db->close();				
				#log this action to warning log.
				$db->run = "insert into ebb_warnlog (Username, Authorized, Action, Message) values('$user', '$logged_user', '4', '$reason')";
				$db->query();
				$db->close();
			}
			#redirect back to topic.
			header("Location: viewtopic.php?bid=$bid&tid=$tid");
		}
	}
break;
case 'move':
	$boardlist = board_select();
	$page = new template($template_path ."/mod-movetopic.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$mod[title]",
	"LANG-NOBOARD" => "$mod[noboard]",
	"LANG-TEXT" => "$mod[move]",
	"BID" => "$bid",
	"TID" => "$tid",
	"BOARDLIST" => "$boardlist",
	"LANG-SUBMIT" => "$mod[movetopic]"));
	$page->output();
break;
case 'move_process':
	//process query
	$board = var_cleanup($_POST['board']);
	$tid = var_cleanup($_GET['tid']);
	#error check.
	if(empty($board)){
		$error = $mod['noboard'];
		echo error($error, "validate");
	}
	if(empty($tid)){
		$error =$txt['notid']; 
		echo error($error, "validate");
	}
	#change board info on tables.
	$db->run = "Select bid, tid FROM ebb_topics WHERE tid='$tid'";
	$board_chk = $db->result();
	$chpost_q = $db->query();
	$db->close();
	#see if user chose same board as the current topic location.
	if($board_chk['bid'] == $board){
		$error = $mod['sameboard']; 
		echo error($error, "validate");
	}
	#moe over topics & posts to new location.
	while($r = mysql_fetch_assoc($chpost_q)){
		$db->run = "Update ebb_posts SET bid='$board' WHERE tid='$r[tid]'";
		$db->query();
		$db->close();
	}
	$db->run = "Update ebb_topics SET bid='$board' WHERE tid='$tid'";
	$db->query();
	$db->close();
	//update last posted section of the old topic location.
	$db->run = "SELECT id FROM ebb_boards WHERE id='$bid'";
	$board_num = $db->num_results();
	$db->close();
	if($board_num == 0){
		$db->run = "UPDATE ebb_boards SET last_update='' WHERE tid='$tid'";
		$db->query();
		$db->close();
	}else{
		$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE bid='$bid' ORDER BY last_update DESC LIMIT 1";
		$board_r = $db->result();
		$db->close();
		//update the last_update colume for ebb_boards.
		$db->run = "UPDATE ebb_boards SET last_update='$board_r[last_update]', Posted_User='$board_r[Posted_User]', Post_Link='$board_r[Post_Link]'  WHERE id='$bid'";
		$db->query();
		$db->close();
	}
	//update board of new location, if the topic is newer.
	$db->run = "SELECT last_update FROM ebb_boards WHERE id='$board'";
	$board_chk = $db->result();
	$db->close();
	$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE tid='$tid'";
	$topic_chk = $db->result();
	$db->close();
	if($board_chk['last_update'] < $topic_chk['last_update']){
		//update the last_update colume for ebb_boards.
		$db->run = "UPDATE ebb_boards SET last_update='$topic_chk[last_update]', Posted_User='$topic_chk[Posted_User]', Post_Link='$topic_chk[Post_Link]'  WHERE id='$board'";
		$db->query();
		$db->close();
	}
	//bring user back
	header("Location: viewtopic.php?bid=$board&tid=$tid");
break;
case 'delete':
	$page = new template($template_path ."/mod-delete.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$mod[title]",
	"LANG-DELSURE" => "$mod[condel]",
	"TID" => "$tid",
	"BID" => "$bid",
	"LANG-YES" => "$txt[yes]",
	"LANG-NO" => "$txt[no]"));
	$page->output();
break;
case 'delete_process':
	//delete polls made by topics in this board.
	$db->run = "DELETE FROM ebb_poll WHERE tid='$tid'";
	$db->query();
	$db->close();
	#delete any votes.
	$db->run = "DELETE FROM ebb_votes WHERE tid='$tid'";
	$db->query();
	$db->close();
	//delete read status from topics made in this board.
	$db->run = "DELETE FROM ebb_read_topic WHERE Topic='$tid'";
	$db->query();
	$db->close();
	#delete any attachments thats tied to a topic under this board.
	$db->run = "select Filename from ebb_attachments where tid='$tid'";
	$attach_r = $db->result();
	$attach_chk = $db->num_results();
	$db->close();
	if($attach_chk == 1){
		#delete file from web space.
		$delattach = unlink ('../uploads/'. $attach_r['Filename']);
		#delete entry from db.
		$db->run = "DELETE FROM ebb_attachments WHERE tid='$tid'";
		$db->query();
		$db->close();
	}
	//delete topic.
	$db->run = "DELETE FROM ebb_topics WHERE tid='$tid'";
	$db->query();
	$db->close();
	#get post detials
	$db->run = "select pid from ebb_posts WHERE tid='$tid'";
	$pid_q = $db->query();
	$db->close();
	while($r = mysql_fetch_assoc($pid_q)){
		#delete any attachments thats tied to a post under this board.
		$db->run = "select Filename from ebb_attachments where pid='$r[pid]'";
		$attach_r2 = $db->result();
		$attach_chk2 = $db->num_results();
		$db->close();
		if($attach_chk2 == 1){
			#delete file from web space.
			$delattach = unlink ('../uploads/'. $attach_r2['Filename']);
			#delete entry from db.
			$db->run = "DELETE FROM ebb_attachments WHERE tid='$r[pid]'";
			$db->query();
			$db->close();
		} 
	}
	//delete replies, if any.
	$db->run = "DELETE FROM ebb_posts WHERE tid='$tid'";
	$db->query();
	$db->close();
	//delete any subscriptions to this topic.
	$db->run = "DELETE FROM ebb_topic_watch WHERE tid='$tid'";
	$db->query();
	$db->close();
	//update last posted section.
	$db->run = "SELECT id FROM ebb_boards WHERE id='$bid'";
	$board_num = $db->num_results();
	$db->close();
	if($board_num == 0){
		$db->run = "UPDATE ebb_boards SET last_update='' WHERE tid='$tid'";
		$db->query();
		$db->close();
	}else{
		$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE bid='$bid' ORDER BY last_update DESC LIMIT 1";
		$board_r = $db->num_results();
		$db->close();
		//update the last_update colume for ebb_boards.
		$db->run = "UPDATE ebb_boards SET last_update='$board_r[last_update]', Posted_User='$board_r[Posted_User]', Post_Link='$board_r[Post_Link]'  WHERE id='$bid'";
		$db->query();
		$db->close();
	}
	//bring user back
	header("Location: viewboard.php?bid=$bid");
break;
case 'lock':
	//process query
	$db->run = "Update ebb_topics SET Locked='1' WHERE tid='$tid'";
	$db->query();
	$db->close();
	//bring user back
	header("Location: viewtopic.php?bid=$bid&tid=$tid");
break;
case 'unlock':
	//process query
	$db->run = "Update ebb_topics SET Locked='0' WHERE tid='$tid'";
	$db->query();
	$db->close();
	//bring user back
	header("Location: viewtopic.php?bid=$bid&tid=$tid");
break;
default:
	header("Location: index.php");
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
