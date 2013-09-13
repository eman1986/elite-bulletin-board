<?php
define('IN_EBB', true);
/*
Filename: generalcp.php
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
require "../phpmailer/class.phpmailer.php";
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
#get title.
switch($action){
case 'newsletter':
case 'mail_send':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 4);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$cp['newsletter'];
	$helpTitle = $help['newslettertitle'];
	$helpBody = $help['newsletterbody'];
break;
case 'smiles':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 6);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$post['smiles'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'add_smiles':
case 'add_smiles_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 6);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$cp['addsmiles'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'modify_smiles':
case 'modify_smiles_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 6);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$cp['modifysmiles'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'delete_smiles':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 6);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$cp['delsmiles'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'censor':
case 'censor_add':
case 'censor_modify':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 5);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$generalcptitle = $cp['generalmenu'].' - '.$cp['censor'];
	$helpTitle = $help['censortitle'];
	$helpBody = $help['censorbody'];
break;
default:
	$generalcptitle = $cp['generalmenu'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$generalcptitle",
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

switch($action){
case 'newsletter':
	$page = new template("../". $template_path ."/cp-newsletter.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-NEWSLETTER" => "$cp[newsletter]",
	"LANG-NOSUBJECT" => "$cp[nosubject]",
	"LANG-NOBODY" => "$cp[nomailmsg]",
	"LANG-TEXT" => "$cp[newslettertxt]",
	"LANG-SUBJECT" => "$pm[subject]",
	"LANG-MESSAGE" => "$report[message]",
	"LANG-SENDNEWSLETTER" => "$cp[sendnewsletter]"));
	$page->output();
break;
case 'mail_send':
	$subject = stripslashes($_POST['subject']);
	$mail_message = stripslashes($_POST['mail_message']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error checking.
	if(empty($subject)){
		$errormsg = $cp['nosubject']."\n\n";
		$error = 1;
	}
	if (empty($mail_message)){
		$errormsg .= $cp['nomailmsg']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//query.
		$db->run = "SELECT Email FROM ebb_users";
		$newsletter_q = $db->query();
		$db->close();
		#call board setting function.
		$colume = 'mail_type';
		$settings = board_settings($colume);
		//see what form of email will be used to send out the emails.
		if($settings['mail_type'] == 0){
			#call smtp class file.
			require "../includes/smtp.php";
			//call up the smtp class.
			$mailer = new ebbmail;
			$mailer->ebbmail();
			//setup the subject of this newsletter.
			$mailer->Subject = $subject;
			//get body of newsletter.
			$mailer->Body = $mail_message;
			//add users to the email list
			while ($row = mysql_fetch_array($newsletter_q)) {
				$mailer->AddBCC($row['Email']);
				//send out the email.
				$mailer->Send();
				//clear the list to prevent any double emails.
				$mailer->ClearAllRecipients();
			}
		}else{
			//create a From: mailheader
			$headers = "From: $title <$board_email>";
			while ($row = mysql_fetch_array($newsletter_q)) {
				set_time_limit(1200); //set the time higher if you need to.
				@mail($row['Email'], $subject, $mail_message, $headers);
			}
		}
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Sent out Newsletter", "$logged_user", "$acp_date", "$ip");
		//bring user back.
		header("Location: index.php");
	}
break;
case 'smiles':
	#display smiles.
	$admin_smiles = admin_smilelisting();
break;
case 'add_smiles':
	$page = new template("../". $template_path ."/cp-newsmiles.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-ADDSMILES" => "$cp[addsmiles]",
	"LANG-NOCODE" => "$cp[nosmilecodeerror]",
	"LANG-LONGCODE" => "$cp[longsmilecode]",
	"LANG-NOIMAGE" => "$cp[nosmilefileerror]",
	"LANG-LONGIMAGE" => "$cp[longsmilepath]",
	"LANG-SMILECODE" => "$cp[smilecode]",
	"LANG-SMILEFILE" => "$cp[smilefile]"));
	$page->output();
break;
case 'add_smiles_process':
	$smile_code = var_cleanup($_POST['smile_code']);
	$smile_file = var_cleanup($_POST['smile_file']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error checking.
	if (empty($smile_code)){
		$errormsg = $cp['nosmilecodeerror']."\n\n";
		$error = 1;
	}
	if (empty($smile_file)){
		$errormsg .= $cp['nosmilefileerror']."\n\n";
		$error = 1;
	}
	if(strlen($smile_code) > 30){
		$errormsg .= $cp['longsmilecode']."\n\n";
		$error = 1;
	}
	if(strlen($smile_file) > 80){
		$errormsg .= $cp['longsmilepath']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_smiles (code, img_name) values('$smile_code', '$smile_file')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("added new smile", "$logged_user", "$acp_date", "$ip");
		//bring user back.
		header("Location: generalcp.php?action=smiles");
	}
break;
case 'modify_smiles':
	#see if user added the Smile ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosmid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "select id from ebb_smiles where id='$id'";
	$modify_smiles_r = $db->result();
	$smile_chk = $db->num_results();
	$db->close();
	#see if the smile exist.
	if($smile_chk == 0){
		$error = $cp['smilenotexist'];
		echo acp_error($error, "error");
	}else{
		$page = new template("../". $template_path ."/cp-modifysmiles.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-MODIFYSMILES" => "$cp[modifysmiles]",
		"LANG-NOCODE" => "$cp[nosmilecodeerror]",
		"LANG-LONGCODE" => "$cp[longsmilecode]",
		"LANG-NOIMAGE" => "$cp[nosmilefileerror]",
		"LANG-LONGIMAGE" => "$cp[longsmilepath]",
		"ID" => "$id",
		"LANG-SMILECODE" => "$cp[smilecode]",
		"SMILECODE" => "$modify_smiles_r[code]",
		"LANG-SMILEFILE" => "$cp[smilefile]",
		"SMILEFILE" => "$modify_smiles_r[img_name]"));
		$page->output();
	}
break;
case 'modify_smiles_process':
	#see if user added the Smile ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosmid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "select id from ebb_smiles where id='$id'";
	$smile_chk = $db->num_results();
	$db->close();
	#see if the smile exist.
	if($smile_chk == 0){
		$error = $cp['smilenotexist'];
		echo acp_error($error, "error");
	}	
	$mod_smile_code = var_cleanup($_POST['smile_code']);
	$mod_smile_file = var_cleanup($_POST['smile_file']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if (empty($mod_smile_code)){
		$errormsg = $cp['nosmilecodeerror']."\n\n";
		$error = 1;
	}
	if (empty($mod_smile_file)){
		$errormsg .= $cp['nosmilefileerror']."\n\n";
		$error = 1;
	}
	if(strlen($mod_smile_code) > 30){
		$errormsg .= $cp['longsmilecode']."\n\n";
		$error = 1;
	}
	if(strlen($mod_smile_file) > 80){
		$errormsg .= $cp['longsmilepath']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "update ebb_smiles set code='$mod_smile_code', img_name='$mod_smile_file' where id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified smile", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: generalcp.php?action=smiles");
	}
break;
case 'delete_smiles':
	#see if user added the Smile ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nosmid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "select id from ebb_smiles where id='$id'";
	$smile_chk = $db->num_results();
	$db->close();
	#see if the smile exist.
	if($smile_chk == 0){
		$error = $cp['smilenotexist'];
		echo acp_error($error, "error");
	}else{
		//process query
		$db->run = "delete from ebb_smiles where id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Deleted smile", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: generalcp.php?action=smiles");
	}
break;
case 'censor':
	$admin_censorlist = admin_censorlist();
break;
case 'censor_add':
	$addcensor = var_cleanup($_POST['addcensor']);
	$censoraction = var_cleanup($_POST['censoraction']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if (empty($addcensor)){
		$errormsg = $cp['nocensor']."\n\n";
		$error = 1;
	}
	if(strlen($addcensor) > 50){
		$errormsg .= $cp['longcensor']."\n\n";
		$error = 1;
	}
	if($censoraction == ""){
		$errormsg .= $cp['nocensoraction']."\n\n";
		$error = 1;
	}
	if(!is_numeric($censoraction)){
		$errormsg .= $cp['invalidcensoraction']."\n\n"; 
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_censor (Original_Word, action) values('$addcensor', '$censoraction')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Added word to censor list", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: generalcp.php?action=censor");
	}
break;
case 'censor_modify':
	#see if user added the censor ID or not.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['nocensorid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "select id from ebb_censor where id='$id'";
	$censor_chk = $db->num_results();
	$db->close();
	#see if the word exist.
	if($censor_chk == 0){
		$error = $cp['censornotfound'];
		echo acp_error($error, "error");
	}else{
		//process query
		$db->run = "DELETE FROM ebb_censor WHERE id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Deleted a word from censor list", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: generalcp.php?action=censor");
	}
break;
default:
	header("Location: index.php"); 
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
