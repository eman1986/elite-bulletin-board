<?php
define('IN_EBB', true);
/*
Filename: delete.php
Last Modified: 12/6/2012

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
  "PAGETITLE" => "$delete[title]",
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
header("Location: index.php");
}
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
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
//check for the posting rule.
$db->run = "select B_Delete from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
if($stat == "Member"){
	$checkmod = 0;
	$checkgroup = 0;
}else{
	#get group access information.
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
switch ($action){
case 'delete_topic':
	//see if topic exist
	$db->run = "select tid from ebb_topics WHERE tid='$tid'";
	$checkboard = $db->num_results();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	#get author name to verify.
	$db->run = "SELECT author FROM ebb_topics Where tid='$tid'";
	$topic = $db->result();
	$db->close();
	//see if user has rights to do this.
	$del_chk = permission_check($board_rule['B_Delete']);
	$permission_chk = access_vaildator($permission_type, 21);
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
			$candel = 0;
		}else{
			if ($permission_chk == 1){
				$candel = 1;
			}else{
				$candel = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $topic['author']) AND ($permission_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}	
	}else{
		if (($logged_user == $topic['author']) AND ($del_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}
	}
	if($candel == 1){
		//delete topics.
		$db->run = "DELETE FROM ebb_topics WHERE tid='$tid'";
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
		//delete replies, if any.
		$db->run = "DELETE FROM ebb_posts WHERE tid='$tid'";
		$db->query();
		$db->close();
		//delete read status from topics made in this board.
		$db->run = "DELETE FROM ebb_read_topic WHERE Topic='$tid'";
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
			$db->run = "UPDATE ebb_boards SET last_update='', Posted_User='', Post_Link='' WHERE id='$bid'";
			$db->query();
			$db->close();
		}else{
			$db->run = "SELECT last_update, Posted_User, Post_Link FROM ebb_topics WHERE bid='$bid' ORDER BY last_update DESC LIMIT 1";
			$board_r = $db->result();
			$db->close();
			//update the last_update colume for ebb_boards.
			$db->run = "UPDATE ebb_boards SET last_update='$board_r[last_update]', Posted_User='$board_r[Posted_User]', Post_Link='$board_r[Post_Link]' WHERE id='$bid'";
			$db->query();
			$db->close();
		}
		#delete any attachments thats tied to this topic.
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
		//bring user back
		header("Location: index.php");
	}else{
		$error = $delete['denied'];
		echo error($error, "error");
	}
break;
case 'delete_post':
	//see if the post exist.
	$db->run = "select pid from ebb_posts WHERE pid='$pid'";
	$checkboard = $db->num_results();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	#get author's name to verify.
	$db->run = "SELECT author FROM ebb_posts Where pid='$pid'";
	$post = $db->result();
	$db->close();
	//see if user has rights to do this.
	$del_chk = permission_check($board_rule['B_Delete']);
	$permission_chk = access_vaildator($permission_type, 21);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$post[author]'";
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
			$candel = 0;
		}else{
			if ($permission_chk == 1){
				$candel = 1;
			}else{
				$candel = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $post['author']) AND ($permission_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}	
	}else{
		if (($logged_user == $post['author']) AND ($del_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}
	}
	if($candel == 1){
		//delete topics.
		$db->run = "DELETE FROM ebb_posts WHERE pid='$pid'";
		$db->query();
		$db->close();
		#delete any attachments thats tied to this topic.
		$db->run = "select Filename from ebb_attachments where pid='$pid'";
		$attach_r = $db->result();
		$attach_chk = $db->num_results();
		$db->close();
		if($attach_chk == 1){
			#delete file from web space.
			$delattach = unlink ('../uploads/'. $attach_r['Filename']);
			#delete entry from db.
			$db->run = "DELETE FROM ebb_attachments WHERE pid='$pid'";
			$db->query();
			$db->close();
		}
		//update last posted section.
		$db->run = "SELECT tid FROM ebb_posts WHERE tid='$tid'";
		$post_num = $db->num_results();
		$db->close();
		if($post_num == 0){
			$db->run = "SELECT bid, tid, Original_Date, author FROM ebb_topics WHERE tid='$tid'";
			$post_r = $db->result();
			$db->close();
			#create link to original post.
			$originalupdate = "bid=". $post_r['bid'] . "&tid=". $post_r['tid'];
			//update topic last_update.
			$db->run = "UPDATE ebb_topics SET last_update='$post_r[Original_Date]', Posted_User='$post_r[author]', Post_Link='$originalupdate' WHERE tid='$tid'";
			$db->query();
			$db->close();
			//update the last_update colume for ebb_boards.
			$db->run = "UPDATE ebb_boards SET last_update='$post_r[Original_Date]', Posted_User='$post_r[author]', Post_Link='$originalupdate' WHERE id='$bid'";
			$db->query();
			$db->close();
			//bring user back
			header("Location: viewtopic.php?$originalupdate");
		}else{
			$db->run = "SELECT pid, Original_Date, author FROM ebb_posts WHERE tid='$tid' ORDER BY Original_Date DESC LIMIT 1";
			$topic_r = $db->result();
			$db->close();
			//create new post link.
			$newlink = "bid=". $bid . "&tid=". $tid . "&pid=". $topic_r['pid'] . "#". $topic_r['pid'];
			//update the last_update colume for ebb_boards.
			$db->run = "UPDATE ebb_topics SET last_update='$topic_r[Original_Date]', Posted_User='$topic_r[author]', Post_Link='$newlink'  WHERE tid='$tid'";
			$db->query();
			$db->close();
			//update the last_update colume for ebb_boards.
			$db->run = "UPDATE ebb_boards SET last_update='$topic_r[Original_Date]', Posted_User='$topic_r[author]', Post_Link='$newlink' WHERE id='$bid'";
			$db->query();
			$db->close();
			//bring user back
			header("Location: viewtopic.php?$newlink");
		}
	}else{
		$error = $delete['denied'];
		echo error($error, "error");
	}
break;
case 'del_topic':
	//see if topic exist
	$db->run = "select tid from ebb_topics WHERE tid='$tid'";
	$checkboard = $db->num_results();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	#get author name to verify.
	$db->run = "SELECT author FROM ebb_topics Where tid='$tid'";
	$topic = $db->result();
	$db->close();
	//see if user has rights to do this.    
	$del_chk = permission_check($board_rule['B_Delete']);
	$permission_chk = access_vaildator($permission_type, 21);
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
			$candel = 0;
		}else{
			if ($permission_chk == 1){
				$candel = 1;
			}else{
				$candel = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $topic['author']) AND ($permission_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}	
	}else{
		if (($logged_user == $topic['author']) AND ($del_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}
	}
	if ($candel == 1){
		$page = new template($template_path ."/deletetopic.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-DELETETOPIC" => "$delete[title]",
		"LANG-DELSURE" => "$delete[topiccon]",
		"BID" => "$bid",
		"ID" => "$tid",
		"LANG-YES" => "$txt[yes]",
		"LANG-NO" => "$txt[no]"));
		$page->output();
	}else{
		$error = $delete['denied'];
		echo error($error, "error");
	}
break;
case 'del_post':
	//see if the post exist.
	$db->run = "select pid from ebb_posts WHERE pid='$pid'";
	$checkboard = $db->num_results();
	$db->close();
	if ($checkboard == 0){
		$error = $viewtopic['doesntexist'];
		echo error($error, "error");
	}
	#get authors name.
	$db->run = "SELECT author FROM ebb_posts Where pid='$pid'";
	$post = $db->result();
	$db->close();
	//see if user has rights to do this.
	$del_chk = permission_check($board_rule['B_Delete']);
	$permission_chk = access_vaildator($permission_type, 21);
	if($checkmod == 1){
		//find what usergroup this user belongs to(if any).
		$db->run = "SELECT gid FROM ebb_group_users where Username='$post[author]'";
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
			$candel = 0;
		}else{
			if ($permission_chk == 1){
				$candel = 1;
			}else{
				$candel = 0;
			}
		}	
	}elseif($checkgroup == 1){
		if (($logged_user == $post['author']) AND ($permission_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}	
	}else{
		if (($logged_user == $post['author']) AND ($del_chk == 1)){
			$candel = 1;
		}else{
			$candel = 0;
		}
	}
	if ($candel == 1){
		$page = new template($template_path ."/deletepost.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-DELETEPOST" => "$delete[delpost]",
		"LANG-DELSURE" => "$delete[postcon]",
		"BID" => "$bid",
		"TID" => "$tid",
		"ID" => "$pid",
		"LANG-YES" => "$txt[yes]",
		"LANG-NO" => "$txt[no]"));
		$page->output();
	}else{
		$error = $delete['denied'];
		echo error($error, "error");
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
