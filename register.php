<?php
define('IN_EBB', true);
/*
Filename: register.php
Last Modified: 10/17/2012

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
  "PAGETITLE" => "$reg[register]",
  "LANG-HELP-TITLE" => "$help[registertitle]",
  "LANG-HELP-BODY" => "$help[registerbody]"));

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
//output top
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
}else{
	#user is already registered.
	header("Location: index.php"); 
}
#call board setting function.
$colume = 'proxy_block, mx_check, register_stat, Image_Verify, TOS_Status, TOS_Rules, mail_type, userstat, coppa, Default_Zone, Default_Language, Default_Style';
$settings = board_settings($colume);
#see if registration is open.
if($settings['register_stat'] == 0){
	$error = $reg['disabled'];
	echo error($error, "general");
	#terminate program after message appears.
	exit(); 
}
#see if user is on a proxy connection.
if ((detect_proxy() != $_SERVER["REMOTE_ADDR"]) and ($settings['proxy_block'] == 1)){
	// user is behind some kind of proxy, block them.
	$error = $reg['proxyblock'];
	echo error($error, "error");
}
//display register form.
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
switch ($action){
	case 'process':
		//get values from form.
		$email = var_cleanup($_POST['email']);
		$username = var_cleanup($_POST['username']);
		$password = var_cleanup($_POST['password']);
		$vert_password = var_cleanup($_POST['vert_password']);
		$time_zone = var_cleanup($_POST['time_zone']);
		$time_format = var_cleanup($_POST['time_format']);
		$pm_notice = var_cleanup($_POST['pm_notice']);
		$show_email = var_cleanup($_POST['show_email']);
		$style = var_cleanup($_POST['style']);
		$default_lang = var_cleanup($_POST['default_lang']);
		$agreecheck = (isset($_POST['agreecheck'])) ? var_cleanup($_POST['agreecheck']) : null;
		$coppavalid = (isset($_POST['coppavalid'])) ? var_cleanup($_POST['coppavalid']) : null;
		$number = (isset($_POST['img_vert'])) ? var_cleanup($_POST['img_vert']) : null;
		$emailvalidate = valid_email($email);
		$rss1 = "http://rss.msnbc.msn.com/id/3032091/device/rss/rss.xml";
		$rss2 = "http://news.google.com/nwshp?hl=en&tab=wn&output=rss";
		if($settings['mx_check'] == 1){
			if(!function_exists('checkdnsrr')){
				#MX check not available on window servers, so it's set to true by default(window support added to PHP 5.3.0)
				$email_mx_chk = true;
			}else{
				$email_mx_chk = validate_email_mx($email);
			}
		}
		$IP = $_SERVER['REMOTE_ADDR'];
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#spam check.
		$username_chk = language_filter($username, 2);
		//error checking.
		if (($settings['Image_Verify'] == 1) AND (empty($number))){
			$errormsg = $reg['noimgvert']."\n\n";
			$error = 1;
		}elseif (($settings['Image_Verify'] == 2) AND (empty($number))){
			$errormsg = $reg['noimgvert']."\n\n";
			$error = 1;
		}
		if (($settings['TOS_Status'] == 1) AND ($agreecheck == "")){
			$errormsg .= $reg['disagreetos']."\n\n";
			$error = 1;
		}
		if (($settings['coppa'] != 0) AND ($coppavalid == "")){
			$errormsg .= $reg['disagreecoppa']."\n\n";
			$error = 1;
		}		
		if(($settings['mx_check'] == 1) and ($email_mx_chk == false)){
			$errormsg .= $reg['mxfailed']."\n\n";
			$error = 1;
		}
		if (empty($style)){
			$errormsg .= $reg['nostyle']."\n\n";
			$error = 1;
		}
		if (empty($default_lang)){
			$errormsg .= $reg['nolang']."\n\n";
			$error = 1;
		}
		if ($time_zone == ""){
			$errormsg .= $reg['notimezone']."\n\n";
			$error = 1;
		}
		if (empty($time_format)){
			$errormsg .= $reg['notimeformat']."\n\n";
			$error = 1;
		}
		if ($pm_notice == ""){
			$errormsg .= $reg['nopmnotify']."\n\n";
			$error = 1;
		}
		if ($show_email == ""){
			$errormsg .= $reg['noshowemail']."\n\n";
			$error = 1;
		}
		if (empty($username)){
			$errormsg .= $reg['nouser']."\n\n";
			$error = 1;
		}
		if (empty($email)){
			$errormsg .= $reg['noemail']."\n\n";
			$error = 1;
		}
		if (empty($password)){
			$errormsg .= $reg['nopass']."\n\n";
			$error = 1;
		}
		if (empty($vert_password)){
			$errormsg .= $reg['novertpass']."\n\n";
			$error = 1;
		}
		if (preg_match('[^A-Za-z0-9]', $username)){
			$errormsg .= $reg['invalidchar']."\n\n";
			$error = 1;
		}
		if($emailvalidate == 1){
			$errormsg .= $reg['invalidemail']."\n\n";
			$error = 1;
		}
		if ($vert_password !== $password){
			$errormsg .= $reg['nomatch']."\n\n";
			$error = 1;
		}
		if(strlen($username) > 25){
			$errormsg .= $reg['longusername']."\n\n";
			$error = 1;
		}
		if(strlen($username) < 4){
			$errormsg .= $reg['shortusername']."\n\n";
			$error = 1;
		}
		if(strlen($email) > 255){
			$errormsg .= $reg['longemail']."\n\n";
			$error = 1;
		}
		if(strlen($time_format) > 14){
			$errormsg .= $cp['longtimeformat']."\n\n";
			$error = 1;
		}
		//check to see if the user & email have already been used already.
		$db->run = "SELECT Email FROM ebb_users WHERE Email='$email'";
		$email_check = $db->num_results();
		$db->close();
		$db->run = "SELECT Username FROM ebb_users WHERE Username='$username'";
		$username_check = $db->num_results();
		$db->close();
		if(($email_check == 1) || ($username_check == 1)){
			if($email_check == 1){
				$errormsg .= $reg['emailexist']."\n\n";
				$error = 1;
			}
			if($username_check == 1){
				$errormsg .= $reg['usernameexist']."\n\n";
				$error = 1;
			}
		}
		//see if username used is in blacklist.
		$blklist = blacklisted_usernames($username);
		if ($blklist == 1) {
			$errormsg .= $txt['usernameblacklisted']."\n\n";
			$error = 1;
		}
		//see if the email being used is on the banlist.
		$emailban = check_email($email);
		if ($emailban == 1){
			$errormsg .= $txt['emailban']."\n\n";
			$error = 1;
		}
		//see if a username/IP was banned.
		echo check_ban();
		//do image verify if enabled.
		if (($settings['Image_Verify'] == 1) or ($settings['Image_Verify'] == 2)){
			$match_check = md5($number);
			//see if the security image and the user's text match.
			if ($match_check !== $_SESSION['image_value']){
				$errormsg .= $reg['imgvertnomatch']."\n\n";
				$error = 1;
			}else{
				//number correct, remove the random value from session.
				session_destroy();
			}
		}
		//see if activation is set to either User or Admin.
		if($active_type == "User"){
			$active_stat = 0;
			$act_key = md5(makeRandomPassword());
		}elseif($active_type == "Admin"){
			$active_stat = 0;
			$act_key = '';
		}else{
			$active_stat = 1;
			$act_key = '';
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			$pass = md5($password.PWDSALT);
			$time = time();
			#see if admin has set a group rule to new users.
			if($settings['userstat'] == 0){
				//add user to db.
				$db->run = "INSERT INTO ebb_users (Email, Username, Password, Status, Date_Joined, IP, Time_format, Time_Zone, PM_Notify, Hide_Email, Style, Language, active, act_key, rssfeed1, rssfeed2, banfeeds) VALUES('$email', '$username', '$pass', 'Member', $time, '$IP', '$time_format', '$time_zone', '$pm_notice', '$show_email', '$style', '$default_lang', '$active_stat', '$act_key', '$rss1', '$rss2', '0')";
				$db->query();
				$db->close();
			}else{
				//add user to db.
				$db->run = "INSERT INTO ebb_users (Email, Username, Password, Status, Date_Joined, IP, Time_format, Time_Zone, PM_Notify, Hide_Email, Style, Language, active, act_key, rssfeed1, rssfeed2, banfeeds) VALUES('$email', '$username', '$pass', 'groupmember', $time, '$IP', '$time_format', '$time_zone', '$pm_notice', '$show_email', '$style', '$default_lang', '$active_stat', '$act_key', '$rss1', '$rss2', '0')";
				$db->query();
				$db->close();
				//add user to group admin requested.
				$db->run = "insert into ebb_group_users (Status, gid, Username) values('Active', '$settings[userstat]', '$username')";
				$db->query();
				$db->close();
			}
			//send out email to remind user they created an account.
			require "lang/".$lang.".email.php";
			if($active_type == "User"){
				#subject.
				$user_verify_subject = $reg['usersubject'];
				$user_verify_msg = user_confirm();
				//send out email to user.
				if($settings['mail_type'] == 0){
					#call smtp class file.
					require "includes/smtp.php";
					//call up the smtp class.
					$mailer = new ebbmail;
					$mailer->ebbmail();
					//setup the subject of this newsletter.
					$mailer->Subject = $user_verify_subject;
					//get body of newsletter.
					$mailer->Body = $user_verify_msg;
					//add users to the email list
					$mailer->AddBCC($email);
					//send out the email.
					$mailer->Send();
					//clear the list to prevent any double emails.
					$mailer->ClearAllRecipients();
				}else{
					//create a From: mailheader
					$headers = "From: $title <$board_email>";
					@mail($email, $user_verify_subject, $user_verify_msg, $headers);
				}
				$error = $reg['acctuser'];
				echo error($error, "general");
				}elseif($active_type == "Admin"){
					#subject.
					$admin_verify_subject = $reg['adminsubject'];
					$admin_verify_msg = admin_confirm();
					//send out email to user.
					if($settings['mail_type'] == 0){
						#call smtp class file.
						require "includes/smtp.php";
						//call up the smtp class.
						$mailer = new ebbmail;
						$mailer->ebbmail();
						//setup the subject of this newsletter.
						$mailer->Subject = $admin_verify_subject;
						//get body of newsletter.
						$mailer->Body = $admin_verify_msg;
						//add users to the email list
						$mailer->AddBCC($email);
						//send out the email.
						$mailer->Send();
						//clear the list to prevent any double emails.
						$mailer->ClearAllRecipients();
					}else{
						//create a From: mailheader
						$headers = "From: $title <$board_email>";
						@mail($email, $admin_verify_subject, $admin_verify_msg, $headers);
					}
					$error = $reg['acctadmin'];
					echo error($error, "general");
				}else{
					#Subject for new user.
					$register_subject = $reg['nonesubject'].$title;
					$register_message = none_confirm();
					//send out email to user.
					if($settings['mail_type'] == 0){
						#call smtp class file.
						require "includes/smtp.php";
						//call up the smtp class.
						$mailer = new ebbmail;
						$mailer->ebbmail();
						//setup the subject of this newsletter.
						$mailer->Subject = $register_subject;
						//get body of newsletter.
						$mailer->Body = $register_message;
						//add users to the email list
						$mailer->AddBCC($email);
						//send out the email.
						$mailer->Send();
						//clear the list to prevent any double emails.
						$mailer->ClearAllRecipients();
					}else{
						//create a From: mailheader
						$headers = "From: $title <$board_email>";
						@mail($email, $register_subject, $register_message, $headers);
					}
					$error = $reg['acctmade'];
					echo error($error, "general");
				}
		}
	break;
	default:
		#TOS check.
		if($settings['TOS_Status'] == 1){
			$tos_support = "1";
			$tos = "<textarea name=\"tos_msg\" rows=\"5\" cols=\"40\" class=\"text\" readonly=readonly>$settings[TOS_Rules]</textarea><br /><input name=\"agreecheck\" type=\"checkbox\" value=\"yes\" id=\"tos\"><b>$form[agree]</b><div id=\"toserr\"></div>";
		}else{
			$tos = '';
			$tos_support = "0";
		}
		#CAPTCHA check.
		if ($settings['Image_Verify'] == 1){
			$captcha_txt = "$form[securityimage]<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('$form[securitynotice]', this, event, '150px')\">[?]</a>";
			$captcha = "<input type=\"text\" name=\"img_vert\" size=\"10\" maxlength=\"6\" class=\"text\" id=\"captcha\" />&nbsp;<img src=\"security_image2.php\" alt=\"\" id=\"captchaImg\" /><a href=\"#\" onClick=\"return reloadElement('captchaImg');\">Reload CAPTCHA</a><div id=\"captchaerr\"></div>";
			$captcha_support = "1";
		}elseif($settings['Image_Verify'] == 2){
			$captcha_txt = "$form[securityimage]<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('$form[securitynotice]', this, event, '150px')\">[?]</a>";
            $captcha = "<input type=\"text\" name=\"img_vert\" size=\"10\" maxlength=\"6\" class=\"text\" id=\"captcha\" />&nbsp;<img src=\"security_image.php\" alt=\"\" id=\"captchaImg\" /><a href=\"#\" onClick=\"return reloadElement('captchaImg');\">Reload CAPTCHA</a><div id=\"captchaerr\"></div>";
			$captcha_support = "1";
		}else{
			$captcha_txt = '';
			$captcha = '';
			$captcha_support = "0";
		}
		#COPPA check.
		if($settings['coppa'] == 13){
			$coppa_txt = "<input name=\"coppavalid\" type=\"checkbox\" value=\"yes\" id=\"coppa\"><b>$reg[coppa13]</b><div id=\"coppaerr\"></div>";		
			$coppa_support = "1";
		}elseif($settings['coppa'] == 16){
			$coppa_txt = "<input name=\"coppavalid\" type=\"checkbox\" value=\"yes\" id=\"coppa\"><b>$reg[coppa16]</b><div id=\"coppaerr\"></div>";
			$coppa_support = "1";
		}elseif($settings['coppa'] == 18){
			$coppa_txt = "<input name=\"coppavalid\" type=\"checkbox\" value=\"yes\" id=\"coppa\"><b>$reg[coppa18]</b><div id=\"coppaerr\"></div>";
			$coppa_support = "1";
		}elseif($settings['coppa'] == 21){
			$coppa_txt = "<input name=\"coppavalid\" type=\"checkbox\" value=\"yes\" id=\"coppa\"><b>$reg[coppa21]</b><div id=\"coppaerr\"></div>";
			$coppa_support = "1";
		}else{
			$coppa_txt = '';
			$coppa_support = "0";
		}
		$timezone = timezone_select($settings['Default_Zone']);
		$style = style_select($settings['Default_Style']);
		$language = lang_select($settings['Default_Language']);
		$page = new template($template_path ."/register.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$reg[register]",
		"TOSSTAT" => "$tos_support",
		"CAPTCHASTAT" => "$captcha_support",
		"COPPASTAT" => "$coppa_support",
		"LANG-LONGUSERNAME" => "$reg[longusername]",
		"LANG-SHORTUSER" => "$reg[shortusername]",
		"LANG-INVALIDUSER" => "$reg[invalidchar]",
		"LANG-LONGEMAIL" => "$reg[longemail]",
		"LANG-INVALIDEMAIL" => "$reg[invalidemail]",
		"LANG-LONGPASS" => "$reg[longpassword]",
		"LANG-NOPASS" => "$reg[nopass]",
		"LANG-NOVPASS" => "$reg[novertpass]",
		"LANG-NOPWDMATCH" => "$reg[nomatch]",
		"LANG-NOTIME" => "$reg[notimeformat]",
		"LANG-NOPM" => "$reg[nopmnotify]",
		"LANG-NOHIDEEMAIL" => "$reg[noshowemail]",
		"LANG-INVALIDTOS" => "$reg[disagreetos]",
		"LANG-INVALIDCOPPA" => "$reg[disagreecoppa]",
		"LANG-NOCAPTCHA" => "$reg[noimgvert]",
		"LANG-EMAIL" => "$form[email]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-RULE" => "$reg[nospecialchar]",
		"LANG-PASSWORD" => "$login[pass]",
		"LANG-CORNFIRMPASSWORD" => "$form[confirmpass]",
		"LANG-TIME" => "$form[timezone]",
		"TIME" => "$timezone",
		"LANG-TIMEFORMAT" => "$form[timeformat]",
		"LANG-TIMEINFO" => "$form[timeinfo]",
		"TIMEFORMAT" => "$time_format",
		"LANG-PMNOTIFY" => "$form[pm_notify]",
		"LANG-SHOWEMAIL" => "$form[showemail]",
		"LANG-YES" => "$txt[yes]",
		"LANG-NO" => "$txt[no]",
		"LANG-STYLE" => "$form[style]",
		"STYLE" => "$style",
		"LANG-LANGUAGE" => "$form[defaultlang]",
		"LANGUAGE" => "$language",
		"LANG-CAPTCHA" => "$captcha_txt",
		"CAPTCHA" => "$captcha",
		"TOS" => "$tos",
		"COPPA" => "$coppa_txt",
		"SUBMIT" => "$reg[register]"));
		$page->output();
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
