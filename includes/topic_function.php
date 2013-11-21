<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: topic_function.php
Last Modified: 11/21/2013

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
            $query = $db->prepare("SELECT count(t.tid)
                                      FROM ebb_topics t
                                      LEFT JOIN ebb_read_topic rt ON t.tid=rt.Topic
                                      WHERE rt.User=:user AND t.tid=:tid");
            $query->execute(array(
                ":user" => $user,
                ":tid" => $tid));

            return $query->fetchColumn();
        }catch (PDOException $e) {
            echo $e->getMessage();
            return 1;
        }
    }
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
