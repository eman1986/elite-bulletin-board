<?php
define('IN_EBB', true);
/*
Filename: rssdeed.php
Last Modified: 1/23/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
#see if board id was defined.
if((isset($_GET['bid'])) or (!empty($_GET['bid']))){
	$bid = var_cleanup($_GET['bid']);
}else{
	$bid = ''; 
}

#see if board exists
$db->run = "select Board, type from ebb_boards WHERE id='$bid'";
$checkboard = $db->num_results();
$board_r = $db->result();
$db->close();

//see if board existsa dn is not a category-board.
if(($checkboard == 1) and ($board_r['type'] != 1)){

	#sql to get latest topic/post.
	$db->run = "SELECT t.tid, t.bid, p.pid, t.Topic, t.Body AS TBODY, p.Body AS PBODY, t.Original_Date AS TDATE, p.Original_Date AS PDATE
				FROM ebb_topics t
				LEFT JOIN ebb_posts p ON p.tid=t.tid
				WHERE t.bid='$bid'
				ORDER BY TDATE DESC, PDATE DESC
				LIMIT 20";
	$rss_query = $db->query();
	$db->close();

	#set headers to make it an xml file.
	header("Content-type: text/xml");
 	echo '<?xml version="1.0" encoding="UTF-8" ?>';
 	echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
  	echo '<channel>
	<title>'.$title.'</title>
	<description>'.$board_r['Board'].'</description>
	<link>'.$board_address.'</link>';
	
	
	#get latest topic/post.
	while($rss = mysql_fetch_array($rss_query)) {

		//check for the posting rule.
		$db->run = "select B_Read from ebb_board_access WHERE B_id='$rss[bid]'";
		$board_rule = $db->result();
		$db->close();
		if(($stat == "guest") or ($stat == "Member")){
		  #guest has no group rights.
			$checkgroup = 0;
			$checkmod = 0;
		}else{
			#get group access information.
			$checkgroup = group_validate($rss['bid'], $level_result['id'], 2);
			$checkmod = group_validate($rss['bid'], $level_result['id'], 1);
		}
		
		//see if the user can access this spot.
		$read_chk = permission_check($board_rule['B_Read']);
		if ($read_chk == 1){

			//see if pid is blank(would be for topcis only)
			if ($rss['pid'] == "") {
				//setup post link
				$post_link = $board_address.'/viewtopic.php?bid='.$rss['bid'].'&amp;tid='.$rss['tid'];

				#if body is over 100 characters, cut it off.
				if(strlen($rss['TBODY']) > 100){
					$rss_desc = substr_replace(var_cleanup($rss['TBODY']),'[...]',100);
				}else{
					$rss_desc = var_cleanup($rss['TBODY']);
				}
				
				//setup date
				$gmttime = gmdate ("r", $rss['TDATE']);				
			} else {
				//setup post link
				$post_link = $board_address.'/viewtopic.php?bid='.$rss['bid'].'&amp;tid='.$rss['tid'].'&amp;pid='.$rss['pid'].'#'.$rss['pid'];
				
				#if body is over 100 characters, cut it off.
				if(strlen($rss['PBODY']) > 100){
					$rss_desc = substr_replace(var_cleanup($rss['PBODY']),'[...]',100);
				}else{
					$rss_desc = var_cleanup($rss['PBODY']);
				}				

				//setup date
				$gmttime = gmdate ("r", $rss['PDATE']);			
			}
			$topic_date = date("r",strtotime("$gmt hours",strtotime($gmttime)));
			echo '<item>
			<link>'.$post_link.'</link>
			<date>'. $topic_date .'</date>
			<title>'.$rss['Topic'].'</title>
			<description>'.$rss_desc.'</description>
			<pubDate>'. $topic_date .'</pubDate>
			</item>';
		}	
	}
	echo '</channel></rss>';
}else{
	$error = $rss['invalidopt'];
	echo error($error, "error");
}
?>
