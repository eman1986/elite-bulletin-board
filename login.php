<?php
define('IN_EBB', true);
/*
Filename: login.php
Last Modified: 2/10/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
require "phpmailer/class.phpmailer.php";

//display login system.
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
#get page title
switch($mode){
case 'verify_acct':
	$logintitle = $login['activationtitle'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'lostpassword':
case 'process_lostpassword':
	$logintitle = $login['passwordrecovery'];
	$helpTitle = $help['lostpwdtitle'];
	$helpBody = $help['lostpwdbody'];
break;
default:
	$logintitle = $login['login'];
	$helpTitle = $help['logintitle'];
	$helpBody = $help['loginbody'];
}
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$logintitle",
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
	}
	$error .= '<p class="td"><b>'.$login['offlinemsg']. '</b></p>';
	echo error($error, "general");
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
}
switch ($mode){
case 'verify_acct':
	#see if activation key is missing.
	if((!isset($_GET['key'])) or (empty($_GET['key']))){
		$error = $login['noacctkey'];
		echo error($error, "error");
	}else{
		$key = var_cleanup($_GET['key']);
	}
	#see if username is missing.
	if((!isset($_GET['u'])) or (empty($_GET['u']))){
		$error = $login['nouser'];
		echo error($error, "error");
	}else{
		$u = var_cleanup($_GET['u']);
	}
	//check for correct key code & username.
	$db->run = "select id from ebb_users where Username='$u' AND act_key='$key'";
	$acct_chk = $db->num_results();
	$db->close();
	if($acct_chk == 1){
		//set user as active.
		$db->run = "update ebb_users set active='1' where Username='$u'";
		$db->query();
		$db->close();
		#display message.
		$error = $login['correctinfo'];
		echo error($error, "general");
	}else{
		$error = $login['incorrectinfo'];
		echo error($error, "error");
	}
break;
case 'lostpassword':
	$page = new template($template_path ."/lostpassword.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$login[passwordrecovery]",
	"LANG-NOUSERNAME" => "$login[blank]",
	"LANG-USERNAME" => "$txt[username]",
	"LANG-REGISTER" => "$login[reg]",
	"LANG-EMAIL" => "$form[email]",
	"LANG-GETPASS" => "$userinfo[getpassword]"));
	$page->output();
  break;
  case 'process_lostpassword':
	$recover_info = var_cleanup($_POST['recover_info']);
	$new_pwd = makeRandomPassword();
	$random_password = md5($new_pwd.PWDSALT);
	$newActKey = md5(makeRandomPassword());
    
    #set error values to default.
	$error = 0;
	$errormsg = '';
        
	#see if user just hit enter.
	if(empty($recover_info)){
		$errormsg = $login['blank']."\n\n";
		$error = 1;
	}
	#see if username exist in database.
	$db->run = "SELECT Username, Email FROM ebb_users WHERE Username='$recover_info' OR Email='$recover_info' LIMIT 1";
	$username_chk = $db->num_results();
    $user_r = $db->result();
	$db->close();
	if($username_chk == 0){
		$errormsg .= $login['invalidrecoveryinfo']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		#change password.
		$db->run = "UPDATE ebb_users SET Password='$random_password', failed_attempts='0', active='0', act_key='$newActKey' WHERE Username='$user_r[Username]'";
		$db->query();
		$db->close();
        
        #call board setting function.
        $column = 'mail_type';
        $settings = board_settings($column);
        
		#send out email.
		require "lang/".$lang.".email.php";
		#subject.
		$lost_subject = $login['passwordrecovery'];
		$lost_message = pwd_reset();
		//get ready to send mail.
		if($settings['mail_type'] == 0){
			#call smtp class file.
			require "includes/smtp.php";
			//call up the smtp class.
			$mailer = new ebbmail;
			$mailer->ebbmail();
			//setup the subject of this newsletter.
			$mailer->Subject = $lost_subject;
			//get body of newsletter.
			$mailer->Body = $lost_message;
			//add users to the email list
			$mailer->AddBCC($user_r['Email']);
			//send out the email.
			$mailer->Send();
			//clear the list to prevent any double emails.
			$mailer->ClearAllRecipients();
		}else{
			//create a From: mailheader
			$headers = "From: $title <$board_email>";
			@mail($user_r['Email'], $lost_subject, $lost_message, $headers);
		}
		#display message.
		$error = $userinfo['emailsent'];
		echo error($error, "general");
	}
break;
default:
	if ((!isset($_COOKIE['ebbuser'])) OR (!isset($_SESSION['ebb_user']))){
		if(isset($_SERVER['HTTP_REFERER'])){
			$redirect = $_SERVER['HTTP_REFERER'];
		}else{
			$redirect = 'index.php'; 
		}
		$page = new template($template_path ."/login.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$login[login]",
		"LANG-NOUSERNAME" => "$login[nouser]",
		"LANG-NOPASSWORD" => "$login[nopass]",
		"TEXT" => "$login[text]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-REGISTER" => "$login[reg]",
		"LANG-PASSWORD" => "$login[pass]",
		"LANG-FORGOT" => "$login[forgot]",
		"LANG-REMEMBER" => "$login[rememberlogin]",
		"LANG-REMEMBERTXT" => "$login[remembertxt]",
		"LANG-LOGIN" => "$login[login]",
		"PATH-REDIRECT" => "$redirect"));
		$page->output();
	}else{
		$error = $login['alreadylogged'];
		echo error($error, "general");
	}
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
