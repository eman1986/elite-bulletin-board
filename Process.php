<?php
define('IN_EBB', true);
/*
Filename: Process.php
Last Modified: 07/11/2012

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
  "PAGETITLE" => "&nbsp;",
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
//check to see if this user is a registered or not.
if ($stat == "guest"){
	header ("Location: index.php");
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

#get page mode.
if(isset($_GET['mode'])){
	$mode = var_cleanup($_GET['mode']);
}else{
	$mode = ''; 
}
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	$error = $txt['nobid'];
	echo error($error, "error");
	}else{
	$bid = var_cleanup($_GET['bid']); 
}
#see if post form had a page number listed for refence(reply topics only).
if(isset($_POST['page'])){
	$pg = var_cleanup($_POST['page']);
}else{
	$pg = 1; 
}
#get board rules.
$db->run = "SELECT Post_Increment, type FROM ebb_boards WHERE id='$bid'";
$board_rules = $db->result();
$db->close();
#see if user is trying to post on a category-type board.
if($board_rules['type'] == 1){
	header("Location: index.php");
}
if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	#get group access information.
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
//get posting rules.
$db->run = "select B_Post, B_Important, B_Attachment, B_Reply, B_Poll from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
	switch ($mode){
		case 'New_Topic':
		//check for topic type rules.
		$post_type = var_cleanup($_POST['post_type']);
		$attach_rights = var_cleanup($_POST['attach_rights']);
		//see if user can post topics
		$post_topic = permission_check($board_rule['B_Post']);
		$permission_chk_post = access_vaildator($permission_type, 37);
		//see if user can post important topics
		$post_important = permission_check($board_rule['B_Important']);
		$permission_chk_important = access_vaildator($permission_type, 39);
		//see if user can add attachments to the topic
		$post_attachment = permission_check($board_rule['B_Attachment']);
		$permission_chk_attach = access_vaildator($permission_type, 26);
		#check permission.
		if(($permission_chk_post == 0) and ($checkgroup == 1)){
			$error = $post['nowrite'];
			echo error($error, "error");
		}elseif ($post_topic == 0){
			$error = $post['nowrite'];
			echo error($error, "error");
		}
		if(($permission_chk_important == 0) and ($checkgroup == 1) and ($post_type == 1)){
			$error = $process['noimportant'];
			echo error($error, "error");
		}elseif(($post_important == 0) and ($post_type == 1)){
			$error = $process['noimportant'];
			echo error($error, "error");
		}

		//get form values.
		$topic = var_cleanup($_POST['topic']);
		$post = var_cleanup($_POST['post']);
  		$no_smile = (isset($_POST['no_smile'])) ? 1 : 0;
		$no_bbcode = (isset($_POST['no_bbcode'])) ? 1 : 0;
		$subscribe = (isset($_POST['subscribe'])) ? 1 : 0;

		#set error values to default.
		$error = 0;
		$errormsg = '';

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
		//do some error checking
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
		if(strlen($post) < 10){
			$errormsg .= $process['shortbody']."\n\n";
			$error = 1;
		}
		//flood check.
		$flood = flood_check($logged_user, "posting");
		if ($flood == 1){
			$errormsg .= $process['flood']."\n\n";
			$error = 1;
		}
		
		//spam check.
		$topic_chk = language_filter($topic, 2);
		$post_chk = language_filter($post, 2);
		
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			//echo error($error, "error");
			echo $error;
		}else{
			//process request.
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();
			$db->run = "insert into ebb_topics (author, Topic, Body, Type, important, IP, Original_Date, bid, last_update, disable_smiles, disable_bbcode) values ('$logged_user', '$topic', '$post', 'Topic', '$post_type', '$ip', '$time', '$bid', '$time', '$no_smile', '$no_bbcode')";
			$db->query();
			$db->close();
			//get tid.
			$db->run = "SELECT tid FROM ebb_topics ORDER BY tid DESC limit 1";
			$tid_result = $db->result();
			$db->close();
			$tid = $tid_result['tid'];
			//update post link.
			$newlink = "bid=". $bid . "&amp;tid=". $tid;
			#update board & topic details.
			echo update_board($bid, $newlink, $logged_user);
			echo update_topic($tid, $newlink, $logged_user);
			//update user's last post.
			echo update_user($logged_user);
			#see if this board can allow post count increase.
			if($board_rules['Post_Increment'] == 1){
				//get current post count then add on to it.
				echo post_count($logged_user); 
			}else{
				#do not add to user's post count.
			}
				//check to see if the author wishes to recieve a email when a reply is added.
			if ($subscribe == 1){
				SubscriptionManager($tid, "subscribe");
			}
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
			}elseif(($post_attachment == 1) AND ($attach_rights == 1)){
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
		break;
		case 'New_Poll':
		//check for topic type rules.
		$post_type = var_cleanup($_POST['post_type']);
		$attach_rights = var_cleanup($_POST['attach_rights']);
		//see if user can post topics
		$post_topic = permission_check($board_rule['B_Post']);
		$permission_chk_post = access_vaildator($permission_type, 37);
		//see if user can post important topics
		$post_important = permission_check($board_rule['B_Important']);
		$permission_chk_important = access_vaildator($permission_type, 39);
		//see if user can add attachments to the topic
		$post_attachment = permission_check($board_rule['B_Attachment']);
		$permission_chk_attach = access_vaildator($permission_type, 26);
		#poll check.
		$post_poll = permission_check($board_rule['B_Poll']);
		$permission_chk_poll = access_vaildator($permission_type, 35);
		#check permission.
		if(($permission_chk_post == 0) and ($checkgroup == 1)){
			$error = $post['nowrite'];
			echo error($error, "error");
		}elseif ($post_topic == 0){
			$error = $post['nowrite'];
			echo error($error, "error");
		}
		if(($permission_chk_important == 0) and ($checkgroup == 1) and ($post_type == 1)){
			$error = $process['noimportant'];
			echo error($error, "error");
		}elseif(($post_important == 0) and ($post_type == 1)){
			$error = $process['noimportant'];
			echo error($error, "error");
		}
		if(($permission_chk_poll == 0) and ($checkgroup == 1)){
			$error = $post['nopoll'];
			echo error($error, "error");
		}elseif($post_poll == 0){
			$error = $post['nopoll'];
			echo error($error, "error");		
		}
		//get form values.
		$poll_topic = var_cleanup($_POST['topic']);
		$poll_post = var_cleanup($_POST['post']);
		$question = var_cleanup($_POST['question']);
		$poll_otp1 = var_cleanup($_POST['poll_otp1']);
		$poll_otp2 = var_cleanup($_POST['poll_otp2']);
		$poll_otp3 = var_cleanup($_POST['poll_otp3']);
		$poll_otp4 = var_cleanup($_POST['poll_otp4']);
		$poll_otp5 = var_cleanup($_POST['poll_otp5']);
		$poll_otp6 = var_cleanup($_POST['poll_otp6']);
		$poll_otp7 = var_cleanup($_POST['poll_otp7']);
		$poll_otp8 = var_cleanup($_POST['poll_otp8']);
		$poll_otp9 = var_cleanup($_POST['poll_otp9']);
		$poll_otp10 = var_cleanup($_POST['poll_otp10']);
		$no_smile = (isset($_POST['no_smile'])) ? 1 : 0;
		$no_bbcode = (isset($_POST['no_bbcode'])) ? 1 : 0;
		$subscribe = (isset($_POST['subscribe'])) ? 1 : 0;
		
		#set error values to default.
		$error = 0;
		$errormsg = '';

		//set the disable variables to 0 if not selected.
		if(empty($no_smile)){
			$no_smile = 0;
		}else{
			$no_smile = 1; 
		}
		if(empty($no_bbcode)){
			$no_bbcode = 0;
		}else{
			$no_bbcode = 0; 
		}
		//error check.
		if (empty($poll_topic)){
			$errormsg = $process['nosubject']."\n\n";
			$error = 1;
		}
		if (empty($poll_post)){
			$errormsg .= $process['nopost']."\n\n";
			$error = 1;
		}
		if (empty($question)){
			$errormsg .= $process['noquestion']."\n\n";
			$error = 1;
		}
		if ((empty($poll_otp1)) OR (empty($poll_otp2))){
			$errormsg .= $process['moreoption']."\n\n";
			$error = 1;
		}
		if(strlen($poll_topic) > 50){
			$errormsg .= $process['longsubject']."\n\n";
			$error = 1;
		}
		if (strlen($question) > 50){
			$errormsg .= $process['longquestion']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp1) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp2) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp3) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp4) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp5) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp6) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp7) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp8) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp9) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_otp10) > 50){
			$errormsg .= $process['longpoll']."\n\n";
			$error = 1;
		}
		if(strlen($poll_post) < 10){
			$errormsg .= $process['shortbody']."\n\n";
			$error = 1;
		}
		
		//flood check.
		$flood = flood_check($logged_user, "posting");
		if ($flood == 1){
			$errormsg .= $process['flood']."\n\n";
			$error = 1;
		}
		
		//spam check.
		$topic_chk = language_filter($poll_topic, 2);
		$post_chk = language_filter($poll_post, 2);
		
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process this
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();
			$db->run = "insert into ebb_topics (author, Topic, Body, Type, important, IP, Original_Date, last_update, bid, Question, disable_smiles, disable_bbcode) values ('$logged_user', '$poll_topic', '$poll_post', 'Poll', '$post_type', '$ip', '$time', '$time', '$bid', '$question', '$no_smile', '$no_bbcode')";
			$db->query();
			$db->close();
			//get tid.
			$db->run = "SELECT tid FROM ebb_topics ORDER BY tid DESC limit 1";
			$tid_result = $db->result();
			$db->close();
			$tid = $tid_result['tid'];
			//update post link.
			$newlink = "bid=". $bid . "&amp;tid=". $tid;
			#update board & topic details.
			echo update_board($bid, $newlink, $logged_user);
			echo update_topic($tid, $newlink, $logged_user);
			//update user's last post.
			echo update_user($logged_user);
			#see if board has disabled post count increments.
			if($board_rules['Post_Increment'] == 1){
				//get current post count then add on to it.
				echo post_count($logged_user); 
			}else{
				#do not add to user's post count.
			}
			//add poll options
			for($i=1;$i<=10;$i++){
				if (var_cleanup($_POST['poll_otp'.$i]) == ""){
					//do nothing at all.
				}else{
					$db->run = "INSERT INTO ebb_poll (Poll_Option, tid) values('".var_cleanup($_POST['poll_otp'.$i])."', '$tid')";
					$db->query();
					$db->close();
				}
			}
			//check to see if the author wishes to recieve a email when a reply is added.
			if ($subscribe == 1){
				SubscriptionManager($tid, "subscribe");
			}
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
			}elseif(($post_attachment == 1) AND ($attach_rights == 1)){
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
	break;
	case 'Reply':
		#see if Topic ID was declared, if not terminate any further outputting.
		if((!isset($_GET['tid'])) or (empty($_GET['tid']))){
			$error = $txt['notid'];
			echo error($error, "error");
		}else{
			$tid = var_cleanup($_GET['tid']); 
		}
		//see if user can post a reply.
		$post_topic = permission_check($board_rule['B_Reply']);
		$permission_chk_reply = access_vaildator($permission_type, 38);
		//see if user can add attachments to the topic
		$post_attachment = permission_check($board_rule['B_Attachment']);
		$permission_chk_attach = access_vaildator($permission_type, 26);
		#check permission.
		if(($permission_chk_reply == 0) and ($checkgroup == 1)){
			$error = $post['nowrite'];
			echo error($error, "error");	
		}elseif ($post_topic == 0){
			$error = $post['nowrite'];
			echo error($error, "error");
		}

		//get form values.
		$reply_post = var_cleanup($_POST['reply_post']);
		$no_smile = (isset($_POST['no_smile'])) ? 1 : 0;
		$no_bbcode = (isset($_POST['no_bbcode'])) ? 1 : 0;
		$subscribe = (isset($_POST['subscribe'])) ? 1 : 0;
		
		#set error values to default.
		$error = 0;
		$errormsg = '';

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
		//error check
		if (empty($reply_post)){
			$errormsg = $process['nopost']."\n\n";
			$error = 1;
		}
		if(strlen($reply_post) < 10){
			$errormsg .= $process['shortbody']."\n\n";
			$error = 1;
		}
		//flood check.
		$flood = flood_check($logged_user, "posting");
		if ($flood == 1){
			$errormsg .= $process['flood']."\n\n";
			$error = 1;
		}
		
		//spam check.
		$post_chk = language_filter($reply_post, 2);
		
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
			//process this
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();
			$db->run = "insert into ebb_posts (author, tid, bid, Body, IP, Original_Date, disable_smiles, disable_bbcode) values ('$logged_user', '$tid', '$bid', '$reply_post', '$ip', '$time', '$no_smile', '$no_bbcode')";
			$db->query();
			$db->close();
			//get pid.
			$db->run = "SELECT pid FROM ebb_posts ORDER BY pid DESC limit 1";
			$pid_result = $db->result();
			$db->close();
			$pid = $pid_result['pid'];
			//get reply number
			$db->run = "select pid from ebb_posts WHERE tid='$tid'";
			$reply_num = $db->num_results();
			$db->close();
			#call board setting function.
			$colume = 'per_page';
			$settings = board_settings($colume);
			
			$total_pages = $reply_num / $settings['per_page'];
			//update post link.
			if ($pg < $total_pages){
				$next = ($pg + 1);
				$newlink = "pg=". $next . "&amp;bid=". $bid . "&amp;tid=". $tid . "&amp;pid=". $pid . "#post". $pid;
				#link for header function.
				$redirect = "pg=". $next . "&bid=". $bid . "&tid=". $tid . "&pid=". $pid . "#post". $pid;
			}else{
				$newlink = "bid=". $bid . "&amp;tid=". $tid . "&amp;pid=". $pid . "#post". $pid;
				#link for header function.
				$redirect = "bid=". $bid . "&tid=". $tid . "&pid=". $pid . "#post". $pid;
			}
			#update board & topic details.
			echo update_board($bid, $newlink, $logged_user);
			echo update_topic($tid, $newlink, $logged_user);
			//update user's last post.
			echo update_user($logged_user);
			#see if board has disabled post count increments.
			if($board_rules['Post_Increment'] == 1){
				//get current post count then add on to it.
				echo post_count($logged_user); 
			}else{
				#do not add to user's post count.
			}
			//check to see if the author wishes to recieve a email when a reply is added.
			if ($subscribe == 1){
				SubscriptionManager($tid, "subscribe");
			}
			//update topic watch table to set post as new again (except for original subscriber).
			$db->run = "update ebb_topic_watch SET status='Unread' where tid='$tid' and username!='$logged_user'";
			$db->query();
			$db->close();
			
			//gather info for email.
			$db->run = "SELECT u.Email, u.Language, tw.username FROM ebb_topic_watch tw LEFT JOIN ebb_users u ON tw.username=u.Username WHERE tw.username!='$logged_user' AND tw.tid='$tid' AND tw.status='Unread'";
			$digest = $db->result();
			$digest_ct = $db->num_results();
			$notify = $db->query();
			$db->close();
			
			//see if we got any subscribers.
			if ($digest_ct > 0){
			
				//grab topic info.
				$db->run = "SELECT p.author, p.bid, p.tid, p.pid, t.Topic FROM ebb_posts p LEFT JOIN ebb_topics t ON t.tid=p.tid WHERE p.pid='$pid'";
				$topic = $db->result();
				$db->close();
			
				//set values for email.
				$digest_subject = "RE:".$topic['Topic'];
			
				require "lang/".$digest['Language'].".email.php";
				while ($row = mysql_fetch_array($notify)) {
					#call board setting function.
					$colume = 'mail_type';
					$settings = board_settings($colume);
					//send out email to users.
					$digest_message = digest();
					if($settings['mail_type'] == 0){
						#call smtp class file.
						require "includes/smtp.php";
						//call up the smtp class.
						$mailer = new ebbmail;
						$mailer->ebbmail();
						//setup the subject of this newsletter.
						$mailer->Subject = $digest_subject;
						//get body of newsletter.
						$mailer->Body = $digest_message;
						//add users to the email list
						$mailer->AddBCC($row['Email']);
						//send out the email.
						$mailer->Send();
						//clear the list to prevent any double emails.
						$mailer->ClearAllRecipients();
					}else{
						//create a From: mailheader
						$headers = "From: $title <$board_email>";
						set_time_limit(2500);
						@mail($row['Email'], $digest_subject, $digest_message, $headers);
					}
				}
			}

			if(($permission_chk_attach == 1) and ($checkgroup == 1)){
			 	#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and pid='0' and tid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set pid='$pid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?$redirect");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?$redirect");
				}			
			}elseif($post_attachment == 1){
			 	#see if user uploaded a file, if so lets assign the file to the topic.
				$db->run = "select id from ebb_attachments where Username='$logged_user' and pid='0' and tid='0'";
				$attach_ct = $db->num_results();
				$attach_id = $db->result();
				$db->close();
				if($attach_ct == 1){
					#add attachment to db for listing purpose.
					$db->run = "update ebb_attachments set pid='$pid' where id='$attach_id[id]'";
					$db->query();
					$db->close();
					//direct user to topic.
					header("Location: viewtopic.php?$redirect");
				}else{
					//direct user to topic.
					header("Location: viewtopic.php?$redirect");
				}
			}else{
				//direct user to topic.
				header("Location: viewtopic.php?$redirect"); 
			}		
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
