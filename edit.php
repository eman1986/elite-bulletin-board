<?php
define('IN_EBB', true);
/*
Filename: edit.php
Last Modified: 06/30/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$edit[edittopic]",
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
	//send user to index page.
	header("Location: index.php");
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
#see if Post ID was declared, if not leave it blank as we're assuming no replies are yet created.
if(!isset($_GET['pid'])){
	$pid = '';
}else{
	$pid = var_cleanup($_GET['pid']);
}

if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
//check for the posting rule.
$db->run = "select B_Edit, B_Important, B_Attachment from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
//get board rules.
$db->run = "SELECT Smiles, BBcode, Image FROM ebb_boards WHERE id='$bid'";
$post_rules_r = $db->result();
$db->close();
$allowsmile = $post_rules_r['Smiles'];
$allowbbcode = $post_rules_r['BBcode'];
$allowimg = $post_rules_r['Image'];
#get group access information.
if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
//see if spell check is enabled.
$colume = 'spell_checker';
$settings = board_settings($colume);
#see if user can use attachments.
$post_attachment = permission_check($board_rule['B_Attachment']);
$permission_chk_attach = access_vaildator($permission_type, 26);
switch ($mode){
case 'edit_topic':
	#sql to get authors name.
	$db->run = "select author from ebb_topics WHERE tid='$tid'";
	$topic = $db->result();
	$db->close();
	//check to see if this user is the author of this post.otherwise this action will be canceled.
	$edit_chk = permission_check($board_rule['B_Edit']);	
	$permission_chk = access_vaildator($permission_type, 20);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$topic[author]'";
		$groupchk = $db->result();
		$groupauth = $db->num_results();
		$db->close();
		if($groupauth == 1){
			//get the access level of this group.
			$db->run = "SELECT Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
		}
		#if author is an admin, mods can't edit the post.
		if(($access_level == 2) and ($level_r['Level'] == 1)){
			$canedit = 0;
		}else{
			if ($permission_chk == 1){
				$canedit = 1;
			}else{
				$canedit = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $topic['author']) AND ($permission_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}	
	}else{
		if (($logged_user == $topic['author']) AND ($edit_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}
	}
	#check to see if user can edit post.
	if ($canedit == 1){
		//get form values.
		$topic = var_cleanup($_POST['topic']);
		$post = var_cleanup($_POST['post']);
		$subscribe = var_cleanup($_POST['subscribe']);
		$no_smile = var_cleanup($_POST['no_smile']);
		$no_bbcode = var_cleanup($_POST['no_bbcode']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		//spam check.
		$topic_chk = language_filter($topic, 2);
		$post_chk = language_filter($post, 2);
		//do some error-checking.	
		if (empty($topic)){
			$errormsg = $process['nosubject']."\n\n";
			$error = 1;
		}
		if (empty($post)){
			$errormsg .= $process['nopost']."\n\n";
			$error = 1;
		}
		if(strlen($topic) > 50){
			$errormsg .= $process['longsubject']."\n\n";
			$error = 1;
		}
		//set the disable variables to 0 if not selected.
		if(empty($no_smile)){
			$no_smile = 0;
		}else{
			$no_smile = 1; 
		}
		if(empty($no_bbcode)){
			$no_bbcode = 0;
		}else{
			$no_bbcode = 1; 
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			if ($subscribe == "yes"){
				//add user to list
				SubscriptionManager($tid, "subscribe");
			} else {
				SubscriptionManager($tid, "unsubscribe");
			}
			//update the topic.
			$db->run = "UPDATE ebb_topics SET Topic='$topic', Body='$post', disable_smiles='$no_smile', disable_bbcode='$no_bbcode' WHERE tid='$tid'";
			$db->query();
			$db->close();
			if(($permission_chk_attach == 1) and ($checkgroup == 1)){
				#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set tid='$tid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}			
			}elseif($post_attachment == 1){
				#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set tid='$tid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}
			}else{
				//direct user to topic.
				header("Location: viewtopic.php?bid=$bid&tid=$tid"); 
			}
		}
	}else{
		$error = $edit['denied'];
		echo error($error, "error");
		}
break;
case 'edit_post':
	#sql to get authors name.
	$db->run = "select author from ebb_posts WHERE pid='$pid'";
	$post_r = $db->result();
	$db->close();
	//check to see if this user is the author of this post.otherwise this action will be canceled.
	$edit_chk = permission_check($board_rule['B_Edit']);	
	$permission_chk = access_vaildator($permission_type, 20);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$post_r[author]'";
		$groupchk = $db->result();
		$groupauth = $db->num_results();
		$db->close();
		if($groupauth == 1){
			//get the access level of this group.
			$db->run = "SELECT Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
		}
		#if author is an admin, mods can't edit the post.
		if(($access_level == 2) and ($level_r['Level'] == 1)){
			$canedit = 0;
		}else{
			if ($permission_chk == 1){
				$canedit = 1;
			}else{
				$canedit = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $post_r['author']) AND ($permission_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}	
	}else{
		if (($logged_user == $post_r['author']) AND ($edit_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}
	}
	#check to see if user can edit post.
	if ($canedit == 1){
		//get form values.
		$reply_post = var_cleanup($_POST['reply_post']);
		$subscribe = var_cleanup($_POST['subscribe']);
		$no_smile = var_cleanup($_POST['no_smile']);
		$no_bbcode = var_cleanup($_POST['no_bbcode']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		//spam check.
		$post_chk = language_filter($reply_post, 2);
		//error-checking.
		if (empty($reply_post)){
			$errormsg = $process['nopost']."\n\n";
			$error = 1;
		}
		//set the disable variables to 0 if not selected.
		if(empty($no_smile)){
			$no_smile = 0;
		}else{
			$no_smile = 1; 
		}
		if(empty($no_bbcode)){
			$no_bbcode = 0;
		}else{
			$no_bbcode = 1; 
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			if ($subscribe == "yes"){
				//add user to list
				SubscriptionManager($tid, "subscribe");
			} else {
				SubscriptionManager($tid, "unsubscribe");
			}
			//update post
			$db->run = "UPDATE ebb_posts SET Body='$reply_post', disable_smiles='$no_smile', disable_bbcode='$no_bbcode' WHERE pid='$pid'";
			$db->query();
			$db->close();
			if(($permission_chk_attach == 1) and ($checkgroup == 1)){
				#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set tid='$tid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}			
			}elseif($post_attachment == 1){
				#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set pid='$pid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?bid=$bid&tid=$tid");
				}
			}else{
				//direct user to topic.
				header("Location: viewtopic.php?bid=$bid&tid=$tid"); 
			}
		}
	}else{
		$error = $edit['denied'];
		echo error($error, "error");
	}
break;
case 'editpost':
	//see if topic exist
	$db->run = "select author, Body, disable_smiles, disable_bbcode from ebb_posts WHERE pid='$pid'";
	$checkboard = $db->num_results();
	$post_r = $db->result();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	//check to see if this user is the author of this post.otherwise this action will be canceled.
	$edit_chk = permission_check($board_rule['B_Edit']);	
	$permission_chk = access_vaildator($permission_type, 20);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$post_r[author]'";
		$groupchk = $db->result();
		$groupauth = $db->num_results();
		$db->close();
		if($groupauth == 1){
			//get the access level of this group.
			$db->run = "SELECT Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
		}
		#if author is an admin, mods can't edit the post.
		if(($access_level == 2) and ($level_r['Level'] == 1)){
			$canedit = 0;
		}else{
			if ($permission_chk == 1){
				$canedit = 1;
			}else{
				$canedit = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $post_r['author']) AND ($permission_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}	
	}else{
		if (($logged_user == $post_r['author']) AND ($edit_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}
	}
	#can user edit post?
	if ($canedit == 1){
		#post attachment option.
		if(($permission_chk_attach == 1) and ($checkgroup == 1)){
			$attachment_disable = '';
		}elseif($post_attachment == 1){
			$attachment_disable = '';
		}else{
			$attachment_disable = 'disabled=disabled';
		} 
		$bbcode = bbcode_form('body');
		$smile = form_smiles('body');
		//get subscription status.
		$db->run = "Select tid from ebb_topic_watch where username='$logged_user' and tid='$tid'";
		$check_subscription = $db->num_results();
		$db->close();
		//check for subscription status.
		if($check_subscription == 1){
			$post_type = "<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" checked=checked />$post[notify]";
		}else{
			$post_type = "<input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]";
		}
		//check for smile status.
		if($post_r['disable_smiles'] == 1){
			$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" checked=checked />$post[disablesmiles]";
		}else{
			$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]";
		}
		//check for bbcode status
		if($post_r['disable_bbcode'] == 1){
			$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" checked=checked />$post[disablebbcode]";
		}else{
			$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]";
		}
		if ($settings['spell_checker'] == 1){
			$page = new template($template_path ."/editpost-spell.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$edit[editpost]",
			"BID" => "$bid",
	  		"TID" => "$tid",
	  		"PID" => "$pid",
	  		"BBCODE" => "$bbcode",
	  		"LANG-NOBODY" => "$process[nopost]",
	  		"LANG-SMILES" => "$post[moresmiles]",
	  		"SMILES" => "$smile",
	  		"LANG-USERNAME" => "$txt[username]",
			"USERNAME" => "$logged_user",
	  		"BODY" => "$post_r[Body]",
	  		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
			"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
			"ATTACHMENTSTAT" => "$attachment_disable",
	  		"LANG-OPTIONS" => "$post[options]",
			"OPTIONS" => "$post_type",
	  		"LANG-EDITTOPIC" => "$edit[editpost]"));
			$page->output();
		}else{
			$page = new template($template_path ."/editpost.htm");
			$page->replace_tags(array(
	  		"TITLE" => "$title",
	  		"LANG-TITLE" => "$edit[editpost]",
	  		"BID" => "$bid",
	  		"TID" => "$tid",
	  		"PID" => "$pid",
	  		"BBCODE" => "$bbcode",
	  		"LANG-NOBODY" => "$process[nopost]",
	  		"LANG-SMILES" => "$post[moresmiles]",
	  		"SMILES" => "$smile",
	  		"LANG-USERNAME" => "$txt[username]",
	  		"USERNAME" => "$logged_user",
	  		"BODY" => "$post_r[Body]",
	  		"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
			"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
			"ATTACHMENTSTAT" => "$attachment_disable",
	  		"LANG-OPTIONS" => "$post[options]",
	  		"OPTIONS" => "$post_type",
	  		"LANG-EDITTOPIC" => "$edit[editpost]"));
			$page->output();
		}
	}else{
		$error = $edit['denied'];
		echo error($error, "error");
	}
break;
case 'edittopic';
	$db->run = "select author, Topic, Body, important, disable_bbcode, disable_smiles from ebb_topics WHERE tid='$tid'";
	$checkboard = $db->num_results();
	$topic = $db->result();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	//get subscription status.
	$db->run = "Select tid from ebb_topic_watch where username='$logged_user' and tid='$tid'";
	$check_subscription = $db->num_results();
	$db->close();
	//check to see if this user is the author of this post.otherwise this action will be canceled.
	$edit_chk = permission_check($board_rule['B_Edit']);	
	$permission_chk = access_vaildator($permission_type, 20);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$topic[author]'";
		$groupchk = $db->result();
		$groupauth = $db->num_results();
		$db->close();
		if($groupauth == 1){
			//get the access level of this group.
			$db->run = "SELECT Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
		}
		#if author is an admin, mods can't edit the post.
		if(($access_level == 2) and ($level_r['Level'] == 1)){
			$canedit = 0;
		}else{
			if ($permission_chk == 1){
				$canedit = 1;
			}else{
				$canedit = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $topic['author']) AND ($permission_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}	
	}else{
		if (($logged_user == $topic['author']) AND ($edit_chk == 1)){
			$canedit = 1;
		}else{
			$canedit = 0;
		}
	}
	#can user edit post?
	if ($canedit == 1){
		#post attachment option.
		if(($permission_chk_attach == 1) and ($checkgroup == 1)){
			$attachment_disable = '';
		}elseif($post_attachment == 1){
			$attachment_disable = '';
		}else{
			$attachment_disable = 'disabled=disabled';
		}
		$bbcode = bbcode_form('body');
		$smile = form_smiles('body');
		//see who can set important topics.
		$set_important = permission_check($board_rule['B_Important']);
		$permission_chk_important = access_vaildator($permission_type, 39);	
		//see current post type stats.
		if(($permission_chk_important == 1) and ($checkgroup == 1)){
			//check for topic type.
			if ($topic['important'] == 1){
				$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" class=\"text\" checked=checked />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" />$post[normal]";
			}else{
				$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" class=\"text\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]";
			}
			//check for subscription status.
			if($check_subscription == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" checked=checked />$post[notify]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]";
			}
			//check for smile status.
			if($topic['disable_smiles'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" checked=checked />$post[disablesmiles]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]";
			}
			//check for bbcode status
			if($topic['disable_bbcode'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" checked=checked />$post[disablebbcode]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]";
			}		
		}elseif ($set_important == 1){
			//check for topic type.
			if ($topic['important'] == 1){
				$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" class=\"text\" checked=checked />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" />$post[normal]";
			}else{
				$post_type = "$post[type]:&nbsp;<input type=\"radio\" name=\"post_type\" value=\"1\" class=\"text\" />$post[important] <input type=\"radio\" name=\"post_type\" value=\"0\" checked=checked />$post[normal]";
			}
			//check for subscription status.
			if($check_subscription == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" checked=checked />$post[notify]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]";
			}
			//check for smile status.
			if($topic['disable_smiles'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" checked=checked />$post[disablesmiles]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]";
			}
			//check for bbcode status
			if($topic['disable_bbcode'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" checked=checked />$post[disablebbcode]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]";
			}
		}else{
			//check for subscription status.
			if($check_subscription == 1){
				$post_type = "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" checked=checked />$post[notify]";
			}else{
				$post_type = "<br /><input type=\"checkbox\" name=\"subscribe\" value=\"yes\" class=\"text\" />$post[notify]";
			}
			//check for smile status.
			if($topic['disable_smiles'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" checked=checked />$post[disablesmiles]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_smile\" value=\"1\" class=\"text\" />$post[disablesmiles]";
			}
			//check for bbcode status
			if($topic['disable_bbcode'] == 1){
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" checked=checked />$post[disablebbcode]";
			}else{
				$post_type .= "<br /><input type=\"checkbox\" name=\"no_bbcode\" value=\"1\" class=\"text\" />$post[disablebbcode]";
			}
		}
		if ($settings['spell_checker'] == 1){
			$page = new template($template_path ."/edittopic-spell.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$edit[edittopic]",
			"BID" => "$bid",
			"TID" => "$tid",
			"BBCODE" => "$bbcode",
			"LANG-NOSUBJECT" => "$process[nosubject]",
 			"LANG-NOBODY" => "$process[nopost]",
  			"LANG-LONGSUBJECT" => "$process[longsubject]",
  			"LANG-SMILES" => "$post[moresmiles]",
  			"SMILES" => "$smile",
  			"LANG-USERNAME" => "$txt[username]",
  			"USERNAME" => "$logged_user",
  			"LANG-TOPIC" => "$post[topic]",
  			"TOPIC" => "$topic[Topic]",
  			"BODY" => "$topic[Body]",
  			"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
			"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
			"ATTACHMENTSTAT" => "$attachment_disable",
  			"LANG-OPTIONS" => "$post[options]",
  			"POST-TYPE" => "$post_type",
  			"LANG-EDITTOPIC" => "$edit[edittopic]"));
			$page->output();
		}else{
			$page = new template($template_path ."/edittopic.htm");
			$page->replace_tags(array(
 			"TITLE" => "$title",
  			"LANG-TITLE" => "$edit[edittopic]",
  			"BID" => "$bid",
  			"TID" => "$tid",
  			"BBCODE" => "$bbcode",
  			"LANG-NOSUBJECT" => "$process[nosubject]",
  			"LANG-NOBODY" => "$process[nopost]",
  			"LANG-LONGSUBJECT" => "$process[longsubject]",
  			"LANG-SMILES" => "$post[moresmiles]",
  			"SMILES" => "$smile",
  			"LANG-USERNAME" => "$txt[username]",
  			"USERNAME" => "$logged_user",
  			"LANG-TOPIC" => "$post[topic]",
  			"TOPIC" => "$topic[Topic]",
  			"BODY" => "$topic[Body]",
  			"LANG-ATTACHMENTUPLOAD" => "$post[uploadfile]",
			"LANG-ATTACHMENTMANAGER" => "$post[manageattach]",
			"ATTACHMENTSTAT" => "$attachment_disable",
  			"LANG-OPTIONS" => "$post[options]",
  			"POST-TYPE" => "$post_type",
  			"LANG-EDITTOPIC" => "$edit[edittopic]"));
			$page->output();
		}
	}else{
		$error = $edit['denied'];
		echo error($error, "error");
	}
break;
default:
	//user went here directly, aka incorrectly. direct them to the index page asap!
	header("Location: index.php");
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
