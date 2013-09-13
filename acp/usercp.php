<?php
define('IN_EBB', true);
/*
Filename: usercp.php
Last Modified: 6/6/2013

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
case 'user_manage':
case 'user_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 14);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['manage'];
	$helpTitle = $help['usermanagetitle'];
	$helpBody = $help['usermanagebody'];
break;
case 'warnlog':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 18);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['warninglist'];
	$helpTitle = $help['warnlogtitle'];
	$helpBody = $help['warnlogbody'];
break;
case 'revoke':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 18);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['revokeaction'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'clearwarnlog':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 18);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['deletewarnlog'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'activate':
case 'activate_user':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 19);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['activateacct'];
	$helpTitle = $help['actusertitle'];
	$helpBody = $help['actuserbody'];
break;
case 'banlist':
case 'ban_add':
case 'ban_remove':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 13);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['banlist'];
	$helpTitle = $help['bantitle'];
	$helpBody = $help['banbody'];
break;
case 'blacklistuser':
case 'blacklist_add':
case 'blacklist_remove':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 16);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['blacklist'];
	$helpTitle = $help['blacklisttitle'];
	$helpBody = $help['blacklistbody'];
break;
case 'user_prune':
case 'process_user_pruning':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 15);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['userprune'];
	$helpTitle = $help['userprunetitle'];
	$helpBody = $help['userprunebody'];
break;
case 'ranks':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 17);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['ranks'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'add_rank':
case 'rank_add_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 17);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['addrank'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'rank_edit':
case 'rank_edit_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 17);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['editrank'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'rank_delete':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 17);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['delrank'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
default:
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 14);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$usercptitle = $cp['usermenu'].' - '.$cp['manage'];
	$helpTitle = $help['usermanagetitle'];
	$helpBody = $help['usermanagebody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$usercptitle",
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
	#total of new PM messages.
	$db->run = "select Read_Status from ebb_pm WHERE Reciever='$logged_user' and Read_Status=''";
	$new_pm = $db->num_results();
	$db->close();
	if($new_pm == 0){
		$pm_msg = $menu['nonewpm'];	
	}else{
		$pm_msg = $new_pm.$menu['newpm'];
	}
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
case 'user_manage':
	#see if username was given.
	if((!isset($_POST['username'])) or (empty($_POST['username']))){
		$error = $login['nouser'];
		echo acp_error($error, "error");
	}else{
		$user = var_cleanup($_POST['username']);
	}	
	$db->run = "SELECT Username, Email, Time_format, PM_Notify, Hide_Email, Status, active, Custom_Title, Style, Time_format, Language, Time_Zone, MSN, AOL, ICQ, Yahoo, WWW, Location, Sig, rssfeed1, rssfeed2, banfeeds FROM ebb_users where Username='$user' LIMIT 1";
	$userpref = $db->result();
	$user_chk = $db->num_results();
	$db->close();
	if($user_chk == 0){
		$error = $userinfo['usernotexist'];
		echo acp_error($error, "error");
	}else{
		#PM Notify Check
		if ($userpref['PM_Notify'] == 1){
			$pmnotice_status = "<input type=\"radio\" name=\"pm_notice\" value=\"1\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"pm_notice\" value=\"0\" class=\"text\" />$txt[no]";
		}else{
			$pmnotice_status = "<input type=\"radio\" name=\"pm_notice\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"pm_notice\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
		}
		#Email display Check
		if($userpref['Hide_Email'] == 0){
			$showemail_status = "<input type=\"radio\" name=\"show_email\" value=\"0\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"show_email\" value=\"1\" class=\"text\" />$txt[no]";
		}else{
			$showemail_status = "<input type=\"radio\" name=\"show_email\" value=\"0\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"show_email\" value=\"1\" class=\"text\" checked=checked />$txt[no]";
		}
		#banned status Check
		if($userpref['Status'] == "Banned"){
			$banuser_status = "<input type=\"checkbox\" name=\"banuser\" class=\"text\" value=\"yes\" checked=checked>$cp[tickban]";
		}else{
			$banuser_status = "<input type=\"checkbox\" name=\"banuser\" class=\"text\" value=\"yes\" />$cp[tickban]";
		}
		#active Check
		if($userpref['active'] == 1){
			$activeuser_status = "<input type=\"radio\" name=\"active_user\" value=\"1\" class=\"text\" checked=checked>$txt[yes] <input type=\"radio\" name=\"active_user\" value=\"0\" class=\"text\" />$txt[no]";
		}else{
			$activeuser_status = "<input type=\"radio\" name=\"active_user\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"active_user\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
		}
		#banfeeds
		if($userpref['banfeeds'] == 1){
			$banfeed_status = "<input type=\"radio\" name=\"banfeed\" value=\"1\" class=\"text\" checked=checked>$txt[yes] <input type=\"radio\" name=\"banfeed\" value=\"0\" class=\"text\" />$txt[no]";
		}else{
			$banfeed_status = "<input type=\"radio\" name=\"banfeed\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"banfeed\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
		}
		$timezone = timezone_select($userpref['Time_Zone']);
		$style = style_select($userpref['Style']);
		$language = acp_lang_select($userpref['Language']);		
		$page = new template("../". $template_path ."/cp-usermanage2.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-MANAGEUSER" => "$cp[manageuser]",
		"LANG-INVALIDEMAIL" => "$reg[invalidemail]",
		"LANG-LONGEMAIL" => "$reg[noemail]",
		"LANG-LONGMSN" => "$userinfo[longmsn]",
		"LANG-LONGAIM" => "$userinfo[longaol]",
		"LANG-LONGICQ" => "$userinfo[longicq]",
		"LANG-LONGYIM" => "$userinfo[longyim]",
		"LANG-LONGURL" => "$userinfo[longwww]",
		"LANG-INVALIDURL" => "$userinfo[invalidurl]",
		"LANG-NOTIMEFORM" => "$reg[notimeformat]",
		"LANG-LONGTIMEFORM" => "$cp[longtimeformat]",
		"LANG-LONGLOC" => "$userinfo[longloc]",
		"LANG-LONGSIG" => "$reg[longsig]",
		"LANG-LONGRSS" => "$userinfo[longrss]",
		"LANG-TEXT" => "$cp[usertext]",
		"LANG-EMAIL" => "$form[email]",
		"EMAIL" => "$userpref[Email]",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$userpref[Username]",
		"LANG-TIME" => "$form[timezone]",
		"TIME" => "$timezone",
		"LANG-TIMEFORMAT" => "$form[timeformat]",
		"LANG-TIMEINFO" => "$form[timeinfo]",
		"TIMEFORMAT" => "$time_format",
		"LANG-PMNOTIFY" => "$form[pm_notify]",
		"PMNOTIFY" => "$pmnotice_status",
		"LANG-SHOWEMAIL" => "$form[showemail]",
		"SHOWEMAIL" => "$showemail_status",
		"LANG-STYLE" => "$form[style]",
		"STYLE" => "$style",
		"LANG-LANGUAGE" => "$form[defaultlang]",
		"LANGUAGE" => "$language",
		"LANG-MSN" => "$form[msn]",
		"MSN" => "$userpref[MSN]",
		"LANG-AOL" => "$form[aol]",
		"AOL" => "$userpref[AOL]",
		"LANG-YIM" => "$form[yim]",
		"YIM" => "$userpref[Yahoo]",
		"LANG-ICQ" => "$form[icq]",
		"ICQ" => "$userpref[ICQ]",
		"LANG-SIG" => "$form[sig]",
		"SIG" => "$userpref[Sig]",
		"LANG-WWW" => "$form[www]",
		"WWW" => "$userpref[WWW]",
		"LANG-LOCATION" => "$form[location]",
		"LOCATION" => "$userpref[Location]",
		"LANG-RSSFEED1" => "$userinfo[rssfeed1]",
		"RSSFEED1" => "$userpref[rssfeed1]",
		"LANG-RSSFEED2" => "$userinfo[rssfeed2]",
		"RSSFEED2" => "$userpref[rssfeed2]",
		"LANG-ADMINTOOLS" => "$cp[admintools]",
		"LANG-ACTIVEUSER" => "$cp[activeuser]",
		"ACTIVEUSER" => "$activeuser_status",
		"LANG-BANUSER" => "$cp[banuser]",
		"BANUSER" => "$banuser_status",
		"LANG-DELUSER" => "$cp[deluser]",
		"LANG-TICKDELETE" => "$cp[tickdel]",
		 "LANG-RSSBAN" => "$cp[banrss]",
		 "RSSBAN" => "$banfeed_status",
		"SUBMIT" => "$cp[submit]"));
		$page->output();
	}
break;
case 'user_process':
	//get form details.
	$username = var_cleanup($_POST['user']);
	$email = var_cleanup($_POST['email']);
	$time_zone = var_cleanup($_POST['time_zone']);
	$time_format = var_cleanup($_POST['time_format']);
	$pm_notice = var_cleanup($_POST['pm_notice']);
	$show_email = var_cleanup($_POST['show_email']);
	$style = var_cleanup($_POST['style']);
	$default_lang = var_cleanup($_POST['default_lang']);
	$msn_messenger = var_cleanup($_POST['msn_messenger']);
	$aol_messenger = var_cleanup($_POST['aol_messenger']);
	$yim = var_cleanup($_POST['yim']);
	$icq = var_cleanup($_POST['icq']);
	$location = var_cleanup($_POST['location']);
	$sig = var_cleanup($_POST['sig']);
	$site = var_cleanup($_POST['site']);
	$rssfeed1 = var_cleanup($_POST['rssfeed1']);
	$rssfeed2 = var_cleanup($_POST['rssfeed2']);
	$active_user = var_cleanup($_POST['active_user']);
	$banfeed = var_cleanup($_POST['banfeed']);
	$banuser = var_cleanup($_POST['banuser']);
	$deluser = var_cleanup($_POST['deluser']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//do some error checking.
	if(empty($username)){
		$errormsg = $cp['nousernameentered']."\n\n";
		$error = 1;
	}
	if ($style == ""){
		$errormsg .= $reg['nostyle']."\n\n";
		$error = 1;
	}
	if ($default_lang == ""){
		$errormsg .= $reg['nolang']."\n\n";
		$error = 1;
	}
	if ($time_zone == ""){
		$errormsg .= $reg['notimezone']."\n\n";
		$error = 1;
	}
	if ($time_format == ""){
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
	if ($email == ""){
		$errormsg .= $reg['noemail']."\n\n";
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
	if ((!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $site)) and (!empty($site))) {
		$errormsg .= $userinfo['invalidurl']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//see if admin wants to ban user.
		if($banuser == "yes"){
			$db->run = "update ebb_users set Status='Banned' where Username='$username'";
			$db->query();
			$db->close();
			//remove any group access if the user belongs to any.
			$db->run = "delete from ebb_group_users where Username='$username'";
			$db->query();
			$db->close();
		}else{
			//set user as a Member.
			$db->run = "select Status from ebb_group_users where Username='$username' and Status='Banned'";
			$stat_chk = $db->num_results();
			$db->close();
			if($stat_chk == 1){
				$db->run = "update ebb_users set Status='Member' where Username='$username'";
				$db->query();
				$db->close();			
			}
		}
		//see if admin wants to delete user.
		if($deluser == "yes"){
			$db->run = "delete from ebb_users where Username='$username'";
			$db->query();
			$db->close();
			//get topic details to delete replies,then delete the topics.
			$db->run = "SELECT bid, tid FROM ebb_topics WHERE author='$username'";
			$board_query = $db->query();
			$db->close();
			#delete all replies made in the topics.
			while($row = mysql_fetch_assoc($board_query)){
				$db->run = "delete from ebb_posts where tid='$row[tid]'";
				$db->query();
				$db->close();
				//[TOPIC]update last posted section of boards.
				$db->run = "SELECT id FROM ebb_boards WHERE id='$row[bid]'";
				$board_num = $db->num_results();
				$db->close();
				if($board_num == 0){
					$db->run = "UPDATE ebb_boards SET last_update='', Posted_User='', Post_Link='' WHERE id='$row[bid]'";
					$db->query();
					$db->close();
				}else{
					$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE bid='$row[bid]' ORDER BY last_update DESC LIMIT 1";
					$board_r = $db->result();
					$db->close();
					//update the last_update colume for ebb_boards.
					$db->run = "UPDATE ebb_boards SET last_update='$board_r[last_update]', Posted_User='$board_r[Posted_User]', Post_Link='$board_r[Post_Link]' WHERE id='$bid'";
					$db->query();
					$db->close();
				}
				//[POST]update last posted section.
				$db->run = "SELECT tid FROM ebb_posts WHERE tid='$row[tid]'";
				$post_num = $db->num_results();
				$db->close();
				if($post_num == 0){
					$db->run = "SELECT bid, tid, Original_Date, author FROM ebb_topics WHERE tid='$row[tid]'";
					$post_r = $db->result();
					$db->close();
					#create link to original post.
					$originalupdate = "bid=". $post_r['bid'] . "&tid=". $post_r['tid'];
					//update topic last_update.
					$db->run = "UPDATE ebb_topics SET last_update='$post_r[Original_Date]', Posted_User='$post_r[author]', Post_Link='$originalupdate' WHERE tid='$row[tid]'";
					$db->query();
					$db->close();
				}else{
					$db->run = "SELECT pid, Original_Date, author FROM ebb_posts WHERE tid='$row[tid]' ORDER BY Original_Date DESC LIMIT 1";
					$topic_r = $db->result();
					$db->close();
					//create new post link.
					$newlink = "bid=". $row['bid'] . "&tid=". $row['tid'] . "&pid=". $topic_r['pid'] . "#". $topic_r['pid'];
					//update the last_update colume for ebb_boards.
					$db->run = "UPDATE ebb_topics SET last_update='$topic_r[Original_Date]', Posted_User='$topic_r[author]', Post_Link='$newlink'  WHERE tid='$row[tid]'";
					$db->query();
					$db->close();
				}
			}
			#delete any attachments thats tied to this user.
			$db->run = "SELECT Filename FROM ebb_attachments WHERE Username='$username'";
			$attachQ = $db->query();
			$attachChk = $db->num_results();
			$db->close();

			if($attachChk > 0){
			    while($delAttach = mysql_fetch_assoc($attachQ)){
					#delete file from web space.
					@unlink ('../uploads/'. $delAttach['Filename']);
				}
				#delete entry from db.
				$db->run = "DELETE FROM ebb_attachments WHERE Username='$username'";
				$db->query();
				$db->close();
			}
			#delete all topics made by this user.
			$db->run = "delete from ebb_topics where author='$username'";
			$db->query();
			$db->close();
			//delete replies made by user.
			$db->run = "delete from ebb_posts where author='$username'";
			$db->query();
			$db->close();
			//delete any subscriptions to topics this user belongs to..
			$db->run = "DELETE FROM ebb_topic_watch WHERE username='$username'";
			$db->query();
			$db->close();
			//delete polls made by topics in this board.
			$db->run = "DELETE FROM ebb_poll WHERE tid='$tid'";
			$db->query();
			$db->close();
			#delete any votes.
			$db->run = "DELETE FROM ebb_votes WHERE tid='$tid'";
			$db->query();
			$db->close();
		}
		//update user info.
		$db->run = "update ebb_users set Email='$email', MSN='$msn_messenger', AOL='$aol_messenger', Yahoo='$yim', ICQ='$icq', Location='$location', Sig='$sig', WWW='$site', Time_format='$time_format', Time_Zone='$time_zone', PM_Notify='$pm_notice', Hide_Email='$show_email', Style='$style', Language='$default_lang', rssfeed1='$rssfeed1', rssfeed2='$rssfeed2', banfeeds='$banfeed', active='$active_user' where Username='$username'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified User Profile: $username", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php");
	}
break;
case 'warnlog':
	$db->run = "select id, Username, Authorized, Action, Message from ebb_warnlog";
	$warn_log_ct = $db->num_results();
	$warn_log_q = $db->query();
	$db->close();
	#see if there are any entries currently.
	if($warn_log_ct == 0){
		$error = $cp['nowarnactions'];
		echo acp_error($error, "general");
	}else{
		$warn_log = warn_log();
	}
break;
case 'revoke':
	#make sure id was defined.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $cp['norid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['rid']);
	}
	#see if id exist.
	$db->run = "select id from ebb_warnlog where id='$id'";
	$warn_log_chk = $db->num_results();
	$db->close();
	if($warn_log_chk == 0){
		$error = $cp['invalidrid'];
		echo acp_error($error, "error");
	}else{
		$db->run = "select Action, Username from ebb_warnlog where id='$id'";
		$revoke_r = $db->result();
		$db->close();
		#get user's current warning level.
		$db->run = "SELECT warning_level FROM ebb_users WHERE Username='$revoke_r[Username]'";
		$warn_r = $db->result();
		$db->close();
		#see what will be revoked.
		if($revoke_r['Action'] == 1){
			$lower_warn = $warn_r['warning_level'] - 10;
			#update user's current warning level.
			$db->run = "update ebb_users set warning_level='$lower_warn' WHERE Username='$revoke_r[Username]'";
			$db->query();
			$db->close();
		}elseif($revoke_r['Action'] == 2){
			$raise_warn = $warn_r['warning_level'] + 10;
			#update user's current warning level.
			$db->run = "update ebb_users set warning_level='$raise_warn' WHERE Username='$revoke_r[Username]'";
			$db->query();
			$db->close();
		}elseif($revoke_r['Action'] == 3){
			#update user's current stat.
			$db->run = "update ebb_users set Status='Member' WHERE Username='$revoke_r[Username]'";
			$db->query();
			$db->close();
		}elseif($revoke_r['Action'] == 4){
			#remove suspension info from db.
			$db->run = "update ebb_users set suspend_length='0', suspend_time='' WHERE Username='$revoke_r[Username]'";
			$db->query();
			$db->close();
		}else{
			$error = $mod['actionblank'];
			echo acp_error($error, "error");
		}
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Revoked an action on $revoke_r[Username]", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php");
	}
break;
case 'clearwarnlog':
	#run sql query that will clear the warnlog db.
	$db->run = "TRUNCATE TABLE ebb_warnlog";
	$db->query();
	$db->close();
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("Cleared Warning Log", "$logged_user", "$acp_date", "$ip");
	//bring user back
	header("Location: usercp.php");
break;
case 'activate':
	//get list of inactive users.
	$db->run = "SELECT id, Username, Date_Joined FROM ebb_users WHERE active='0'";
	$inactive_q = $db->query();
	$user_ct = $db->num_results();
	$db->close();
	//output the html.
	$inactive_list = inactive_users();
break;
case 'activate_user';
	$stat = var_cleanup($_GET['stat']);
	$id = var_cleanup($_GET['id']);
	//load email language file.
	require "lang/".$lang.".email.php";
	#call board setting function.
	$colume = 'mail_type';
	$settings = board_settings($colume);
	if($stat == "accept"){
		//check for correct user id.
		$db->run = "select id from ebb_users where id='$id'";
		$acct_chk = $db->num_results();
		$db->close();
		if($acct_chk == 1){
			//set user as active.
			$db->run = "update ebb_users set active='1' where id='$id'";
			$db->query();
			$db->close();
			//get email of new user.
			$db->run = "select Email from ebb_users where id='$id'";
			$emlR = $db->result();
			$db->close();
			//display ok message.
			$error = $cp['useractivated'];
			echo acp_error($error, "general");
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Activated User", "$logged_user", "$acp_date", "$ip");
			#subject
			$acct_pass_subject = $cp['acceptsubject'];
			$acct_pass_msg = accept_user();
			//send out email to user.
			if($settings['mail_type'] == 0){
				#call smtp class file.
				require "../includes/smtp.php";
				//call up the smtp class.
				$mailer = new ebbmail;
				$mailer->ebbmail();
				//setup the subject of this newsletter.
				$mailer->Subject = $acct_pass_subject;
				//get body of newsletter.
				$mailer->Body = $acct_pass_msg;
				//add users to the email list
				$mailer->AddBCC($emlR['Email']);
				//send out the email.
				$mailer->Send();
				//clear the list to prevent any double emails.
				$mailer->ClearAllRecipients();
			}else{
				//create a From: mailheader
				$headers = "From: $title <$board_email>";
				@mail($emlR['Email'], $acct_pass_subject, $acct_pass_msg, $headers);
			}
		}else{
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Failed Activating User", "$logged_user", "$acp_date", "$ip");
			#output error.
			$error = $cp['useractivateerror'];
			echo acp_error($error, "error");
		}
	}else{
		//check for correct user id.
		$db->run = "select id from ebb_users where id='$id'";
		$acct_chk = $db->num_results();
		$db->close();
		if($acct_chk == 1){
			//set user as active.
			$db->run = "delete from ebb_users where id='$id'";
			$db->query();
			$db->close();
			//display ok message.
			$error = $cp['userdeny'];
			echo acp_error($error, "general");
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Denied User Activation", "$logged_user", "$acp_date", "$ip");
			#subject.
			$acct_fail_subject = $cp['denysubject'];
			$acct_fail_msg = deny_user();
			//send out email to user.
			if($settings['mail_type'] == 0){
				#call smtp class file.
				require "../includes/smtp.php";
				//call up the smtp class.
				$mailer = new ebbmail;
				$mailer->ebbmail();
				//setup the subject of this newsletter.
				$mailer->Subject = $acct_fail_subject;
				//get body of newsletter.
				$mailer->Body = $acct_fail_msg;
				//add users to the email list
				$mailer->AddBCC($emlR['Email']);
				//send out the email.
				$mailer->Send();
				//clear the list to prevent any double emails.
				$mailer->ClearAllRecipients();
			}else{
				//create a From: mailheader
				$headers = "From: $title <$board_email>";
				@mail($emlR['Email'], $acct_fail_subject, $acct_fail_msg, $headers);
			}
		}else{
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Failed Dening User Activation", "$logged_user", "$acp_date", "$ip");
			#output error.
			$error = $cp['useractivateerror'];
			echo acp_error($error, "error");
		}
	}
break;
case 'banlist':
	$admin_banlist_ip = admin_banlist_ip();
	$admin_banlist_email = admin_banlist_email();
	$page = new template("../". $template_path ."/cp-banlist.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-BANLIST" => "$cp[banlist]",
	"LANG-NOEMAIL" => "$cp[noemailban]",
	"LANG-LONGEMAIL" => "$cp[longemailban]",
	"LANG-NOMTYPE" => "$cp[nomatchtype]",
	"LANG-NOIP" => "$cp[noip]",
	"LANG-LONGIP" => "$cp[longipban]",
	"LANG-TEXT" => "$cp[banlisttext]",
	"LANG-EMAILBAN" => "$cp[emailban]",
	"LANG-BANEMAILTXT" => "$cp[emailbantxt]",
	"LANG-MATCHTYPETXT" => "$cp[matchtypetxt]",
	"LANG-EXACTMATCH" => "$cp[exactmatch]",
	"LANG-WILDCARDMATCH" => "$cp[wildcardmatch]",
	"LANG-UNBANEMAIL" => "$cp[emailunban]",
	"LANG-UNBANEMAILTXT" => "$cp[emailunbantxt]",
	"BANLIST-EMAIL" => "$admin_banlist_email",
	"LANG-BANIP" => "$cp[ipban]",
	"LANG-BANIPTXT" => "$cp[ipbantxt]",
	"LANG-UNBANIP" => "$cp[ipunban]",
	"LANG-UNBANIPTXT" => "$cp[ipunbantxt]",
	"BANLIST-IP" => "$admin_banlist_ip",
	"LANG-SUBMIT" => "$cp[submit]"));
	$page->output();
break;
case 'ban_add':
	//form values.
	$emailbanning = var_cleanup($_POST['emailbanning']);
	$ipbanning = var_cleanup($_POST['ipbanning']);
	$match_type = var_cleanup($_POST['match_type']);
	$type = var_cleanup($_POST['type']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error checking.
	if(($match_type == "") AND ($type == "Email")){
		$errormsg .= $cp['nomatchtype']."\n\n";
		$error = 1;
	}
	//if emailbanning is blank.
	if((empty($emailbanning)) AND ($type == "Email")){
		$errormsg .= $cp['noemailban']."\n\n";
		$error = 1;
	}
	if ((empty($ipbanning)) AND ($type == "IP")){
		$errormsg .= $cp['noip']."\n\n";
		$error = 1;
	}
	if(strlen($emailbanning) > 255){
		$errormsg .= $cp['longemailban']."\n\n";
		$error = 1;
	}
	if(strlen($ipbanning) > 15){
		$errormsg .= $cp['longipban']."\n\n";
		$error = 1;
	}
 	if ((!preg_match("/(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",$ipbanning)) AND ($type == "IP")) {
		$errormsg .= 'Invalid IP'."\n\n";
		$error = 1;
	}
	if($type == ""){
		$errormsg .= $cp['nobantype']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		if($type == "IP"){
			$db->run = "insert into ebb_banlist (ban_item, ban_type, match_type) values('$ipbanning', 'IP', 'Exact')";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Banned IP: $ipbanning", "$logged_user", "$acp_date", "$ip");
		}
		if($type == "Email"){
			$db->run = "insert into ebb_banlist (ban_item, ban_type, match_type) values('$emailbanning', 'Email', '$match_type')";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Banned E-mail Address: $emailbanning", "$logged_user", "$acp_date", "$ip");
		}
		//bring user back
		header("Location: usercp.php?action=banlist");
	}
break;
case 'ban_remove':
	//get form values
	$ipsel = var_cleanup($_POST['ipsel']);
	$emailsel = var_cleanup($_POST['emailsel']);
	$type = var_cleanup($_POST['type']);
	//error check.
	if($type == ""){
		$error = $cp['nobantype'];
		echo acp_error($error, "error");
	}
	if(($ipsel == "") and ($type == "IP") or ($emailsel == "") and ($type == "Email")){
		$error = $cp['noselectban'];
		echo acp_error($error, "validate");
	}else{
		//process query
		if($type == "IP"){
			$db->run = "DELETE FROM ebb_banlist WHERE id='$ipsel'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Removed IP From Banlist", "$logged_user", "$acp_date", "$ip");
		}
		if($type == "Email"){
			$db->run = "DELETE FROM ebb_banlist WHERE id='$emailsel'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Removed E-mail Address from Banlist", "$logged_user", "$acp_date", "$ip");
		}
		//bring user back
		header("Location: usercp.php?action=banlist");
	}
break;
case 'blacklistuser':
	$username_blacklist = admin_blacklist();
	$page = new template("../". $template_path ."/cp-blacklist.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-BLACKLISTEDUSERS" => "$cp[blacklist]",
	"LANG-NOUSER" => "$cp[nousernameentered]",
	"LANG-LONGUSER" => "$cp[longusername]",
	"LANG-NOMTYPE" => "$cp[nomatchtype]",
	"LANG-TEXT" => "$cp[usernameblacklisttxt]",
	"LANG-BLACKEDUSERNAME" => "$cp[blacklistusername]",
	"LANG-USERNAMETOBAN" => "$cp[blackedusername]",
	"LANG-BLACKLISTTYPE" => "$cp[blacklisttype]",
	"LANG-EXACTMATCH" => "$cp[exactmatch]",
	"LANG-WILDCARDMATCH" => "$cp[wildcardmatch]",
	"LANG-UNBLACKLISTUSER" => "$cp[whitelistingusername]",
	"LANG-UNBLACKLISTUSERTXT" => "$cp[whitelistingusernametxt]",
	"BLACKLIST" => "$username_blacklist",
	"LANG-SUBMIT" => "$cp[submit]"));
	$page->output();
break;
case 'blacklist_add':
	//get form data.
	$blacklistuser = var_cleanup($_POST['blacklistuser']);
	$match_type = var_cleanup($_POST['match_type']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error checking.
	if(empty($blacklistuser)){
		$errormsg = $cp['nousernameentered']."\n\n";
		$error = 1;
	}
	if($match_type == ""){
		$errormsg .= $cp['nomatchtype']."\n\n";
		$error = 1;
	}
	if(strlen($blacklistuser) > 25){
		$errormsg .= $cp['longusername']."\n\n";
		$error = 1; 
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_blacklist (blacklisted_username, match_type) values('$blacklistuser', '$match_type')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Blacklisted Username: $blacklistuser", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php?action=blacklistuser");
	}
break;
case 'blacklist_remove':
	//get form data.
	$blkusersel = var_cleanup($_POST['blkusersel']);
	#error check.
	if($blkusersel == ""){
		$error = $cp['nousernameselected'];
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "delete from ebb_blacklist where id='$blkusersel'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Removed Blacklisted Username from Banlist", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php?action=blacklistuser");
	}
break;
case 'user_prune':
	$page = new template("../". $template_path ."/cp-userprune.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-USERPRUNE" => "$cp[userprune]",
	"LANG-TEXT" => "$cp[userprunetext]",
	"LANG-PRUNEWARNING" => "$cp[userprunewarning]",
	"LANG-BEGINPRUNE" => "$cp[beginuserprune]"));
	$page->output();
break;
case 'process_user_pruning':
	$date_math = 3600*24*7;
	$time_eq = time() - $date_math;
	//process query
	$db->run = "DELETE FROM ebb_users WHERE Date_Joined>='$time_eq'";
	$db->query();
	$db->close();
	#log action in database.
	$acp_date = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	echo acp_log_add("Pruned Inactive Users from database", "$logged_user", "$acp_date", "$ip");
	//bring user back.
	header("Location: index.php");
break;
case 'ranks':
	$admin_rank = admin_ranklisting();
break;
case 'add_rank':
	$page = new template("../". $template_path ."/cp-newrank.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-ADDRANK" => "$cp[addrank]",
	"LANG-NORANKNAME" => "$cp[norankname]",
	"LANG-LONGRANKNAME" => "$cp[longrankname]",
	"LANG-NORANKSTAR" => "$cp[nostarfile]",
	"LANG-LONGRANKSTAR" => "$cp[longrankstar]",
	"LANG-NORANKRULE" => "$cp[nopostrule]",
	"LANG-LONGRANKRULE" => "$cp[longrankpost]",
	"LANG-RANKNAME" => "$cp[rankname]",
	"LANG-STAR" => "$cp[stars]",
	"LANG-POSTRULE" => "$cp[postrule]"));
	$page->output();
break;
case 'rank_add_process':
	$rank_name = var_cleanup($_POST['rank_name']);
	$rank_star = var_cleanup($_POST['rank_star']);
	$rank_post = var_cleanup($_POST['rank_post']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if ($rank_name == ""){
		$errormsg .= $cp['norankname']."\n\n";
		$error = 1;
	}
	if ($rank_star == ""){
		$errormsg .= $cp['nostarfile']."\n\n";
		$error = 1;
	}
	if ($rank_post == ""){
		$errormsg .= $cp['nopostrule']."\n\n";
		$error = 1;
	}
	if(strlen($rank_name) > 50){
		$errormsg .= $cp['longrankname']."\n\n";
		$error = 1;
	}
	if(strlen($rank_star) > 255){
		$errormsg .= $cp['longrankstar']."\n\n";
		$error = 1;
	}
	if(strlen($rank_post) > 4){
		$errormsg .= $cp['longrankpost']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "insert into ebb_ranks (Name, Star_Image, Post_req) values ('$rank_name', '$rank_star', '$rank_post')";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Added new rank: $rank_name", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php?action=ranks");
	}
break;
case 'rank_edit':
	#see if rank ID was provided.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['norankid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "Select Name, Star_Image, Post_req FROM ebb_ranks WHERE id='$id'";
	$edit_rank = $db->result();
	$rank_chk = $db->num_results();
	$db->close();
	#see if rank ID is valid.
	if($rank_chk == 0){
		$error = $cp['ranknotexist'];
		echo acp_error($error, "error");
	}else{
		$page = new template("../". $template_path ."/cp-modifyrank.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-MODIFYRANK" => "$cp[editrank]",
		"LANG-NORANKNAME" => "$cp[norankname]",
		"LANG-LONGRANKNAME" => "$cp[longrankname]",
		"LANG-NORANKSTAR" => "$cp[nostarfile]",
		"LANG-LONGRANKSTAR" => "$cp[longrankstar]",
		"LANG-NORANKRULE" => "$cp[nopostrule]",
		"LANG-LONGRANKRULE" => "$cp[longrankpost]",
		"ID" => "$id",
		"LANG-RANKNAME" => "$cp[rankname]",
		"RANKNAME" => "$edit_rank[Name]",
		"LANG-STAR" => "$cp[stars]",
		"STARIMAGE" => "$edit_rank[Star_Image]",
		"LANG-POSTRULE" => "$cp[postrule]",
		"POSTRULE" => "$edit_rank[Post_req]"));
		$page->output();
	}
break;
case 'rank_edit_process':
	#see if rank ID was provided.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['norankid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "Select id FROM ebb_ranks WHERE id='$id'";
	$rank_chk = $db->num_results();
	$db->close();
	#see if rank ID is valid.
	if($rank_chk == 0){
		$error = $cp['ranknotexist'];
		echo acp_error($error, "error");
	}else{
		$modify_rank_name = var_cleanup($_POST['rank_name']);
		$modify_rank_star = var_cleanup($_POST['rank_star']);
		$modify_post = var_cleanup($_POST['rank_post']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if ($modify_rank_name == ""){
			$errormsg .= $cp['norankname'];
			$error = 1;
		}
		if ($modify_rank_star == ""){
			$errormsg .= $cp['nostarfile'];
			$error = 1;
		}
		if ($modify_post == ""){
			$errormsg .= $cp['nopostrule'];
			$error = 1;
		}
		if(strlen($modify_rank_name) > 50){
			$errormsg .= $cp['longrankname'];
			$error = 1;
		}
		if(strlen($modify_rank_star) > 60){
			$errormsg .= $cp['longrankstar'];
			$error = 1;
		}
		if(strlen($modify_post) > 5){
			$errormsg .= $cp['longrankpost'];
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//process query
			$db->run = "UPDATE ebb_ranks SET Name='$modify_rank_name', Star_Image='$modify_rank_star', Post_req='$modify_post' WHERE id='$id'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Modified rank: $modify_rank_name", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: usercp.php?action=ranks");
		}
	}
break;
case 'rank_delete':
	#see if rank ID was provided.
	if((!isset($_GET['id'])) or (empty($_GET['id']))){
		$error = $txt['norankid'];
		echo acp_error($error, "error");
	}else{
		$id = var_cleanup($_GET['id']);
	}
	$db->run = "Select id FROM ebb_ranks WHERE id='$id'";
	$rank_chk = $db->num_results();
	$db->close();
	#see if rank ID is valid.
	if($rank_chk == 0){
		$error = $cp['ranknotexist'];
		echo acp_error($error, "error");
	}else{
		//process query
		$db->run = "DELETE FROM ebb_ranks WHERE id='$id'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Deleted rank", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: usercp.php?action=ranks");
	}
break;
default:
	$page = new template("../". $template_path ."/cp-usermanage.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-SELECTUSER" => "$cp[seluser]",
	"LANG-NOUSER" => "$cp[nousernameentered]",
	"LANG-LONGUSER" => "$cp[longusername]",
	"LANG-TEXT" => "$cp[usertxt]",
	"LANG-USERNAME" => "$txt[username]"));
	$page->output();
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
