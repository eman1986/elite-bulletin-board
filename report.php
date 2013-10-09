<?php
define('IN_EBB', true);
/*
Filename: report.php
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

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$report[title]",
  "LANG-HELP-TITLE" => "$help[reporttitle]",
  "LANG-HELP-BODY" => "$help[reportbody]"));

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
	#guest should not view this page.
	header("Location: login.php");
}
#call board setting function.
$colume = 'mail_type';
$settings = board_settings($colume);
//display search
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
switch($mode){
case 'topic':
	#see if Topic ID was declared, if not terminate any further outputting.
	if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
		die($txt['notid']);
	}else{
		$tid = var_cleanup($_GET['tid']); 
	}
	//check to see if topic exists or not and if it doesn't kill the program
	$db->run = "select tid FROM ebb_topics WHERE tid='$tid'";
	$checktopic = $db->num_results();
	$db->close();
	if ($checktopic == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	$page = new template($template_path ."/report-topic.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$report[title]",
	"LANG-NOMESSAGE" => "$report[nomsg]",
	"LANG-TEXT" => "$report[text]",
	"TID" => "$tid",
	"LANG-REPORTEDBY" => "$report[Reportedby]",
	"USERNAME" => "$logged_user",
	"LANG-REASON" => "$report[reason]",
	"LANG-SPAMPOST" => "$report[spampost]",
	"LANG-FIGHTPOST" => "$report[fightpost]",
	"LANG-ADVERT" => "$report[advert]",
	"LANG-USERPROBLEMS" => "$report[userproblems]",
	"LANG-OTHER" => "$report[other]",
	"LANG-MESSAGE" => "$report[message]",
	"LANG-SUBMIT" => "$report[submit]"));
	$page->output();
break;
case 'report_topic':
	#see if Topic ID was declared, if not terminate any further outputting.
	if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
		die($txt['notid']);
	}else{
		$tid = var_cleanup($_GET['tid']); 
	}
	$reason = var_cleanup($_POST['reason']);
	$msg = var_cleanup($_POST['msg']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#load mail language file.
	require "lang/".$lang.".email.php";
	#error check.
	if(empty($reason)){
		$errormsg = $report['noreason']."\n\n";
		$error = 1;
	}
	if(empty($msg)){
		$errormsg .= $report['nomsg']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		#get info on moderators to send out email.
		$db->run = "select bid FROM ebb_topics WHERE tid='$tid'";
		$t = $db->result();
		$db->close();
		$db->run = "select group_id FROM ebb_grouplist WHERE board_id='$t[bid]'";
		$b = $db->query();
		$db->close();
		#find moderators for the gorpu controling this board..
		while($r = mysql_fetch_assoc($b)){
			$db->run = "select Username FROM ebb_group_users WHERE gid='$r[group_id]'";
			$g = $db->query();
			$db->close();
			#get user's profile
			while($r2 = mysql_fetch_assoc($g)){
				$db->run = "select Email FROM ebb_users WHERE Username='$r2[Username]'";
				$u = $db->result();
				$db->close();
				#mail moderators.
				$report_subject = $report['reportsubject'];
				$report_topic_msg = report_topic();
				if($settings['mail_type'] == 0){
					#call smtp class file.
					require "includes/smtp.php";
					//call up the smtp class.
					$mailer = new ebbmail;
					$mailer->ebbmail();
					//setup the subject of this newsletter.
					$mailer->Subject = $report_subject;
					//get body of newsletter.
					$mailer->Body = $report_topic_msg;
					//add users to the email list
					$mailer->AddBCC($u['Email']);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					set_time_limit(2500); //set the time higher if you need to.
					@mail($u['Email'], $report_subject, $report_topic_msg, $headers);
				}
			}
		}
		#display thank you message.
		$error = $report['reportsent'];
		echo error($error, "general");
	}
break;
case 'post':
	if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
		die($txt['nopid']);
	}else{
		$pid = var_cleanup($_GET['pid']);
	}
	//check to see if topic exists or not and if it doesn't kill the program
	$db->run = "select pid FROM ebb_posts WHERE pid='$pid'";
	$checktopic = $db->num_results();
	$db->close();
	if ($checktopic == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	$page = new template($template_path ."/report-post.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$report[title]",
	"LANG-NOMESSAGE" => "$report[nomsg]",
	"LANG-TEXT" => "$report[text]",
	"PID" => "$pid",
	"LANG-REPORTEDBY" => "$report[Reportedby]",
	"USERNAME" => "$logged_user",
	"LANG-REASON" => "$report[reason]",
	"LANG-SPAMPOST" => "$report[spampost]",
	"LANG-FIGHTPOST" => "$report[fightpost]",
	"LANG-ADVERT" => "$report[advert]",
	"LANG-USERPROBLEMS" => "$report[userproblems]",
	"LANG-OTHER" => "$report[other]",
	"LANG-MESSAGE" => "$report[message]",
	"LANG-SUBMIT" => "$report[submit]"));
	$page->output();
break;
case 'report_post':
	if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
		die($txt['nopid']);
	}else{
		$pid = var_cleanup($_GET['pid']);
	}
	$reason = var_cleanup($_POST['reason']);
	$msg = var_cleanup($_POST['msg']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#load mail language file.
	require "lang/".$lang.".email.php";
	#error check.
	if(empty($reason)){
		$errormsg = $report['noreason']."\n\n";
		$error = 1;
	}
	if(empty($msg)){
		$errormsg .= $report['nomsg']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		#get info on moderators to send out email.
		$db->run = "select tid FROM ebb_posts WHERE pid='$pid'";
		$p = $db->result();
		$db->close();
		$db->run = "select bid FROM ebb_topics WHERE tid='$p[tid]'";
		$t = $db->result();
		$db->close();
		$db->run = "select group_id FROM ebb_grouplist WHERE board_id='$t[bid]'";
		$b = $db->query();
		$db->close();
		#find moderators for the gorpu controling this board..
		while($r = mysql_fetch_assoc($b)){
			$db->run = "select Username FROM ebb_group_users WHERE gid='$r[group_id]'";
			$g = $db->query();
			$db->close();
			#get user's profile
			while($r2 = mysql_fetch_assoc($g)){
				$db->run = "select Email FROM ebb_users WHERE Username='$r2[Username]'";
				$u = $db->result();
				$db->close();
				#mail moderators.
				$report_subject = $report['reportsubject'];
				$report_post_msg = report_post();
				if($settings['mail_type'] == 0){
					#call smtp class file.
					require "includes/smtp.php";
					//call up the smtp class.
					$mailer = new ebbmail;
					$mailer->ebbmail();
					//setup the subject of this newsletter.
					$mailer->Subject = $report_subject;
					//get body of newsletter.
					$mailer->Body = $report_post_msg;
					//add users to the email list
					$mailer->AddBCC($u['Email']);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					set_time_limit(2500); //set the time higher if you need to.
					@mail($u['Email'], $report_subject, $report_post_msg, $headers);
				}
			}
		}
		#display thank you message.
		$error = $report['reportsent'];
		echo error($error, "general");
	}
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
