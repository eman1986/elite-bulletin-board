<?php
define('IN_EBB', true);
/*
Filename: Profile.php
Last Modified: 07/11/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";

#get mode.
if (isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
#move guest to main page.
if($stat == "guest"){
	header("Location: index.php");
}
#list action on title bar.
switch($mode){
	case 'edit_profile':
	case 'profile_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['editprofile'];
		$helpTitle = $help['editprofiletitle'];
		$helpBody = $help['editprofilebody'];
	break;
	case 'edit_sig':
	case 'sig_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['editsig'];
		$helpTitle = $help['sigtitle'];
		$helpBody = $help['sigbody'];
	break;
	case 'avatar':
	case 'avatar_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['avatarsetting'];
		$helpTitle = $help['avatartitle'];
		$helpBody = $help['avatarbody'];
	break;
	case 'new_email':
	case 'new_email_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['emailupdate'];
		$helpTitle = $help['nohelptitle'];
		$helpBody = $help['nohelpbody'];
	break;
	case 'new_password':
	case 'new_password_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['changepassword'];
		$helpTitle = $help['nohelptitle'];
		$helpBody = $help['nohelpbody'];
	break;
	case 'groupmanager':
	case 'join_group':
	case 'unjoin_group':
		$pagetitle = $userinfo['title']. " - " . $userinfo['managegroups'];
		$helpTitle = $help['groupmanagertitle'];
		$helpBody = $help['groupmanagerbody'];
	break;
	case 'attachments':
	case 'deleteattachment':
		$pagetitle = $userinfo['title']. " - " . $post['manageattach'];
		$helpTitle = $help['attachmentmanagertitle'];
		$helpBody = $help['attachmentmanagerbody'];
	break;
	case 'digest':
	case 'digest_process':
		$pagetitle = $userinfo['title']. " - " . $userinfo['subscriptionsetting'];
		$helpTitle = $help['digesttitle'];
		$helpBody = $help['digestbody'];
	break;
	default:
 		$permission_chk_profile = access_vaildator($permission_type, 31);
 		if($permission_chk_profile == 0){
			die($txt['accessdenied']); 		
 		} 		
		$pagetitle = $userinfo['title']. " - " . $userinfo['viewprofile'];
		$helpTitle = $help['nohelptitle'];
		$helpBody = $help['nohelpbody'];
}

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$pagetitle",
  "LANG-HELP-TITLE" => "$helpTitle",
  "LANG-HELP-BODY" => "$helpBody"));
$page->output();
//check to see if the install file is still on the user's server.
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
//display profile
switch ($mode){
	case 'edit_profile':
		$permission_chk_ctitle = access_vaildator($permission_type, 30);
		#set the columes needed for now.
		$columes = 'PM_Notify, Hide_Email, Custom_Title, Style, Time_format, Language, Time_Zone, MSN, AOL, ICQ, Yahoo, WWW, Location, rssfeed1, rssfeed2';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		#call board setting function.
		$colume = 'Default_Zone, Default_Style';
		$settings = board_settings($colume);
		#pm notify detect.
		if ($userpref['PM_Notify'] == 1){
			$pmnotice_status = "<input type=\"radio\" name=\"pm_notice\" value=\"1\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"pm_notice\" value=\"0\" class=\"text\" />$txt[no]";
		}else{
			$pmnotice_status = "<input type=\"radio\" name=\"pm_notice\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"pm_notice\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
		}
		#hide email detect.
		if($userpref['Hide_Email'] == 0){
			$showemail_status = "<input type=\"radio\" name=\"show_email\" value=\"0\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"show_email\" value=\"1\" class=\"text\" />$txt[no]";
		}else{
			$showemail_status = "<input type=\"radio\" name=\"show_email\" value=\"0\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"show_email\" value=\"1\" class=\"text\" checked=checked />$txt[no]";
		}
		#see if user can make a custom title.
		if($permission_chk_ctitle == 0){
			$customtitle = "<input type=\"text\" name=\"ctitle\" value=\"$userpref[Custom_Title]\" class=\"text\" size=\"30\" id=\"ctitle\" readonly=readonly /><div id=\"ctitleerr\"></div>";
		}else{
			$customtitle = "<input type=\"text\" name=\"ctitle\" value=\"$userpref[Custom_Title]\" class=\"text\" maxlength=\"20\" size=\"30\" id=\"ctitle\" /><div id=\"ctitleerr\"></div>"; 
		}
		//output
		$timezone = timezone_select($userpref['Time_Zone']);
		$style = style_select($userpref['Style']);
		$language = lang_select($userpref['Language']);
		$page = new template($template_path ."/editprofile.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITPROFILE" => "$userinfo[editprofile]",
		"LANG-LONGPASS" => "$reg[longpassword]",
		"LANG-NOPASS" => "$reg[novertpass]",
		"LANG-CTITLE" => "$userinfo[longctitle]",
		"LANG-LONGMSN" => "$userinfo[longmsn]",
		"LANG-LONGAIM" => "$userinfo[longaol]",
		"LANG-LONGICQ" => "$userinfo[longicq]",
		"LANG-LONGYIM" => "$userinfo[longyim]",
		"LANG-LONGWWW" => "$userinfo[longwww]",
		"LANG-INVALIDURL" => "$userinfo[invalidurl]",
		"LANG-NOTIMEFORM" => "$reg[notimeformat]",
		"LANG-LONGTIMEFORM" => "$cp[longtimeformat]",
		"LANG-LONGLOCATE" => "$userinfo[longloc]",
		"LANG-LONGRSS" => "$userinfo[longrss]",
		"LANG-TEXT" => "$userinfo[editprofiletxt]",
		"LANG-ENTERPASS" => "$userinfo[enterpass]",
		"LANG-CURRPASS" => "$userinfo[currentpass]",
		"LANG-PMNOTIFY" => "$form[pm_notify]",
		"PMNOTIFY" => "$pmnotice_status",
		"LANG-SHOWEMAIL" => "$form[showemail]",
		"SHOWEMAIL" => "$showemail_status",
		"LANG-CUSTOMTITLE" => "$userinfo[customtitle]",
		"CUSTOMTITLE" => "$customtitle",
		"LANG-MSN" => "$form[msn]",
		"MSN" => "$userpref[MSN]",
		"LANG-AOL" => "$form[aol]",
		"AOL" => "$userpref[AOL]",
		"LANG-ICQ" => "$form[icq]",
		"ICQ" => "$userpref[ICQ]",
		"LANG-YAHOO" => "$form[yim]",
		"YAHOO" => "$userpref[Yahoo]",
		"LANG-WWW" => "$form[www]",
		"WWW" => "$userpref[WWW]",
		"LANG-TIME" => "$form[timezone]",
		"TIME" => "$timezone",
		"LANG-TIMEFORMAT" => "$form[timeformat]",
		"LANG-TIMEINFO" => "$form[timeinfo]",
		"TIMEFORMAT" => "$userpref[Time_format]",
		"LANG-STYLE" => "$form[style]",
		"STYLE" => "$style",
		"LANG-LANGUAGE" => "$form[defaultlang]",
		"LANGUAGE" => "$language",
		"LANG-LOCATION" => "$form[location]",
		"LOCATION" => "$userpref[Location]",
		"LANG-PICKRSSFEED" => "$userinfo[rssfeedhnt]",
		"LANG-RSSFEED1" => "$userinfo[rssfeed1]",
		"RSSFEED1" => "$userpref[rssfeed1]",
		"LANG-RSSFEED2" => "$userinfo[rssfeed2]",
		"RSSFEED2" => "$userpref[rssfeed2]",
		"SUBMIT" => "$userinfo[saveprofile]"));
		$page->output();
	break;
	case 'profile_process':
		$conpass = var_cleanup($_POST['conpass']);
		$pm_notice = var_cleanup($_POST['pm_notice']);
		$show_email = var_cleanup($_POST['show_email']);
		$ctitle = var_cleanup($_POST['ctitle']);
		$msn = var_cleanup($_POST['msn']);
		$aol = var_cleanup($_POST['aol']);
		$yim = var_cleanup($_POST['yim']);
		$icq = var_cleanup($_POST['icq']);
		$www = var_cleanup($_POST['www']);
		$location = var_cleanup($_POST['location']);
		$time_zone = var_cleanup($_POST['time_zone']);
		$time_format = var_cleanup($_POST['time_format']);
		$style = var_cleanup($_POST['style']);
		$lang = var_cleanup($_POST['default_lang']);
		$rssfeed1 = var_cleanup($_POST['rssfeed1']);
		$rssfeed2 = var_cleanup($_POST['rssfeed2']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		//error check.
		#set the columes needed for now.
		$columes = 'Password, banfeeds';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		#call board setting function.
		$permission_chk_ctitle = access_vaildator($permission_type, 30);
		if($permission_chk_ctitle == 0){
			$errormsg = $userinfo['nocustomtitle']."\n\n";
			$error = 1;
		}
		#see if user is allowed to adjust their RSS feeds.
		if ($userpref['banfeeds'] == 1){
			$errormsg .= $userinfo['disabledrss']."\n\n";
			$error = 1;
		}
		if(strlen($ctitle) > 20){
			$errormsg .= $userinfo['longctitle']."\n\n";
			$error = 1;
		}
		if((!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $rssfeed1)) and (!empty($rssfeed1))){
			$errormsg .= $userinfo['invalidurl']."\n\n";
			$error = 1;
		}
		if((!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $rssfeed2)) and (!empty($rssfeed2))){
			$errormsg .= $userinfo['invalidurl']."\n\n";
			$error = 1;
		}
		if(strlen($rssfeed1) > 200){
			$errormsg .= $userinfo['longrss']."\n\n";
			$error = 1;
		}
		if(strlen($rssfeed2) > 200){
			$errormsg .= $userinfo['longrss']."\n\n";
			$error = 1;
		}
		if ((!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $www)) and (!empty($www))) {
			$errormsg .= $userinfo['invalidurl']."\n\n";
			$error = 1;
		}
		if(strlen($msn) > 255){
			$errormsg .= $userinfo['longmsn']."\n\n";
			$error = 1;
		}
		if(strlen($aol) > 255){
			$errormsg .= $userinfo['longaol']."\n\n";
			$error = 1;
		}
		if(strlen($yim) > 255){
			$errormsg .= $userinfo['longyim']."\n\n";
			$error = 1;
		}
		if(strlen($icq) > 15){
			$errormsg .= $userinfo['longicq']."\n\n";
			$error = 1;
		}
		if(strlen($www) > 200){
			$errormsg .= $userinfo['longwww']."\n\n";
			$error = 1;
		}
		if(strlen($location) > 70){
			$errormsg .= $userinfo['longloc']."\n\n"; 
			$error = 1;
		}
		if (empty($style)){
			$errormsg .= $reg['nostyle']."\n\n";
			$error = 1;
		}
		if (empty($lang)){
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
		if (empty($conpass)){
			$errormsg .= $reg['novertpass']."\n\n";
			$error = 1;
		}
		if(strlen($conpass) > 12){
			$errormsg .= $reg['longpassword']."\n\n";
			$error = 1;
		}
		if(strlen($time_format) > 14){
			$errormsg .= $cp['longtimeformat']."\n\n";
			$error = 1;
		}
		$pass = md5($conpass.PWDSALT);
		//see if password matches.
		if ($userpref['Password'] !== $pass){
			$errormsg .= $userinfo['curpassnomatch']."\n\n";
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process query
			$db->run = "UPDATE ebb_users SET PM_Notify='$pm_notice', Hide_Email='$show_email', Custom_Title='$ctitle', MSN='$msn', AOL='$aol', Yahoo='$yim', ICQ='$icq', WWW='$www', Location='$location', Time_Zone='$time_zone', Time_format='$time_format', Style='$style', Language='$lang', rssfeed1='$rssfeed1', rssfeed2='$rssfeed2' WHERE Username='$logged_user'";
			$db->query();
			$db->close();
			//bring user back
			header("Location: Profile.php");
		}
	break;
	case 'edit_sig':
		$permission_chk_sig = access_vaildator($permission_type, 33);
		if($permission_chk_sig == 0){
			$error = $txt['accessdenied'];
			echo error($error, "error");		
		}
		#set the columes needed for now.
		$columes = 'Sig';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		$sig = $userpref['Sig'];
		#settings.
		$allowsmile = 1;
		$allowbbcode = 1;
		$allowimg = 1;
		#format signature.
		if(isset($sig) && strlen($sig) > 0){
			$displaysig = nl2br(smiles(BBCode(language_filter($sig, 1), true)));
		} else {
			$displaysig = '';
		}
		#call bbcode functions.
		$bbcode = bbcode_form('sig');
		$smile = form_smiles('sig');
		#template output.
		$page = new template($template_path ."/editsig.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITSIG" => "$userinfo[editsig]",
		"LANG-LONGSIG" => "$reg[longsig]",
		"LANG-TEXT" => "$userinfo[sigtxt]",
		"BBCODE" => "$bbcode",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-CURRENTSIG" => "$userinfo[cursig]",
		"CURRENTSIG" => "$displaysig",
		"SIGNATURE" => "$sig",
		"LANG-SAVESIG" => "$userinfo[savesignature]"));
		$page->output();
	break;
	case 'sig_process':
		$permission_chk_sig = access_vaildator($permission_type, 33);
		if($permission_chk_sig == 0){
			$error = $txt['accessdenied'];
			echo error($error, "error");		
		} 
		$signature = var_cleanup($_POST['signature']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		//error check
		if(strlen($signature) > 255){
			$errormsg = $reg['longsig']."\n\n";
			$error = 1;
		}
		if(strlen($signature) > 0){
			language_filter($signature, 2);
		}
		
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process query
			$db->run = "Update ebb_users SET Sig='$signature' Where Username='$logged_user'";
			$db->query();
			$db->close();
			//bring user back
			header("Location: Profile.php?mode=edit_sig");
		}
	break;
	case 'groupmanager':
		$permission_chk_sig = access_vaildator($permission_type, 34);
		if($permission_chk_sig == 0){
			$error = $userinfo['group_access'];
			echo error($error, "error");		
		}
		//output html.
		$joined_group = groups_joined();
	break;
	case 'join_group':
		$permission_chk_sig = access_vaildator($permission_type, 34);
		if($permission_chk_sig == 0){
			$error = $userinfo['group_access'];
			echo error($error, "error");		
		}
		$id = var_cleanup($_GET['id']);
		$db->run = "select Enrollment from ebb_groups where id='$id'";
		$status_check = $db->result;
		$num_chk = $db->num_results();
		$db->close();
		//see if user is already a member of this group.
		$db->run = "select gid from ebb_group_users where Username='$logged_user' AND gid='$id'";
		$membership_chk = $db->num_results();
		$db->close();
		if ($membership_chk == 1){
			$error = $userinfo['alreadyjoined'];
			echo error($error, "error");
		}
		//see if the group exist.
		if ($num_chk == 1){
			if ($status_check['Enrollment'] == 0){
				$error = $userinfo['locked'];
				echo error($error, "error");
			}else{
				$db->run = "insert into ebb_group_users (Username, gid, Status) values('$logged_user', '$id', 'Pending')";
				$db->query();
				$db->close();
				header("Location: Profile.php");
			}
		}else{
			$error = $groups['notexist'];
			echo error($error, "error");
		}
	break;
	case 'unjoin_group':
		$permission_chk_sig = access_vaildator($permission_type, 34);
		if($permission_chk_sig == 0){
			$error = $userinfo['group_access'];
			echo error($error, "error");		
		}
		$id = var_cleanup($_GET['id']);
		$db->run = "delete from ebb_group_users where Username='$logged_user' and gid='$id'";
		$db->query();
		$db->close();
		#place user as a member.
		$db->run = "update ebb_users SET Status='Member' where Username='$logged_user'";
		$db->query();
		$db->close();
		header("Location: Profile.php");
	break;
	case 'avatar':
		$permission_chk_avatar = access_vaildator($permission_type, 32);
		if($permission_chk_avatar == 0){
			$error = $userinfo['avatar_access'];
			echo error($error, "error");		
		}
		#set the columes needed for now.
		$columes = 'Avatar';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		$allowed = "$userinfo[allowed] <b>.gif .jpeg .jpg .png</b>";
		#see if user has a avatar.
		if (empty($userpref['Avatar'])){
			$avatar = "images/noavatar.gif";
		}else{
			$avatar = $userpref['Avatar'];
		}
		$page = new template($template_path ."/editavatar.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITAVATAR" => "$userinfo[avatarsetting]",
		"LANG-LONGAVATAR" => "$userinfo[longavatar]",
		"LANG-INVALIDURL" => "$userinfo[invalidurl]",
		"LANG-TEXT" => "$userinfo[avatartxt]",
		"LANG-CURRENTAVATAR" => "$userinfo[currentavatar]",
		"CURRENTAVATAR" => "$avatar",
		"ALLOWEDTYPES" => "$allowed",
		"LANG-SAVEAVATAR" => "$userinfo[saveavatar]",
		"LANG-GALLERY" => "$avatargallery[title]"));
		$page->output();
	break;
	case 'avatar_process':
		$permission_chk_avatar = access_vaildator($permission_type, 32);
		if($permission_chk_avatar == 0){
			$error = $userinfo['avatar_access'];
			echo error($error, "error");		
		}
		#get form value.
		$avatar_img = var_cleanup($_POST['avatar_img']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		if(empty($avatar_img)){
			$db->run = "Update ebb_users SET Avatar='' Where Username='$logged_user'";
			$db->query();
			$db->close(); 
			header("Location: Profile.php?mode=avatar");
		}elseif ((!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $avatar_img)) and (!empty($avatar_img))) {
			$error = $userinfo['invalidurl'];
			echo error($error, "error");
			exit();
		}else{
			#extract information regarding this avatar and see if it meets the standards.
			//use curl if it exists
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $avatar_img);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_RANGE, '0-5120'); //limit to prevent abuse. WARNING: THIS IS NOT OBSERVED BY ALL SERVERS!
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Range: bytes=0-5120")); //limit to prevent abuse. WARNING: THIS IS NOT OBSERVED BY ALL SERVERS!
				$data = curl_exec($ch);

				//ensure connection was successful.
				if($data !== false && strlen($data) < 5120) {
					//get image information.
					$resource = imagecreatefromstring($data);
					$width = imagesx($resource);
					$height = imagesy($resource);
					$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
					curl_close($ch);
				} else {
					$error = $txt['errormsg']; //TODO very vague message, version 3 provides a better message.
					echo error($error, "error");
				}
			} else {
				$imgInfo = getimagesize($avatar_img);
				
				if ($imgInfo){
					$width = $imgInfo[0];
					$height = $imgInfo[1];
					$mime = $imgInfo['mime'];
				}else {
					$error = $txt['errormsg']; //TODO very vague message, version 3 provides a better message.
					echo error($error, "error");
				}
			}

			#compile a list of allowed mime types.
			$allowed = array("image/gif", "image/jpeg", "image/jpg", "image/png");
			if (!in_array($mime, $allowed)){
				$errormsg = $userinfo['wrongtype']."\n\n";
				$error = 1;
			}
			if(strlen($avatar_img) > 255){
				$errormsg .= $userinfo['longavatar']."\n\n";
				$error = 1;
			}
			if(($width > 100) or ($height > 100)){
				$errormsg .= $userinfo['lgavatar']."\n\n";
				$error = 1;
			}
			#see if any errors occured and if so report it.
			if ($error == 1){
				$error = nl2br($errormsg);
				echo error($error, "validate");
			}else{
				//process query
				$db->run = "Update ebb_users SET Avatar='$avatar_img' Where Username='$logged_user'";
				$db->query();
				$db->close();
				//bring user back
				header("Location: Profile.php?mode=avatar");
			}
		}
	break;
	case 'attachments':
		//start pagenation.
		$count = 0;
		$count2 = 0;
		if(!isset($_GET['pg'])){
		    $pg = 1;
		}else{
		    $pg = var_cleanup($_GET['pg']);
		}
		#call board setting function.
		$colume = 'per_page';
		$settings = board_settings($colume);
		// Figure out the limit for the query based on the current page number.
		$from = (($pg * $settings['per_page']) - $settings['per_page']);

		$db->run = "select id, Filename, File_Size, tid, pid from ebb_attachments where Username='$logged_user' LIMIT $from, $settings[per_page]";
		$attach_q = $db->query();
		$db->close();

		$db->run = "select id, Filename, File_Size, tid, pid from ebb_attachments where Username='$logged_user'";
		$num = $db->num_results();
		$db->close();
		#output pagination.
		$pagenation = pagination("mode=attachments&amp;");
		#output attachment list.
		attach_manager("profile");
	break;
	case 'deleteattachment': 
		#get form values.
		if(isset($_GET['id'])){    			 
			$id = var_cleanup($_GET['id']);
		}else{
			die($txt['noattachid']); 
		}
		#get filename from db.
		$db->run = "select Filename from ebb_attachments where id='$id'";
		$attach_r = $db->result();
		$db->close();
		#delete file from web space.
		$delattach = @unlink ('uploads/'. $attach_r['Filename']);
		if($delattach){
			#remove entry from db.
			$db->run = "delete from ebb_attachments where id='$id'";
			$db->query();
			$db->close();
			#go back to attachment form.
			header ("Location: Profile.php?mode=attachments");
		}else{
			$error = $post['cantdelete']; 
			echo error($error, "error"); 
		}  
	break;
	case 'digest':
		//start pagenation.
		$count = 0;
		$count2 = 0;
		if(!isset($_GET['pg'])){
		    $pg = 1;
		}else{
		    $pg = var_cleanup($_GET['pg']);
		}
		#call board setting function.
		$colume = 'per_page';
		$settings = board_settings($colume);
		// Figure out the limit for the query based
		// on the current page number.
		$from = (($pg * $settings['per_page']) - $settings['per_page']);
		// Figure out the total number of results in DB:
		$db->run = "SELECT tid FROM ebb_topic_watch WHERE username='$logged_user' LIMIT $from, $settings[per_page]";
		$sub_q = $db->query();
		$db->close();

		$db->run = "SELECT tid FROM ebb_topic_watch WHERE username='$logged_user'";
		$num = $db->num_results();
		$db->close();
		#output pagination.
		$pagenation = pagination("mode=digest&amp;");
		#display subscriptions.
		digest_list();
	break;
	case 'digest_process':
		if((!isset($_GET['del'])) or (empty($_GET['del']))){
			$error = $txt['invalidaction'];
			echo error($error, "error");
		}else{
			$del = var_cleanup($_GET['del']); 
		}
		//process query
		SubscriptionManager($del, "unsubscribe");

		//bring user back
		header("Location: Profile.php?mode=digest");
	break;
	case 'new_email':
		$page = new template($template_path ."/editemail.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITEMAIL" => "$userinfo[emailupdate]",
		"LANG-LONGEMAIL" => "$reg[longemail]",
		"LANG-INVALIDEMAIL" => "$reg[invalidemail]",
		"LANG-NOMATCH" => "$userinfo[nocemailmatch]",
		"LANG-TEXT" => "$userinfo[emailtxt]",
		"LANG-CURREMAIL" => "$userinfo[currentemail]",
		"LANG-NEWEMAIL" => "$userinfo[newemail]",
		"LANG-CONFIRMEMAIL" => "$userinfo[confirmemail]",
		"LANG-UPDATEEMAIL" => "$userinfo[updateemail]"));
		$page->output();
	break;
	case 'new_email_process':
		#set the columes needed for now.
		$columes = 'Email';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		#call board setting function.
		$colume = 'mx_check';
		$settings = board_settings($colume);
		#form values.
		$curemail = var_cleanup($_POST['curemail']);
		$conemail = var_cleanup($_POST['conemail']);
		$newemail = var_cleanup($_POST['newemail']);
		#email format variables.
		$cemailvalidate = valid_email($curemail);
		$nemailvalidate = valid_email($newemail);
		$vemailvalidate = valid_email($conemail);
		$email_mx_chk = validate_email_mx($newemail);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($curemail)){
			$errormsg = $userinfo['nocemail']."\n\n";
			$error = 1; 
		}
		if(empty($conemail)){
			$errormsg .= $userinfo['novemail']."\n\n";
			$error = 1; 
		}
		if(empty($newemail)){
			$errormsg .= $userinfo['nonemail']."\n\n";
			$error = 1; 
		}
		if(($settings['mx_check'] == 1) and ($email_mx_chk == false)){
			$errormsg .= $reg['mxfailed']."\n\n";
			$error = 1;
		}
		if(($cemailvalidate == 1) or ($nemailvalidate == 1) or ($vemailvalidate == 1)){
			$errormsg .= $reg['invalidemail']."\n\n";
			$error = 1;
		}
		if ($newemail !== $conemail){
			$errormsg .= $userinfo['nocemailmatch']."\n\n";
			$error = 1;
		}
		if((strlen($newemail) > 255) or (strlen($conemail) > 255) or (strlen($curemail) > 255)){
			$errormsg .= $reg['longemail']."\n\n";
			$error = 1;
		}
		if ($curemail !== $userpref['Email']){
			$errormsg .= $userinfo['noemailmatch']."\n\n";
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process query
			$db->run = "UPDATE ebb_users SET Email='$newemail' where Username='$logged_user'";
			$db->query();
			$db->close();
			//bring user back
			header("Location: Profile.php");
		}
	break;
	case 'new_password':
		$page = new template($template_path ."/editpassword.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITPASS" => "$userinfo[changepassword]",
		"LANG-LONGPWD" => "$reg[longpassword]",
		"LANG-NOCPWD" => "$userinfo[nocpwd]",
		"LANG-NONPWD" => "$userinfo[nonpwd]",
		"LANG-NOVPWD" => "$userinfo[novpwd]",
		"LANG-NOMATCH" => "$userinfo[nopassmatch]",
		"LANG-TEXT" => "$userinfo[passtxt]",
		"LANG-CURRPASS" => "$userinfo[currentpass]",
		"LANG-NEWPASS" => "$userinfo[newpass]",
		"LANG-CONFIRMPASS" => "$userinfo[connewpass]",
		"LANG-UPDATEPASS" => "$userinfo[updatepass]"));
		$page->output();
	break;
  case 'new_password_process':
		#set the columes needed for now.
		$columes = 'Username, Password';
		#call user function.
		$userpref = user_settings($logged_user, $columes);
		#form values.
		$curpass = var_cleanup($_POST['curpass']);
		$newpass = var_cleanup($_POST['newpass']);
		$confirmpass = var_cleanup($_POST['confirmpass']);
		$curpass_chk = md5($curpass.PWDSALT);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($curpass)){
			$errormsg = $userinfo['nocpwd']."\n\n";
			$error = 1; 
		}
		if(empty($newpass)){
			$errormsg .= $userinfo['nonpwd']."\n\n";
			$error = 1; 
		}
		if(empty($confirmpass)){
			$errormsg .= $userinfo['novpwd']."\n\n";
			$error = 1; 
		}
		if ($newpass !== $confirmpass){
			$errormsg .= $userinfo['nopassmatch']."\n\n";
			$error = 1;
		}
		if((strlen($newpass) > 12) or (strlen($confirmpass) > 12) or (strlen($curpass) > 12)){
			$errormsg .= $reg['longpassword']."\n\n";
			$error = 1;
		}
		if ($curpass_chk !== $userpref['Password']){
			$errormsg .= $userinfo['curpassnomatch']."\n\n";
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process query
			$passchange = md5($newpass.PWDSALT);
			$db->run = "UPDATE ebb_users SET Password='$passchange' where Username='$logged_user'";
			$db->query();
			$db->close();
			
			#call board setting function.
			$colume = 'cookie_path, cookie_domain, cookie_secure';
			$settings = board_settings($colume);
			
			//decide which login method to delete.
			if(isset($_COOKIE['ebbuser'])){
				//remove cookie.
				$currtime = time() - (2592000);
				setcookie("ebbuser", $userpref['Username'], $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
				setcookie("ebbpass", $userpref['Password'], $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
			}else{
				//clear session data.
				session_destroy();
			}

			//bring user back
			header("Location: index.php");
		}
	break;
	default:
		$user = (empty($_GET['user'])) ? $logged_user : var_cleanup($_GET['user']);
		#see if user is viewing a profile.
		if((empty($user)) and ($stat !== "guest")){
			$user = $logged_user;		
		}else{
			$db->run = "SELECT Status, Avatar, Username, Post_Count, Date_Joined, Time_Zone, Email, MSN, AOL, ICQ, Yahoo, WWW, Location, Hide_Email FROM ebb_users where Username='$user'";
			$userchk = $db->num_results();
			$db->close();
			//check to see if this user exist.
			if ($userchk == 0){
				$error = $userinfo['usernotexist'];
				echo error($error, "error");
			}		
		}	
		#set the columes needed for now.
		$columes = 'Status, Avatar, Username, Post_Count, Date_Joined, Time_Zone, Email, MSN, AOL, ICQ, Yahoo, WWW, Location, rssfeed1, rssfeed2';
		#call user function.
		$userpref = user_settings($user, $columes);
		//see if the user set an avatar
		if (empty($userpref['Avatar'])){
			$avatar = "images/noavatar.gif";
		}else{
			$avatar = $userpref['Avatar'];
		}
		//get status
		if ($userpref['Status'] == "groupmember"){
			//find what usergroup this user belongs to.
			$db->run = "SELECT gid FROM ebb_group_users where Username='$userpref[Username]'";
			$groupchk = $db->result();
			$db->close();
			//get the access level of this group.
			$db->run = "SELECT Name, Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
			$db->close();
			if($level_r['Level'] == 1){
				$rank = "<i><b>$level_r[Name]</b></i>";
			}
			if($level_r['Level'] == 2){
				$rank = "<b>$level_r[Name]</b>";
			}
			if($level_r['Level'] == 3){
				$rank = "<i>$level_r[Name]</i>";
			}
		}elseif($userpref['Status'] == "Banned"){
			$rank = "Banned";
		}else{
			$db->run = "SELECT Name FROM ebb_ranks WHERE Post_req <= $userpref[Post_Count] ORDER BY Post_req DESC";
			$rank2 = $db->result();
			$rank = "<i>$rank2[Name]</i>";
			$db->close();
		}
		$gmttime = gmdate ($time_format, $userpref['Date_Joined']);
		$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		
		#BEGIN Portal Data Gathering.
		
		#latest Topics.
		$LastTopics = latestTopics($userpref['Username']);
		$LastPosts = latestPosts($userpref['Username']);
		
		#END Portal Data Gathering.
		
		#see if user is the actual user, if so display their options, otherwise
		#display the basics options available to the viewing public.
		if($logged_user == $userpref['Username']){
			$uOptions = "<a href=\"Profile.php?mode=edit_profile\">$userinfo[editinfo]</a><br />
  			<a href=\"Profile.php?mode=edit_sig\">$userinfo[editsig]</a><br />
			<a href=\"Profile.php?mode=avatar\">$userinfo[avatarsetting]</a><br />
			<a href=\"Profile.php?mode=new_email\">$userinfo[emailupdate]</a><br />
			<a href=\"Profile.php?mode=new_password\">$userinfo[changepassword]</a><br />
			<a href=\"Profile.php?mode=groupmanager\">$userinfo[managegroups]</a><br />
			<a href=\"Profile.php?mode=attachments\">$post[manageattach]</a><br />
			<a href=\"Profile.php?mode=digest\">$userinfo[subscriptionsetting]</a>";
		}else{
			$uOptions = "<a href=\"Search.php?action=user_result&amp;search_type=topic&amp;poster=$userpref[Username]\">$userinfo[findtopics]</a><br />
			<a href=\"Search.php?action=user_result&amp;search_type=post&amp;poster=$userpref[Username]\">$userinfo[findposts]</a><br />                
            <a href=\"PM.php?action=write&amp;user=$userpref[Username]\">PM $userpref[Username]</a> ";
		}
		
		//output the html.
		$page = new template($template_path ."/profile.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"USERNAME" => "$userpref[Username]",
		"AVATAR" => "$avatar",
		"LANG-RANK" => "$userinfo[rank]",
		"RANK" => "$rank",
		"LANG-POSTCOUNT" => "$userinfo[postcount]",
		"POSTCOUNT" => "$userpref[Post_Count]",
		"LANG-EMAIL" => "$form[email]",
		"EMAIL" => "$userpref[Email]",
		"LANG-MSN" => "$form[msn]",
		"MSN" => "$userpref[MSN]",
		"LANG-AOL" => "$form[aol]",
		"AOL" => "$userpref[AOL]",
		"LANG-ICQ" => "$form[icq]",
		"ICQ" => "$userpref[ICQ]",
		"LANG-YAHOO" => "$form[yim]",
		"YAHOO" => "$userpref[Yahoo]",
		"LANG-WWW" => "$form[www]",
		"WWW" => "$userpref[WWW]",
		"LANG-LOCATION" => "$form[location]",
		"LOCATION" => "$userpref[Location]",
		"LANG-JOINED" => "$members[joindate]",
		"JOINED" => "$join_date",
		"LANG-LATEST-TOPICS" => "$userinfo[latesttopics]" ,
		"LATEST-TOPICS" => "$LastTopics",
		"LANG-LATEST-POSTS" => "$userinfo[latestreplies]",
		"LATEST-POSTS" => "$LastPosts",
		"FEED-TITLE1" => "$feedTitle1",
		"FEED-DESC1" => "$feedDesc1",
		"FEED-ITEM1" => "$feedItem1",
		"FEED-TITLE2" => "$feedTitle2",
		"FEED-DESC2" => "$feedDesc2",
		"FEED-ITEM2" => "$feedItem2",
		"LANG-OPTION" => "$userinfo[profilemenu]",
		"OPTION" => "$uOptions"));
		$page->output();
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
