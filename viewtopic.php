<?php
define('IN_EBB', true);
/*
Filename: viewtopic.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
include "includes/topic_function.php";
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	die($txt['nobid']);
}else{
	$bid = var_cleanup($_GET['bid']); 
}
#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	die($txt['notid']);
}else{
	$tid = var_cleanup($_GET['tid']); 
}
#see if Post ID was declared, if not leave it blank as we're assuming no replies are yet created.
if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
	$pid = '';
}else{
	$pid = var_cleanup($_GET['pid']);
}
#get topic & board name.
$db->run = "select author, Topic, Body, Views, Locked, IP, Original_Date, Type, disable_smiles, disable_bbcode FROM ebb_topics WHERE tid='$tid'";
$checktopic = $db->num_results();
$t_name = $db->result();
$db->close();
$db->run = "select Board, Smiles, BBcode, Image FROM ebb_boards WHERE id='$bid'";
$checkboard = $db->num_results();
$b_name = $db->result();
$db->close();
#page title.
$pagetitle = $viewtopic['title']." - ".$t_name['Topic'];
$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$pagetitle",
  "LANG-HELP-TITLE" => "$help[nohelptitle]",
  "LANG-HELP-BODY" => "$help[nohelpbody]"));

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
//record user comming in here
$read_ct = read_topic_stat($tid, $logged_user);
if (($read_ct == 0) AND ($stat !== "guest")){
	$db->run = "insert into ebb_read_topic (Topic, User) values('$tid', '$logged_user')";
	$db->query();
	$db->close();
}
//check to see if topic exists or not and if it doesn't kill the program
if (($checkboard == 0) or ($checktopic == 0)){
	$error = $viewtopic['doesntexist'];
	echo error($error, "error");
}
//increment the total view of the topic by one(if user is NOT topic starter).
$addone = $t_name['Views'] + 1;
$db->run = "update ebb_topics SET Views='$addone' where tid='$tid' and author!='$logged_user'";
$db->query();
$db->close();
//update the status of the topic watch.
$db->run = "select status from ebb_topic_watch where username='$logged_user' and tid='$tid'";
$t_watch = $db->result();
$db->close();
if ($t_watch['status'] == "Unread"){
	$db->run = "update ebb_topic_watch SET status='Read' where username='$logged_user' and tid='$tid'";
	$db->query();
	$db->close();
}
//check for the posting rule.
$db->run = "select B_Read, B_Post, B_Reply, B_Poll, B_Vote, B_Edit, B_Delete, B_Attachment, B_Important from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
if(($stat == "guest") or ($stat == "Member")){
  #guest has no group rights.
	$checkgroup = 0;
	$checkmod = 0;
}else{
	#get group access information.
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
//see if the user can access this spot.
$read_chk = permission_check($board_rule['B_Read']);
if ($read_chk == 0){
	$error = $viewboard['noread'];
	echo error($error, "error");
}
//set some board vars.
$allowsmile = $b_name['Smiles'];
$allowbbcode = $b_name['BBcode'];
$allowimg = $b_name['Image'];
//begin pagenation
$count = 0;
$count2 = 0;
if(!isset($_GET['pg']) || empty($_GET['pg'])){
	$pg = 1;
}else{
	$pg = var_cleanup($_GET['pg']);
}
//check to see if the user can post a reply.
if ($stat == "guest"){
	$replylink = '';
	$permission_chk_reply = 0;
}else{
	#get reply permission.
	$reply_chk = permission_check($board_rule['B_Reply']);
	$permission_chk_reply = access_vaildator($permission_type, 38);	
	if ($reply_chk == 0){
		$replylink = "<img src=\"$template_path/images/locked.gif\" /><br /><br />";
	}elseif(($permission_chk_reply == 0) and ($checkgroup == 1)){
		$replylink = "<img src=\"$template_path/images/locked.gif\" /><br /><br />";	
	}else{
		if ($t_name['Locked'] == 0){
			$replylink = "<a href=\"Post.php?mode=Reply&amp;tid=$tid&amp;bid=$bid&amp;pg=$pg\"><img src=\"$template_path/images/reply.gif\" border=\"0\" alt=\"$viewtopic[replytopicalt]\" /></a><br /><br />";
		}else{
			$replylink = "<img src=\"$template_path/images/locked.gif\" alt=\"\" /><br /><br />";
		}
	}
}
#call board setting function.
$colume = 'per_page';
$settings = board_settings($colume);
// Figure out the limit for the query based on the current page number.
$from = (($pg * $settings['per_page']) - $settings['per_page']);
// Figure out the total number of results in DB:
$db->run = "select author, pid, tid, bid, Body, IP, Original_Date, disable_smiles, disable_bbcode from ebb_posts WHERE tid='$tid' LIMIT $from, $settings[per_page]";
$query = $db->query();
$db->close();

$db->run = "select pid from ebb_posts WHERE tid='$tid'";
$num = $db->num_results();
$db->close();
#output pagination.
$pagenation = pagination("bid=$bid&amp;tid=$tid&amp;");
#get author's profile data.
$db->run = "select Post_Count, Custom_Title, Status, Avatar, Sig from ebb_users WHERE Username='$t_name[author]'";
$user = $db->result();
$db->close();
$gmttime = gmdate ($time_format, $t_name['Original_Date']);
$topic_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
$total = $user['Post_Count'];
#get user custom title if one is made for them.
if(empty($user['Custom_Title'])){
	$customtitle = ''; 
}else{
	$customtitle = $user['Custom_Title']."<br />"; 
}
//rank star info.
if ($user['Status'] == "groupmember"){
	//find what usergroup this user belongs to.
	$db->run = "SELECT gid FROM ebb_group_users where Username='$t_name[author]'";
	$groupchk = $db->result();
	$db->close();
	//get the access level of this group.
	$db->run = "SELECT Name, Level FROM ebb_groups where id='$groupchk[gid]'";
	$level_r = $db->result();
	$db->close();
	#see what lever the user is.
	if($level_r['Level'] == 1){
		$rankicon = "<img src=\"$template_path/images/adminstar.gif\" alt=\"$level_r[Name]\" />";
		$rank = $level_r['Name'];
	}
	if($level_r['Level'] == 2){
		$rankicon = "<img src=\"$template_path/images/modstar.gif\" alt=\"$level_r[Name]\" />";
		$rank = $level_r['Name'];
	}
	if(($level_r['Level'] == 3)or ($user['Status'] == "Member")){
		#get ranks.
		$db->run = "SELECT Name, Star_Image FROM ebb_ranks WHERE Post_req <= $total ORDER BY Post_req DESC";
		$ranks = $db->result();
		$db->close();
		$rankicon = "<img src=\"$template_path/images/$ranks[Star_Image]\" alt=\"$ranks[Name]\" />";
		$rank = $level_r['Name'];
	}
}elseif($user['Status'] == "Banned"){
	$rankicon = "";
	$rank = "Banned";
}else{
	#get ranks.
	$db->run = "SELECT Name, Star_Image FROM ebb_ranks WHERE Post_req <= $total ORDER BY Post_req DESC";
	$ranks = $db->result();
	$db->close();
	$rankicon = "<img src=\"$template_path/images/$ranks[Star_Image]\" alt=\"$ranks[Name]\" />";
	$rank = $ranks['Name'];
}
//avatar and sig info.
if(empty($user['Avatar'])){
	$avatar = "images/noavatar.gif";
}else{
	$avatar = $user['Avatar'];
}
if(empty($user['Sig'])){
	$sig = '';
}else{
	$tsig = nl2br(smiles(BBCode(language_filter($user['Sig'], 1), true)));
	$sig = "_________________<br />$tsig";
}
//end info
$msg = $t_name['Body'];
//see if user wished to disable smiles.
if($t_name['disable_smiles'] == 0){
	if ($allowsmile == 1){
		$msg = smiles($msg);
	}
}
//see if user wished to disable bbcode
if($t_name['disable_bbcode'] == 0){
	if ($allowbbcode == 1){
		$msg = BBCode($msg);
	}
	if ($allowimg == 1){
		$msg = BBCode($msg, true);
	}
}
//censor convert.
$msg = language_filter($msg, 1);
$msg = nl2br($msg);
#get permission values.
$edit_chk = permission_check($board_rule['B_Edit']);
$delete_chk = permission_check($board_rule['B_Delete']);
$permission_chk_edit = access_vaildator($permission_type, 20);
$permission_chk_del = access_vaildator($permission_type, 21);
$permission_chk_vip = access_vaildator($permission_type, 24);
$permission_chk_move = access_vaildator($permission_type, 23);
$permission_chk_lock = access_vaildator($permission_type, 22);
$permission_chk_warn = access_vaildator($permission_type, 25);
$permission_chk_attach = access_vaildator($permission_type, 26);
$permission_chk_dwnld = access_vaildator($permission_type, 29);
#get user warning value.
$warn_bar = user_warn($t_name['author']);
#see if user is moderator.
if ($checkmod == 1){
	#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
	if(($access_level == 2) and ($level_r['Level'] == 1)){
		$menu = '';
		$quickEditStatus = "";
	}else{
		#see  is user can view IPs.
		if($permission_chk_vip == 1){
			$view_ip = "$viewtopic[ipmod]&nbsp;<a href=\"manage.php?mode=viewip&amp;ip=$t_name[IP]&amp;u=$t_name[author]&amp;tid=$tid&amp;bid=$bid\">$t_name[IP]</a>";
		}else{
			$view_ip = '';
		}
		#see if user can alter topic.
	    if(($permission_chk_edit == 1) and ($permission_chk_del == 1)){
			$menu = $view_ip."&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
		}elseif($permission_chk_edit == 1){
			$menu = $view_ip."&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a>&nbsp;<a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
		}elseif($permission_chk_del == 1){
			$menu = $view_ip."&nbsp;<a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a>&nbsp;<a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "";
		}else{
			$menu = '';
			$quickEditStatus = "";
		}
	}
}elseif($checkgroup == 1){
	#see if user is part of a group.
	if (($logged_user == $t_name['author']) AND ($permission_chk_edit == 1) AND ($permission_chk_del == 1)){
		#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
		if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
			$menu = '';
			$quickEditStatus = ""; 
		}else{
			$menu = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
		}
	}elseif (($logged_user == $t_name['author']) AND ($permission_chk_edit == 1)){
		#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
		if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
			$menu = '';
			$quickEditStatus = ""; 
		}else{
			$menu = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
		}
	}elseif (($logged_user == $t_name['author']) AND ($permission_chk_del == 1)){
		#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
		if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
			$menu = '';
			$quickEditStatus = ""; 
		}else{
			$menu = "$viewtopic[iplogged]&nbsp;<a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			$quickEditStatus = "";
			}
	}else{
		$menu = '';
		$quickEditStatus = "";
	}	
}else{
	#default user permission check.
	if (($logged_user == $t_name['author']) AND ($edit_chk == 1) AND ($delete_chk == 1)){	
		$menu = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
		$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
	}elseif (($logged_user == $t_name['author']) AND ($edit_chk == 1)){
		$menu = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=edittopic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
		$quickEditStatus = "<div align=\"right\"><a href=\"#\" onclick=\"editor.enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
	}elseif (($logged_user == $t_name['author']) AND ($delete_chk == 1)){
		$menu = "$viewtopic[iplogged]&nbsp;<a href=\"delete.php?action=del_topic&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
		$quickEditStatus = "";
	}else{
		$menu = '';
		$quickEditStatus = "";
	}
}
#see if topic is setup to include a poll.
if ($t_name['Type'] == "Poll"){
    $permission_chk_vote = access_vaildator($permission_type, 36);
	//check to see if a user already voted.
	$db->run = "SELECT tid FROM ebb_votes WHERE Username='$logged_user' AND tid='$tid'";
	$count = $db->num_results();
	$db->close();
	//see who can vote on the poll.
	$canvote = permission_check($board_rule['B_Vote']);
	//display results
	if (($count == 1) OR ($stat == "guest") or ($canvote == 0)){
		$poll = view_results();
	}elseif(($permission_chk_vote == 0) and ($checkgroup == 1)){
		$poll = view_results();
	}else{
		//display poll
		$poll = view_poll();
	}
}else{
	//no poll exists so lets just make this equal nothing.
	$poll = '';
}
#see if guests can download anything.
$colume = 'download_attachments';
$settings = board_settings($colume);
#see if guests can download content.
if(($permission_chk_dwnld == 0) and ($checkgroup == 1)){
	$attachment = '';
}elseif(($settings['download_attachments'] == 0) and ($stat == "guest")){
	$attachment = '';
}else{
	#list any attachments.
	$attachment = attachment_stat("topic", $t_name['author'], $tid);
}
//output viewtopic
$page = new template($template_path ."/viewtopic.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "LANG-TITLE" => "$b_name[Board]",
  "BID" => "$bid",
  "TID" => "$tid",
  "LANG-TOPIC" => "$t_name[Topic]",
  "REPLYLINK" => "$replylink",
  "PAGENATION" => "$pagenation",
  "MENU" => "$menu",
  "POLL" => "$poll",
  "LANG-PRINT" => "$viewtopic[ptitle]",
  "SUBJECT" => "$t_name[Topic]",
  "LANG-POSTED" => "$viewtopic[postedon]",
  "TOPIC-DATE" => "$topic_date",
  "AUTHOR" => "$t_name[author]",
  "CUSTOMTITLE" => "$customtitle",
  "RANK" => "$rank",
  "RANKICON" => "$rankicon",
  "AVATAR" => "$avatar",
  "LANG-POSTCOUNT" => "$index[posts]",
  "POSTCOUNT" => "$total",
  "WARNINGBAR" => "$warn_bar",
  "TOPIC" => "$msg",
  "QUICKEDIT" => "$quickEditStatus",
  "LANG-QUICKEDIT" => "$edit[edittopic]",
  "LANG-CANCELEDIT" => "$viewtopic[canceledit]",
  "LANG-PROCESSINGEDIT" => "$viewtopic[processingedit]",
  "ATTACHMENT" => "$attachment",
  "SIGNATURE" => "$sig"));
$page->output(); 
//show replys, if any.
$reply = reply_listing();

//admin & moderator options
if ($checkmod == 1){
	#see if user can delete topics.
	if($permission_chk_del == 1){
		$mod_delete = "<a href=\"manage.php?mode=delete&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/deletetopic.gif\" alt=\"\" border=\"0\" /></a>";
	}else{
		$mod_delete = '';
	}
	#see if user can move topics.
	if($permission_chk_move == 1){
		$mod_move = "<a href=\"manage.php?mode=move&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/move.gif\" alt=\"\" border=\"0\" /></a>";
	}else{
		$mod_move = '';
	}
	#see if user can lock/unlock a topic.
	if($permission_chk_lock == 1){
		if ($t_name['Locked'] == 1){
			$mod_lock = "<a href=\"manage.php?mode=unlock&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/unlock.gif\" alt=\"\" border=\"0\" /></a><br />";
		}else{
			$mod_lock = "<a href=\"manage.php?mode=lock&amp;bid=$bid&amp;tid=$tid\"><img src=\"$template_path/images/lock.gif\" alt=\"\" border=\"0\" /></a><br />";
		}
	}else{
		$mod_lock = '';
	}
	echo $mod_move."&nbsp;".$mod_delete."&nbsp;".$mod_lock."<br />";
}
//see if the user can post a reply in this topic.
$post_attachment = permission_check($board_rule['B_Attachment']);
if(($permission_chk_reply == 0) and ($checkgroup == 1)){
	//display nothing.
}elseif (($stat == "guest") OR ($t_name['Locked'] == 1) OR ($reply_chk == 0)){
	//display nothing.
}else{
	//bbcode buttons
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	//output it
	#call board setting function.
	$colume = 'spell_checker';
	$settings = board_settings($colume);
	#post attachment option.
	if(($permission_chk_attach == 1) and ($checkgroup == 1)){
		$attachment_disable = '';
	}elseif(($post_attachment == 1)){
		$attachment_disable = '';
	}else{
		$attachment_disable = 'disabled=disabled';
	}
	if ($settings['spell_checker'] == 1){
		$page = new template($template_path ."/instantreply-spell.htm");
		$page->replace_tags(array(
		"LANG-INSTANTREPLY" => "$viewtopic[instantreply]",
		"BID" => "$bid",
		"TID" => "$tid",
		"BBCODE" => "$bbcode",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"NOTIFY" => "$post[notify]",
		"LANG-DISABLESMILES" => "$post[disablesmiles]",
		"LANG-DISABLEBBCODE" => "$post[disablebbcode]",
		"LANG-REPLY" => "$pm[reply]",
		"PAGE" => "$pg"));
		$page->output();
	}else{
		$page = new template($template_path ."/instantreply.htm");
		$page->replace_tags(array(
		"LANG-INSTANTREPLY" => "$viewtopic[instantreply]",
		"BID" => "$bid",
		"TID" => "$tid",
		"BBCODE" => "$bbcode",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"NOTIFY" => "$post[notify]",

		"LANG-DISABLESMILES" => "$post[disablesmiles]",
		"LANG-DISABLEBBCODE" => "$post[disablebbcode]",
		"LANG-REPLY" => "$pm[reply]",
		"PAGE" => "$pg"));
		$page->output();
	}
}
//output the rules of this board.
echo '<div class="td2">'.board_policy().'</div>';
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
