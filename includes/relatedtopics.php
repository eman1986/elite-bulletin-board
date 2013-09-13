<?php
define('IN_EBB', true);
/*
Filename: relatedtopics.php
Last Modified: 7/21/2008

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "../config.php";
include "../header.php";

if ($stat == "guest"){
	echo $search['guesterror'];
	exit();
}else{

	//flood check.
	$flood = flood_check($logged_user, "search");
	if ($flood == 1){
		exit($search['flood']);
	}
	#keyword variable.
	$topic = var_cleanup($_POST['topic']);

	#if blank, simply do nothing.
	if(empty($topic)){
		exit();
	}	
	#update last_search colume.
	$time = time();
	$db->run = "update ebb_users SET last_search='$time' where Username='$logged_user'";
	$db->query();
	$db->close();	
	#Topic query
	$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE Topic LIKE '%$topic%' or Body LIKE '%$topic%' LIMIT 5";
	$search_result = $db->query();
	$count_t = $db->num_results();
	$db->close();
	#post query.
	$db->run = "SELECT bid, tid, pid FROM ebb_posts WHERE Body LIKE '%$topic%' LIMIT 5";
	$search_result2 = $db->query();
	$count_p = $db->num_results();
	$db->close();
	#see if anything in there, if so lets display it.
	if(($count_t == 0) AND ($count_p == 0)){
		echo $search['nosimilar'];
	}else{
		echo '<p>'.$search['relatedtopics'].':</p>';
		//output any topics
		while ($row = mysql_fetch_assoc($search_result)) {
		
			//check for the posting rule.
			$db->run = "select B_Read from ebb_board_access WHERE B_id='$row[bid]'";
			$board_rule = $db->result();
			$db->close();
			if(($stat == "guest") or ($stat == "Member")){
				#guest has no group rights.
				$checkgroup = 0;
				$checkmod = 0;
			}else{
				#get group access information.
				$checkgroup = group_validate($row['bid'], $level_result['id'], 2);
				$checkmod = group_validate($row['bid'], $level_result['id'], 1);
			}
			//see if the user can access this spot.
			$read_chk = permission_check($board_rule['B_Read']);
			if ($read_chk == 1){
				#search results data.
				echo "<a href=\"viewtopic.php?bid=$row[bid]&amp;tid=$row[tid]\">$row[Topic]</a> - $row[author]<br />";	
			}//end promission check.
		}//end loop.
		
		//output any posts
		while ($row2 = mysql_fetch_assoc($search_result2)){
			$db->run = "SELECT Topic FROM ebb_topics where tid='$row2[tid]'";
			$topic_r = $db->result();
			$db->close();
			//check for the posting rule.
			$db->run = "select B_Read from ebb_board_access WHERE B_id='$row2[bid]'";
			$board_rule = $db->result();
			$db->close();
			if(($stat == "guest") or ($stat == "Member")){
				#guest has no group rights.
				$checkgroup = 0;
				$checkmod = 0;
			}else{
				#get group access information.
				$checkgroup = group_validate($row2['bid'], $level_result['id'], 2);
				$checkmod = group_validate($row2['bid'], $level_result['id'], 1);
			}
			//see if the user can access this spot.
			$read_chk = permission_check($board_rule['B_Read']);
			if ($read_chk == 1){			
				#search results data.
				echo "<a href=\"viewtopic.php?bid=$row2[bid]&amp;tid=$row2[tid]&amp;pid=$row2[pid]\">RE: $topic_r[Topic]</a><br />";
			}//end promission check.				
		}//end loop.
	}//end flood check.
}//end guest check.
?>
