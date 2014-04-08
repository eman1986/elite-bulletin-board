<?php
define('IN_EBB', true);
/*
Filename: quickedit.php
Last Modified: 1/23/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

include "config.php";
require "header.php";

#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	exit($txt['nobid']);
}else{
	$bid = var_cleanup($_GET['bid']);
}
#see if Topic ID was declared, if not terminate any further outputting.
if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
	exit($txt['notid']);
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

$type = var_cleanup($_GET['type']);

//check for the posting rule.
$db->run = "select B_Edit from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();

if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	#get group access information.
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}

switch($mode){
case 'edit':
	//see what type of post this is.
	if($type == "topic"){	
		#see if request exist.
		$db->run = "select author from ebb_topics WHERE tid='$tid'";
		$checkboard = $db->num_results();
		$topic = $db->result();
		$db->close();
	
		if ($checkboard == 0){
			exit($viewtopic['doesntexist']);
		}
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
	    	#get information from quick form.
			$EditText = var_cleanup($_POST['value']);
	
			#error check.
			if(empty($EditText)){
				exit($process['nopost']);
			}else{
				//spam check.
				$post_chk = language_filter($EditText, 2);
				
				//update the topic.
				$db->run = "UPDATE ebb_topics SET Body='$EditText' WHERE tid='$tid'";
				$db->query();
				$db->close();
				#display text.
				
				echo nl2br(smiles(BBCode(language_filter($EditText, 1), true)));
			}	
		}else{
			exit($edit['denied']);
		}
	}elseif($type == "post"){
		//see if topic exist
		$db->run = "select author from ebb_posts WHERE pid='$pid'";
		$checkboard = $db->num_results();
		$post_r = $db->result();
		$db->close();
		if ($checkboard == 0){
			exit($viewtopic['doesntexist']);
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
	    	#get information from quick form.
			$EditText = var_cleanup($_POST['value']);
    	   	
			#error check.
			if(empty($EditText)){
				exit($process['nopost']);
			}else{
				//spam check.
				$post_chk = language_filter($EditText, 2);
				//update the topic.
				$db->run = "UPDATE ebb_posts SET Body='$EditText' WHERE pid='$pid'";
				$db->query();
				$db->close();
				#display text.
				echo nl2br(smiles(BBCode(language_filter($EditText, 1), true)));				
			}
		}
	}else{
		exit($edit['denied']);
	}
break;
default:
	//see what type of post this is.
	if($type == "topic"){
		$db->run = "select author, Body from ebb_topics WHERE tid='$tid'";
		$checkboard = $db->num_results();
		$topic = $db->result();
		$db->close();
	
		if ($checkboard == 0){
			exit($viewtopic['doesntexist']);
		}
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
		    echo html_entity_decode($topic['Body'], ENT_QUOTES);
		}else{
			exit($edit['denied']);
		}
	}elseif($type == "post"){
		//see if topic exist
		$db->run = "select author, Body from ebb_posts WHERE pid='$pid'";
		$checkboard = $db->num_results();
		$post_r = $db->result();
		$db->close();
		if ($checkboard == 0){
			exit($viewtopic['doesntexist']);
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
			echo html_entity_decode($post_r['Body'], ENT_QUOTES);
		}else{
			exit($edit['denied']);
		}
	}
}
?>
