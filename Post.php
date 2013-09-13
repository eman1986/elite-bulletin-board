<?php
define('IN_EBB', true);
/*
Filename: Post.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
//determine the page title.
if ($mode == "New_Topic"){
	$postTitle = $post['newtopic'];
	$helpTitle = $help['attachtitle'];
	$helpBody = $help['attachbody'];
}elseif ($mode == "New_Poll"){
	$postTitle = $post['newpoll'];
	$helpTitle = $help['polltitle'];
	$helpBody = $help['pollbody'];
}elseif ($mode == "Reply"){
	$postTitle = $post['reply'];
	$helpTitle = $help['attachtitle'];
	$helpBody = $help['attachbody'];
}else{
	header("Location: index.php");
}

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
	"TITLE" => "$title",
	"PAGETITLE" => "$postTitle",
	"LANG-HELP-TITLE" => "$helpTitle",
	"LANG-HELP-BODY" => "$helpBody"));
$page->output();

//see if this is a guest trying to post.
if ($stat == "guest"){
	header ("Location: login.php");
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
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	$error = $txt['nobid'];
	echo error($error, "error");
}else{
	$bid = var_cleanup($_GET['bid']); 
}
#get board bbcode rules.
$db->run = "SELECT Smiles, BBcode, Image, type, Board FROM ebb_boards WHERE id='$bid'";
$post_rules_r = $db->result();
$db->close();
//get posting rules.
$db->run = "select B_Post, B_Important, B_Attachment, B_Reply, B_Poll from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
#see if user is trying to post on category-type board.
if($post_rules_r['type'] == 1){
	header("Location: index.php");
}
#see if user is a non-group member.
if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	#get group access information.
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
#call board setting function.
$colume = 'spell_checker';
$settings = board_settings($colume);
//set var to posting rules.
$allowsmile = $post_rules_r['Smiles'];
if($allowsmile == 1){
	$allowsmiles = $cp['on'];
}else{
	$allowsmiles = $cp['off'];
}
$allowbbcode = $post_rules_r['BBcode'];
if($allowbbcode == 1){
	$allow_bbcode = $cp['on'];
}else{
	$allow_bbcode = $cp['off'];
}
$allowimg = $post_rules_r['Image'];
if($allowimg == 1){
	$allow_img = $cp['on'];
}else{
	$allow_img = $cp['off'];
}
switch ( $mode ){
case 'New_Topic':
	//see if user can post.
	$post_chk = permission_check($board_rule['B_Post']);
	$permission_chk_post = access_vaildator($permission_type, 37);
	//see if user can post important topics
	$post_important = permission_check($board_rule['B_Important']);
	$permission_chk_important = access_vaildator($permission_type, 39);
	#see if user can add attachments to the topic
	$post_attachment = permission_check($board_rule['B_Attachment']);
	$permission_chk_attach = access_vaildator($permission_type, 26);
	#display error if user cant post.
	if(($permission_chk_post == 0) and ($checkgroup == 1)){
		$error = $post['nowrite'];
		echo error($error, "error");	
	}elseif ($post_chk == 0){
		$error = $post['nowrite'];
		echo error($error, "error");
	}
	#post attachment option.
	if(($permission_chk_attach == 1) and ($checkgroup == 1)){
		$attachment_disable = '';
		$can_attach = 1;
	}elseif($post_attachment == 1){
		$attachment_disable = '';
		$can_attach = 1;
	}else{
		$attachment_disable = 'disabled=disabled';
		$can_attach = 0;
	}

	//output form options
	if(($permission_chk_important == 1) and ($checkgroup == 1)){
		$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]<br />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}elseif($post_important == 1){
		$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]<br />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}else{
		$post_type = "<input type=\"hidden\" name=\"post_type\" value=\"0\" />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	if ($settings['spell_checker'] == 1){
		$page = new template($template_path ."/postnewtopic-spell.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[newtopic]",
		"LANG-NOSUBJECT" => "$process[nosubject]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-LONGSUBJECT" => "$process[longsubject]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"LANG-TOPIC" => "$post[topic]",
		"LANG-OPTIONS" => "$post[options]",
		"POST-TYPE" => "$post_type",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-POSTTOPIC" => "$post[posttopic]"));
		$page->output();
	}else{
		$page = new template($template_path ."/postnewtopic.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[newtopic]",
		"LANG-NOSUBJECT" => "$process[nosubject]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-LONGSUBJECT" => "$process[longsubject]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"LANG-TOPIC" => "$post[topic]",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"POST-TYPE" => "$post_type",
		"LANG-POSTTOPIC" => "$post[posttopic]"));
		$page->output();
	}
break;
case 'New_Poll':
	//see if user can post.
	$post_chk = permission_check($board_rule['B_Post']);
	$permission_chk_post = access_vaildator($permission_type, 37);
	//see if user can post important topics
	$post_important = permission_check($board_rule['B_Important']);
	$permission_chk_important = access_vaildator($permission_type, 39);
	#see if user can add attachments to the topic
	$post_attachment = permission_check($board_rule['B_Attachment']);
	$permission_chk_attach = access_vaildator($permission_type, 26);
	//see if this user can post a poll on this board.
	$poll_chk = permission_check($board_rule['B_Poll']);
	$permission_chk_poll = access_vaildator($permission_type, 35);
	//see if user can post.
	if(($permission_chk_post == 0) and ($checkgroup == 1)){
		$error = $post['nowrite'];
		echo error($error, "error");	
	}elseif ($post_chk == 0){
		$error = $post['nowrite'];
		echo error($error, "error");
	}
	//see if this user can post a poll on this board.
	if(($permission_chk_poll == 0) and ($checkgroup == 1)){
		$error = $post['nopoll'];
		echo error($error, "error");	
	}elseif ($poll_chk == 0){
		$error = $post['nopoll'];
		echo error($error, "error");
	}
	#post attachment option.
	if(($permission_chk_attach == 1) and ($checkgroup == 1)){
		$attachment_disable = '';
		$can_attach = 1;
	}elseif($post_attachment == 1){
		$attachment_disable = '';
		$can_attach = 1;
	}else{
		$attachment_disable = 'disabled=disabled';
		$can_attach = 0;
	}
	
	//output form options
	if(($permission_chk_important == 1) and ($checkgroup == 1)){
		$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]<br />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}elseif($post_important == 1){
		$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]<br />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}else{
		$post_type = "<input type=\"hidden\" name=\"post_type\" value=\"0\" />
		<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]<br />
		<input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]<br />
		<input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]
		<input type=\"hidden\" name=\"attach_rights\" value=\"$can_attach\" />";
	}
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	if ($settings['spell_checker'] == 1){
		$page = new template($template_path ."/postnewpoll-spell.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[newpoll]",
		"LANG-NOSUBJECT" => "$process[nosubject]",
		"LANG-LONGSUBJECT" => "$process[longsubject]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-NOQUESTION" => "$process[noquestion]",
		"LANG-LONGQUESTION" => "$process[longquestion]",
		"LANG-NOPOLL" => "$process[moreoption]",
		"LANG-LONGPOLL" => "$process[longpoll]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"LANG-TOPIC" => "$post[topic]",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"POST-TYPE" => "$post_type",
		"LANG-POLL" => "$post[polltext]",
		"LANG-QUESTION" => "$post[question]",
		"LANG-OPTION1" => "$post[pollopt1]",
		"LANG-OPTION2" => "$post[pollopt2]",
		"LANG-OPTION3" => "$post[pollopt3]",
		"LANG-OPTION4" => "$post[pollopt4]",
		"LANG-OPTION5" => "$post[pollopt5]",
		"LANG-OPTION6" => "$post[pollopt6]",
		"LANG-OPTION7" => "$post[pollopt7]",
		"LANG-OPTION8" => "$post[pollopt8]",
		"LANG-OPTION9" => "$post[pollopt9]",
		"LANG-OPTION10" => "$post[pollopt10]",
		"LANG-POSTTOPIC" => "$post[posttopic]"));
		$page->output();
	}else{
		$page = new template($template_path ."/postnewpoll.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[newpoll]",
		"LANG-NOSUBJECT" => "$process[nosubject]",
		"LANG-LONGSUBJECT" => "$process[longsubject]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-NOQUESTION" => "$process[noquestion]",
		"LANG-LONGQUESTION" => "$process[longquestion]",
		"LANG-NOPOLL" => "$process[moreoption]",
		"LANG-LONGPOLL" => "$process[longpoll]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"LANG-TOPIC" => "$post[topic]",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"POST-TYPE" => "$post_type",
		"LANG-POLL" => "$post[polltext]",
		"LANG-QUESTION" => "$post[question]",
		"LANG-OPTION1" => "$post[pollopt1]",
		"LANG-OPTION2" => "$post[pollopt2]",
		"LANG-OPTION3" => "$post[pollopt3]",
		"LANG-OPTION4" => "$post[pollopt4]",
		"LANG-OPTION5" => "$post[pollopt5]",
		"LANG-OPTION6" => "$post[pollopt6]",
		"LANG-OPTION7" => "$post[pollopt7]",
		"LANG-OPTION8" => "$post[pollopt8]",
		"LANG-OPTION9" => "$post[pollopt9]",
		"LANG-OPTION10" => "$post[pollopt10]",
		"LANG-POSTTOPIC" => "$post[posttopic]"));
		$page->output();
	}
break;
case 'Reply':
	#see if Topic ID was declared, if not terminate any further outputting.
	if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
		$error = $txt['notid'];
		echo error($error, "error");
	}else{
		$tid = var_cleanup($_GET['tid']); 
	}
	#see if Post ID was declared, if not leave it blank as we're assuming no replies are yet created.
	if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
		$pid = '';
	}else{
		$pid = var_cleanup($_GET['pid']);
	}
	#see if a page number is found, otherwise its just page 1.
	if(isset($_GET['pg'])){
		$pg = var_cleanup($_GET['pg']);
	}else{
		$pg = 1; 
	}
	//see if user can post.
	$post_chk = permission_check($board_rule['B_Reply']);
	$permission_chk_post = access_vaildator($permission_type, 38);
	#see if user can add attachments to the topic
	$post_attachment = permission_check($board_rule['B_Attachment']);
	$permission_chk_attach = access_vaildator($permission_type, 26);
	//see if user can post a reply.
	if(($permission_chk_post == 0) and ($checkgroup == 1)){
		$error = $post['nowrite'];
		echo error($error, "error");	
	}elseif ($post_chk == 0){
		$error = $post['nowrite'];
		echo error($error, "error");
	}
	#post attachment option.
	if(($permission_chk_attach == 1) and ($checkgroup == 1)){
		$attachment_disable = '';
	}elseif($post_attachment == 1){
		$attachment_disable = '';
	}else{
		$attachment_disable = 'disabled=disabled';
	}
	#see if user has quoted someone.
	if(isset($_GET['quser'])){
		$quser = var_cleanup($_GET['quser']);
		$type = var_cleanup($_GET['type']);
		if((empty($quser)) and (empty($type))){
			$quotetxt = ''; 
		}else{
			if($type == 1){ 
				#get topic post from requested topic.
				$db->run = "select Body from ebb_topics WHERE tid='$tid' and author='$quser'";
				$quote_r = $db->result();
				$db->close();
			}else{
			    #get topic post from requested topic.
				$db->run = "select Body from ebb_posts WHERE pid='$pid' and re_author='$quser'";
				$quote_r = $db->result();
				$db->close(); 
			}
			#setup quote for 
			$quotetxt = "[quote=$quser]$quote_r[Body][/quote]";
		}
	}else{
		$quotetxt = ''; 
	}
	//output form.
	$bbcode = bbcode_form('body');
	$smile = form_smiles('body');
	if ($settings['spell_checker'] == 1){
		$page = new template($template_path ."/postreply-spell.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$pm[reply]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"TID" => "$tid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"QUOTES" => "$quotetxt",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"NOTIFY" => "$post[notify]",
		"LANG-DISABLESMILES" => "$post[disablesmiles]",
		"LANG-DISABLEBBCODE" => "$post[disablebbcode]",
		"LANG-REPLY" => "$pm[reply]",
		"PAGE" => "$pg",
		"LANG-TOPICREVIEW" => "$post[topicreview]"));
		$page->output();
	}else{
		$page = new template($template_path ."/postreply.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$pm[reply]",
		"LANG-NOBODY" => "$process[nopost]",
		"LANG-ALLOWSMILES" => "$post[smiles]",
		"ALLOWSMILES" => "$allowsmiles",
		"LANG-ALLOWBBCODE" => "$post[bbcode]",
		"ALLOWBBCODE" => "$allow_bbcode",
		"LANG-ALLOWIMG" => "$post[img]",
		"ALLOWIMG" => "$allow_img",
		"BID" => "$bid",
		"TID" => "$tid",
		"LANG-POSTINGRULES" => "$post[postingrules]",
		"BBCODE" => "$bbcode",
		"LANG-POSTINGIN" => "$post[postin]",
		"POSTINGIN" => "$post_rules_r[Board]",
		"LANG-SMILES" => "$post[moresmiles]",
		"SMILES" => "$smile",
		"LANG-USERNAME" => "$txt[username]",
		"USERNAME" => "$logged_user",
		"QUOTES" => "$quotetxt",
		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
		"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
		"ATTACHMENTSTAT" => "$attachment_disable",
		"LANG-OPTIONS" => "$post[options]",
		"NOTIFY" => "$post[notify]",
		"LANG-DISABLESMILES" => "$post[disablesmiles]",
		"LANG-DISABLEBBCODE" => "$post[disablebbcode]",
		"LANG-REPLY" => "$pm[reply]",
		"PAGE" => "$pg",
		"LANG-TOPICREVIEW" => "$post[topicreview]"));
		$page->output();
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
