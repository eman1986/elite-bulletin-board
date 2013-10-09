<?php
define('IN_EBB', true);
/*
Filename: vote.php
Last Modified: 10/27/2007

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
include "header.php";
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
#see if user added a poll option, if not terminate any further outputting.
if((!isset($_POST['vote'])) or (empty($_POST['vote']))){
	die($pollbox['novote']);
}else{
	$vote = var_cleanup($_POST['vote']);
}
#get group access information.
if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
//get posting rules.
$db->run = "select B_Vote from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
//see who can vote on the poll.
$canvote = permission_check($board_rule['B_Vote']);
$permission_chk_vote = access_vaildator($permission_type, 36);
if ($canvote == 0){
	die($viewtopic['cantvote']);
}elseif(($permission_chk_vote == 0) and ($checkgroup == 1)){
	die($viewtopic['cantvote']);
}else{
	//perform query.
	$db->run = "insert into ebb_votes (Username, tid, Vote) values ('$logged_user', '$tid', '$vote')";
	$db->query();
	$db->close();
	//direct user back to topic.
	header ("Location: viewtopic.php?bid=$bid&tid=$tid");
}
ob_end_flush();
?>
