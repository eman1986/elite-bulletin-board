<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: topic_function.php
Last Modified: 10/29/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

/**
 * Check read status on a selected board.
 * @param integer $tid Topic ID to select a board.
 * @param string $user Username to check against.
 * @return integer 1, topic is read; 0, topic unread.
*/
function readTopicStat($tid, $user) {

    global $db;

    if ($user == "guest"){
        return 1;
    } else {
        try {
            $query = $db->prepare("SELECT t.tid
                                      FROM ebb_topics t
                                      LEFT JOIN ebb_read_topic rt ON t.tid=rt.Topic
                                      WHERE rt.User=:user AND t.tid=:tid");
            $query->execute(array(
                ":user" => $user,
                ":tid" => $tid));

            return $query->rowCount();
        }catch (PDOException $e) {
            echo $e->getMessage();
            return 1;
        }
    }
}

#board policy.
function board_policy(){

	global $db, $board_rule, $checkmod, $checkgroup, $viewtopic, $stat, $access_level;

	#read policy.
	if(($board_rule['B_Read'] == 1) AND ($access_level == 1)){
		$board_policy = $viewtopic['canread']. "<br />";
	}elseif(($board_rule['B_Read'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy = $viewtopic['canread']. "<br />";
	}elseif(($board_rule['B_Read']== 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy = $viewtopic['canread']. "<br />";
	}elseif($board_rule['B_Read'] == 0){
		$board_policy = $viewtopic['canread']. "<br />";
	}elseif(($board_rule['B_Read'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy = $viewtopic['canread']. "<br />";
	}else{
		$board_policy = $viewtopic['cantread']. "<br />";
	}
	#posting policy.
	if(($board_rule['B_Post'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canpost']. "<br />";
	}elseif(($board_rule['B_Post'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canpost']. "<br />";
	}elseif(($board_rule['B_Post'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canpost']. "<br />";
	}elseif($board_rule['B_Post'] == 4){
		$board_policy .= $viewtopic['cantpost']. "<br />";
	}elseif(($board_rule['B_Post'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canpost']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantpost']. "<br />";
	}
	#reply policy.
	if(($board_rule['B_Reply'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canreply']. "<br />";
	}elseif(($board_rule['B_Reply'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canreply']. "<br />";
	}elseif(($board_rule['B_Reply'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canreply']. "<br />";
	}elseif($board_rule['B_Reply'] == 4){
		$board_policy .= $viewtopic['cantreply']. "<br />";
	}elseif(($board_rule['B_Reply'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canreply']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantreply']. "<br />";
	}
	#voting policy.
	if(($board_rule['B_Vote'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canvote']. "<br />";
	}elseif(($board_rule['B_Vote'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canvote']. "<br />";
	}elseif(($board_rule['B_Vote'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canvote']. "<br />";
	}elseif($board_rule['B_Vote'] == 4){
		$board_policy .= $viewtopic['canvote']. "<br />";
	}elseif(($board_rule['B_Vote'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canvote']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantvote']. "<br />";
	}
	#poll policy.
	if(($board_rule['B_Poll'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canpoll']. "<br />";
	}elseif(($board_rule['B_Poll'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canpoll']. "<br />";
	}elseif(($board_rule['B_Poll'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canpoll']. "<br />";
	}elseif($board_rule['B_Poll'] == 4){
		$board_policy .= $viewtopic['cantpoll']. "<br />";
	}elseif(($board_rule['B_Poll'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canpoll']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantpoll']. "<br />";
	}
	#edit topic policy.
	if(($board_rule['B_Edit'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canedit']. "<br />";
	}elseif(($board_rule['B_Edit'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canedit']. "<br />";
	}elseif(($board_rule['B_Edit'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canedit']. "<br />";
	}elseif($board_rule['B_Edit'] == 4){
		$board_policy .= $viewtopic['cantedit']. "<br />";
	}elseif(($board_rule['B_Edit'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canedit']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantedit']. "<br />";
	}
	#delete topic policy.
	if(($board_rule['B_Delete'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['candelete']. "<br />";
	}elseif(($board_rule['B_Delete'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['candelete']. "<br />";
	}elseif(($board_rule['B_Delete'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['candelete']. "<br />";
	}elseif($board_rule['B_Delete'] == 4){
		$board_policy .= $viewtopic['cantdelete']. "<br />";
	}elseif(($board_rule['B_Delete'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['candelete']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantdelete']. "<br />";
	}
	#important topic policy.
	if(($board_rule['B_Important'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canimportant']. "<br />";
	}elseif(($board_rule['B_Important'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canimportant']. "<br />";
	}elseif(($board_rule['B_Important'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canimportant']. "<br />";
	}elseif($board_rule['B_Important'] == 4){
		$board_policy .= $viewtopic['cantimportant']. "<br />";
	}elseif(($board_rule['B_Important'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canimportant']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantimportant']. "<br />";
	}
	#attachment policy.
	if(($board_rule['B_Attachment'] == 1) AND ($access_level == 1)){
		$board_policy .= $viewtopic['canattach']. "<br />";
	}elseif(($board_rule['B_Attachment'] == 2) AND ($access_level == 1) or ($access_level == 2)){
		$board_policy .= $viewtopic['canattach']. "<br />";
	}elseif(($board_rule['B_Attachment'] == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
		$board_policy .= $viewtopic['canattach']. "<br />";
	}elseif(($board_rule['B_Attachment'] == 5) AND ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
		$board_policy .= $viewtopic['canattach']. "<br />";
	}elseif($board_rule['B_Attachment'] == 4){
		$board_policy .= $viewtopic['cantattach']. "<br />";
	}else{
		$board_policy .= $viewtopic['cantattach']. "<br />";
	}
	#see if this user is a moderator of this board.
	if($checkmod == 1){
		$board_policy .= $viewtopic['moderated'];
	}
	return ($board_policy);
}
#view poll function
function view_poll(){

	global $bid, $tid, $pollbox, $db, $template_path, $logged_user;

	$db->run = "SELECT Question FROM ebb_topics WHERE tid='$tid'";
	$question_result = $db->result();
	$db->close();
	//get poll options
	$db->run = "SELECT option_id, Poll_Option FROM ebb_poll WHERE tid='$tid'";
	$res = $db->query();
	$db->close();
	//output the poll form.
	$page = new template($template_path ."/pollbox-head.htm");
	$page->replace_tags(array(
	"BID" => "$bid",
	"TID" => "$tid",
	"QUESTION" => "$question_result[Question]"));
	$poll = $page->output();
	#get poll data.
	while ($i = mysql_fetch_assoc($res)){
		$page = new template($template_path ."/pollbox.htm");
		$page->replace_tags(array(
		"POLLID" => "$i[option_id]",
		"POLLOPTION" => "$i[Poll_Option]"));
		$poll .= $page->output();
	}
	#pollbox footer
	$page = new template($template_path ."/pollbox-foot.htm");
	$page->replace_tags(array(
	"USERNAME" => "$logged_user",
	"LANG-VOTE" => "$pollbox[vote]"));
	$poll .= $page->output();
	return ($poll);
}
#view poll results function
function view_results(){

	global $tid, $pollbox, $template_path, $db;

	//count how many votes were casted
	$db->run = "SELECT tid FROM ebb_votes WHERE tid='$tid'";
	$poll_count = $db->num_results();
	$db->close();
	//pick up the info on the poll.
	$db->run = "SELECT option_id, Poll_Option FROM ebb_poll WHERE tid='$tid'";
	$poll_questions = $db->query();
	$db->close();
	//pick-up question of this poll.
	$db->run = "SELECT Question FROM ebb_topics WHERE tid='$tid'";
	$question_result = $db->result();
	$db->close();
	//begin to output poll.
	$page = new template($template_path ."/pollresults-head.htm");
	$page->replace_tags(array(
	"QUESTION" => "$question_result[Question]"));
	$poll = $page->output();
	while ($i = mysql_fetch_assoc($poll_questions)){
		//grab results.
		$db->run = "SELECT tid FROM ebb_votes WHERE Vote='$i[option_id]' AND tid='$tid'";
		$poll_results = $db->num_results();
		$db->close();

		//see if the poll is currently empty.
		if($poll_results == 0){
			$VotePercent = 0;
		} else {
			//get percentage.
			$VotePercent = Round(($poll_results / $poll_count) * 100);
		}
		
		//output results.
		$page = new template($template_path ."/pollresults.htm");
		$page->replace_tags(array(
		"POLLOPTION" => "$i[Poll_Option]",
		"PERCENTAGE" => "$VotePercent"));
		$poll = $page->output();
	}
	$page = new template($template_path ."/pollresults-foot.htm");
	$page->replace_tags(array(
	"LANG-TOTAL" => "$pollbox[total]",
	"TOTAL" => "$poll_count"));
	$poll = $page->output();
	return ($poll);
}
