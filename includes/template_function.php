<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: template_function.php
Last Modified: 10/20/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
#theme_selector.

/**
 * Get template path.
 * @param int $id Theme ID
 * @return mixed
*/
function theme($id) {
    global $db;

    $query = $db->prepare('SELECT Temp_Path from ebb_style WHERE id=:id LIMIT 1');
    $query->execute(array(":id" => $id));
    $results = $query->fetch(PDO::FETCH_OBJ);

    return $results->Temp_Path;
}

/**
 * Builds the board index list.
*/
function index_board(){

    global $db, $index, $time_format, $gmt, $template_path, $stat, $logged_user, $access_level, $level_result, $rss;

    #categopry sql.
    $db->run = "select id, Board from ebb_boards where type='1' ORDER BY B_Order";
    $category_query = $db->query();
    $db->close();
    while ($cat = mysql_fetch_assoc ($category_query)) {
        #get permission rules.
        $db->run = "select B_Read from ebb_board_access WHERE B_id='$cat[id]'";
        $board_rule = $db->result();
        $db->close();
        $view_category = permission_check($board_rule['B_Read']);

        //see if user can see category.
        if($view_category == 1) {
            $db->run = "SELECT id, Board, Description, last_update, Posted_User, Post_Link FROM ebb_boards WHERE type='2' and Category='$cat[id]' ORDER BY B_Order";
            $board_query = $db->query();
            $db->close();
            $page = new template($template_path ."/board_header.htm");
            $page->replace_tags(array(
            "CAT-NAME" => "$cat[Board]",
            "LANG-BOARD" => "$index[boards]",
            "LANG-TOPIC" => "$index[topics]",
            "LANG-POST" => "$index[posts]",
            "LANG-LASTPOSTDATE" => "$index[lastposteddate]"));
            $board_row = $page->output();
            while ($row = mysql_fetch_assoc ($board_query)) {
                #guest & non-group members dont need a group-check.
                if(($stat == "guest") or ($stat == "Member")){
                    #get group access information.
                    $checkgroup = 0;
                    $checkmod = 0;
                }else{
                    #get group access information.
                    $checkgroup = group_validate($row['id'], $level_result['id'], 2);
                    $checkmod = group_validate($row['id'], $level_result['id'], 1);
                }
                #get topic & post count from board.
                $db->run = "select tid from ebb_topics WHERE bid='$row[id]'";
                $topic_num = $db->num_results();
                $db->close();
                $db->run = "select pid from ebb_posts WHERE bid='$row[id]'";
                $post_num = $db->num_results();
                $db->close();
                #get group moderators for this board.
                $board_moderator = moderator_boardlist($row['id']);
                #get sub-boards.
                $subboard = index_subboard($row['id']);
                #get last post details.
                if ($row['last_update'] == ""){
                    $board_date = $index['noposts'];
                    $last_post_link = "";
                }else{
                    $gmttime = gmdate ($time_format, $row['last_update']);
                    $board_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
                    $last_post_link = "<a href=\"viewtopic.php?$row[Post_Link]\">$index[Postedby]</a>: $row[Posted_User]";
                }
                #get read status on board.
                $read_ct = read_board_stat($row['id'], $logged_user);
                if (($read_ct == 1) OR ($row['last_update'] == "") OR ($stat == "guest")){
                    $icon = "<img src=\"$template_path/images/old.gif\" alt=\"$index[oldpost]\" title=\"$index[oldpost]\" />";
                }else{
                    $icon = "<img src=\"$template_path/images/new.gif\" alt=\"$index[newpost]\" title=\"$index[newpost]\" />";
                }
                #get permission rules.
                $db->run = "select B_Read from ebb_board_access WHERE B_id='$row[id]'";
                $board_rule = $db->result();
                $db->close();
                $view_board = permission_check($board_rule['B_Read']);
                #see if user can view the board.
                if($view_board == 1){
                    #get board values.
                    $page = new template($template_path ."/board_data.htm");
                    $page->replace_tags(array(
                    "LANG-TOPIC" => "$index[topics]",
                    "LANG-POST" => "$index[posts]",
                    "POSTICON" => "$icon",
                    "BOARDID" => "$row[id]",
                    "BOARDNAME" => "$row[Board]",
                    "LANG-RSS" => "$rss[viewfeed]",
                    "BOARDDESCRIPTION" => "$row[Description]",
                    "MODERATORS" => "$board_moderator",
                    "SUBBOARDS" => "$subboard",
                    "TOPICCOUNT" => "$topic_num",
                    "POSTCOUNT" => "$post_num",
                    "POSTDATE" => "$board_date",
                    "POSTLINK" => "$last_post_link"));
                    $board_row = $page->output();
                }
            }//end of board loop.
            $page = new template($template_path ."/board_footer.htm");
            $board_row = $page->output();
        }
    }//end of category loop.
    return($board_row);
}
#sub-board grabber
function index_subboard($bid){
 
	global $index, $db, $access_level, $stat, $level_result;
 
	$db->run = "select id, Board from ebb_boards where type='3' and Category='$bid' ORDER BY B_Order";
	$subboard_query = $db->query();
	$count_sub = $db->num_results();
	$db->close();

	if($count_sub == 0){
		$subboard = '';
	}else{
		$subboard = $index['subboards']. ":&nbsp;";
		while ($row = mysql_fetch_assoc ($subboard_query)) {
			#guest & non-group members dont need a group check.
			if(($stat == "guest") or ($stat == "Member")){
				#get group access information.
				$checkgroup = 0;
				$checkmod = 0;
			}else{
				#get group access information.
				$checkgroup = group_validate($row['id'], $level_result['id'], 2);
				$checkmod = group_validate($row['id'], $level_result['id'], 1);
			}
			#board rules sql.
			$db->run = "select B_Read from ebb_board_access WHERE B_id='$row[id]'";
			$board_rule = $db->result();
			$db->close();
			$view_board = permission_check($board_rule['B_Read']);
			#see if user can view the board.
			if($view_board == 1){	
				$subboard .= "<i><a href=\"viewboard.php?bid=$row[id]\">$row[Board]</a></i>&nbsp;";
			}
		}
	}
	return($subboard);
}
#sub display for viewboard.
function viewboard_subboard($bid){
 
	global $db, $index, $time_format, $gmt, $template_path, $stat, $logged_user, $access_level, $level_result, $rss;
	#start variable.
	$subboard_row = ''; 
	$db->run = "SELECT id, Board, Description, last_update, Posted_User, Post_Link FROM ebb_boards WHERE type='3' and Category='$bid' ORDER BY B_Order";
	$board_query = $db->query();
	$db->close();
	#subboard header.
	$page = new template($template_path ."/viewboard_hsubboards.htm");
	$page->replace_tags(array(
	"LANG-BOARD" => "$index[boards]",
	"LANG-TOPIC" => "$index[topics]",
	"LANG-POST" => "$index[posts]",
	"LANG-LASTPOSTDATE" => "$index[lastposteddate]"));
	$subboard_row = $page->output();
	while ($row = mysql_fetch_assoc ($board_query)) {
		#guest & non-group members dont need group check.
		if(($stat == "guest") or ($stat == "Member")){
			#get group access information.
			$checkgroup = 0;
			$checkmod = 0;
		}else{
			#get group access information.
			$checkgroup = group_validate($row['id'], $level_result['id'], 2);
			$checkmod = group_validate($row['id'], $level_result['id'], 1);
		}
		#get topic & post count from board.
		$db->run = "select tid from ebb_topics WHERE bid='$row[id]'";
		$topic_num = $db->num_results();
		$db->close();
		$db->run = "select pid from ebb_posts WHERE bid='$row[id]'";
		$post_num = $db->num_results();
		$db->close();
		#get group moderators for this board.
		$board_moderator = moderator_boardlist($row['id']);
		#get sub-boards.
		$subboard = index_subboard($row['id']);
		#get last post details.
		if ($row['last_update'] == ""){
			$board_date = $index['noposts'];
			$last_post_link = "";
		}else{
			$gmttime = gmdate ($time_format, $row['last_update']);
			$board_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			$last_post_link = "<a href=\"viewtopic.php?$row[Post_Link]\">$index[Postedby]</a>: $row[Posted_User]";
		}
		#get read status on board.
		$db->run = "select * from ebb_read_board where User='$logged_user'";
		$read_stat = $db->result();
		$db->close();
		$read_ct = read_board_stat($row['id'], $logged_user);
		if (($read_ct == 1) OR ($row['last_update'] == "") OR ($stat == "guest")){
			$icon = "<img src=\"$template_path/images/old.gif\" alt=\"$index[oldpost]\" title=\"$index[oldpost]\" />";
		}else{
			$icon = "<img src=\"$template_path/images/new.gif\" alt=\"$index[newpost]\" title=\"$index[newpost]\" />";
		}
		#get permission rules.
		$db->run = "select B_Read from ebb_board_access WHERE B_id='$row[id]'";
		$board_rule = $db->result();
		$db->close();
		$view_board = permission_check($board_rule['B_Read']);
		#see if user can view the board.
		if($view_board == 1){
			$page = new template($template_path ."/viewboard_subboards.htm");
			$page->replace_tags(array(
			"LANG-TOPIC" => "$index[topics]",
			"LANG-POST" => "$index[posts]",
			"POSTICON" => "$icon",
			"BOARDID" => "$row[id]",
			"BOARDNAME" => "$row[Board]",
			"LANG-RSS" => "$rss[viewfeed]",
			"BOARDDESCRIPTION" => "$row[Description]",
			"MODERATORS" => "$board_moderator",
			"SUBBOARDS" => "$subboard",
			"TOPICCOUNT" => "$topic_num",
			"POSTCOUNT" => "$post_num",
			"POSTDATE" => "$board_date",
			"POSTLINK" => "$last_post_link"));
			$subboard_row = $page->output();
		}
	}
	#subboard footer.
	$page = new template($template_path ."/viewboard_fsubboards.htm");
	$subboard_row = $page->output();
	return($subboard_row);
}
#moderator listing
function moderator_boardlist($b_id){

	global $db, $index;

	$db->run = "select group_id from ebb_grouplist where board_id='$b_id' order by group_id";
	$moderator_r = $db->query();
	$db->close();

	#see if a group exist for this board.
	$db->run = "select * from ebb_grouplist where board_id='$b_id'";
	$chk_group = $db->num_results();
	$db->close();
	if($chk_group == 0){
		$board_moderator = ''; 
	}else{
		$board_moderator = $index['moderators']. ":&nbsp;";
	while ($row = mysql_fetch_assoc ($moderator_r)) {
		//get group details.
		$db->run = "select id, Name from ebb_groups where id='$row[group_id]' and Enrollment!='2'";
		$group_r = $db->result();
		$db->close();
		//output the info.
		$board_moderator .= "<a href=\"groups.php?mode=view&amp;id=$group_r[id]\">$group_r[Name]</a>&nbsp;";
	}
	}
	return ($board_moderator);
}
#whosonline display function
function whosonline(){

	global $db;

	$db->run = "select DISTINCT Username from ebb_online where ip=''";
	$online_logged = $db->query();
	$db->close();
	
	$online = '';
	while ($row = mysql_fetch_assoc ($online_logged)) {
		//see what the users status is.
		$db->run = "select Status from ebb_users where Username='$row[Username]'";
		$stat = $db->result();
		$db->close();
		if ($stat['Status'] == "groupmember"){
			$db->run = "SELECT gid FROM ebb_group_users where Username='$row[Username]'";
			$groupchk = $db->result();
			$db->close();
			$db->run = "SELECT Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_type = $db->result();
			$db->close();
			if ($level_type['Level'] == 1){
				$online .= "<b><a href=\"Profile.php?user=$row[Username]\">$row[Username]</a></b>&nbsp;";
			}else{
				$online .= "<i><a href=\"Profile.php?user=$row[Username]\">$row[Username]</a></i>&nbsp;";
			}
		}elseif (($stat['Status'] == "Member") or ($level_type['Level'] == 3)){
			$online .= "<a href=\"Profile.php?user=$row[Username]\">$row[Username]</a>&nbsp;";
		}else{
			$online .= "&nbsp;";
		}
	}
	return $online;
}
#group detail display
function display_group(){

	global $db, $template_path, $title, $groups;

	$db->run = "select id, Name from ebb_groups where Enrollment!='2'";
	$query = $db->query();
	$db->close();
	
	$grouplist = '';
	#grouplist header.
	$page = new template($template_path ."/grouplist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$groups[title]"));
	$grouplist = $page->output();
	while ($row = mysql_fetch_assoc ($query)){
		$page = new template($template_path ."/grouplist.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$groups[title]",
		"GROUPID" => "$row[id]",
		"GROUPNAME" => "$row[Name]"));
		$grouplist = $page->output();
	}
	#grouplist footer.
	$page = new template($template_path ."/grouplist_foot.htm");
	$grouplist = $page->output();	
	return ($grouplist);
}
#view group members
function view_group(){

	global $groups, $txt, $id, $db, $gmt, $time_format, $template_path, $pm, $members, $index;

	$db->run = "select Username from ebb_group_users where gid='$id' and Status='Active'";
	$query = $db->query();
	$gnum = $db->num_results();
	$db->close();
	#list any members that are a part of this group.
	if ($gnum == 0){
		$page = new template($template_path ."/grouplist-viewusers_noresult.htm");
		$page->replace_tags(array(
		"LANG-GROUPMEMBERS" => "$groups[groupmembers]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]",
		"LANG-NOMEMBERS" => "$groups[nomembers]"));
		$groupmembers = $page->output();
	}else{
		#group userlist header.
		$page = new template($template_path ."/grouplist-viewusers_head.htm");
		$page->replace_tags(array(
		"LANG-GROUPMEMBERS" => "$groups[groupmembers]",
		"LANG-USERNAME" => "$txt[username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"LANG-REGISTRATIONDATE" => "$members[joindate]"));
		$groupmembers = $page->output();
		while ($row = mysql_fetch_assoc ($query)){
			#get user's detail.
			$db->run = "select Date_Joined, Username, Post_Count from ebb_users where Username='$row[Username]'";
			$r = $db->result();
			$db->close();
			#format the date.
			$gmttime = gmdate ($time_format, $r['Date_Joined']);
			$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			#group userlist data.
			$page = new template($template_path ."/grouplist-viewusers.htm");
			$page->replace_tags(array(
			"USERNAME" => "$r[Username]",
			"LANG-PMALT" => "$pm[postpmalt]",
			"LANG-POSTCOUNT" => "$index[posts]",
			"POSTCOUNT" => "$r[Post_Count]",
			"REGISTRATIONDATE" => "$join_date"));
			$groupmembers = $page->output();
		}
		#group userlist footer.
		$page = new template($template_path ."/grouplist-viewusers_foot.htm");
		$groupmembers = $page->output();
	}
	return ($groupmembers);
}
#memberlist display function
function memberlist(){

	global $title, $txt, $pagenation, $members, $gmt, $query, $template_path, $time_format, $menu, $index;

	#memberlist header.
	$page = new template($template_path ."/memberlist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[members]",
	"PAGENATION" => "$pagenation",
	"LANG-USERNAME" => "$txt[username]",
	"LANG-POSTCOUNT" => "$index[posts]",
	"LANG-REGISTRATIONDATE" => "$members[joindate]"));
	$memberlist = $page->output();
	while ($row = mysql_fetch_assoc ($query)){

		$gmttime = gmdate ($time_format, $row['Date_Joined']);
		$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		#memberlist data.
		$page = new template($template_path ."/memberlist.htm");
		$page->replace_tags(array(
		"USERNAME" => "$row[Username]",
		"LANG-POSTCOUNT" => "$index[posts]",
		"POSTCOUNT" => "$row[Post_Count]",
		"REGISTATIONDATE" => "$join_date"));
		$memberlist = $page->output();
	}
	#memberlist footer.
	$page = new template($template_path ."/memberlist_foot.htm");
	$memberlist = $page->output();	
	return ($memberlist);
}
#search results-topics
function search_results_topic(){

	global $title, $template_path, $pagenation, $search_result, $db, $num, $search, $menu, $index, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[search]",
	"LANG-SEARCHRESULTS" => "$search[searchresults]",
	"PAGINATION" => "$pagenation",
	"NUM-RESULTS" => "$num",
	"LANG-RESULTS" => "$search[result]",
	"LANG-USERNAME" => "$search[author]",
	"LANG-TOPIC" => "$index[topics]",
	"LANG-POSTEDIN" => "$search[postedin]"));
	$searchresults = $page->output();
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
			#get board details.
			$db->run = "SELECT Board FROM ebb_boards where id='$row[bid]'";
			$board_r = $db->result();
			$db->close();
			#search results data.
			$page = new template($template_path ."/searchresults_topic.htm");
			$page->replace_tags(array(
			"BID" => "$row[bid]",
			"TID" => "$row[tid]",
			"TOPICNAME" => "$row[Topic]",
			"AUTHOR" => "$row[author]",
			"LANG-POSTEDIN" => "$search[postedin]",
			"BOARDNAME" => "$board_r[Board]"));
			$searchresults = $page->output();
		}
	}
	#search results footer.
	$page = new template($template_path ."/searchresults_foot.htm");
	$searchresults = $page->output();
	return ($searchresults);
}
#search results-posts
function search_results_post(){

	global $title, $template_path, $pagenation, $search_result, $db, $num, $search, $menu, $index, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[search]",
	"LANG-SEARCHRESULTS" => "$search[searchresults]",
	"PAGINATION" => "$pagenation",
	"NUM-RESULTS" => "$num",
	"LANG-RESULTS" => "$search[result]",
	"LANG-USERNAME" => "$search[author]",
	"LANG-TOPIC" => "$index[topics]",
	"LANG-POSTEDIN" => "$search[postedin]"));
	$searchresults = $page->output();
	while ($row = mysql_fetch_assoc($search_result)){
	
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
			#get topic details.
			$db->run = "SELECT Topic FROM ebb_topics where tid='$row[tid]'";
			$topic_r = $db->result();
			$db->close();
			#get board details.
			$db->run = "SELECT Board FROM ebb_boards where id='$row[bid]'";
			$board_r = $db->result();
			$db->close();
			#search results data.
			$page = new template($template_path ."/searchresults_post.htm");
			$page->replace_tags(array(
			"BID" => "$row[bid]",
			"TID" => "$row[tid]",
			"PID" => "$row[pid]",
			"TOPICNAME" => "$topic_r[Topic]",
			"AUTHOR" => "$row[author]",
			"LANG-POSTEDIN" => "$search[postedin]",
			"BOARDNAME" => "$board_r[Board]"));
			$searchresults = $page->output();
		}
	}
	#search results footer.
	$page = new template($template_path ."/searchresults_foot.htm");
	$searchresults = $page->output();
	return $searchresults;
}
#search results-new posts
function search_results_newposts(){

	global $title, $template_path, $search_results, $search_results2, $count, $logged_user, $db, $search, $menu, $index, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[search]",
	"LANG-SEARCHRESULTS" => "$search[searchresults]",
	"PAGINATION" => "",
	"NUM-RESULTS" => "$count",
	"LANG-RESULTS" => "$search[result]",
	"LANG-USERNAME" => "$search[author]",
	"LANG-TOPIC" => "$index[topics]",
	"LANG-POSTEDIN" => "$search[postedin]"));
	$page->output();
	//output any topics
	while ($row = mysql_fetch_assoc($search_results)) {

		$db->run = "select * from ebb_read_topic WHERE Topic='$row[tid]' and User='$logged_user'";
		$read_ct = $db->num_results();
		$db->close();
		if ($read_ct == 0){
		
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
		
				#get board details.
				$db->run = "SELECT Board FROM ebb_boards where id='$row[bid]'";
				$board_r = $db->result();
				$db->close();
				#search results data.
				$page = new template($template_path ."/searchresults_topic.htm");
				$page->replace_tags(array(
				"BID" => "$row[bid]",
				"TID" => "$row[tid]",
				"TOPICNAME" => "$row[Topic]",
				"AUTHOR" => "$row[author]",
				"LANG-POSTEDIN" => "$search[postedin]",
				"BOARDNAME" => "$board_r[Board]"));
				$page->output();			
			}//end promission check.
		}//endnewpost check
	}//end loop.
	
	//output any posts
	while ($row2 = mysql_fetch_assoc($search_results2)){
		$db->run = "SELECT Topic FROM ebb_topics where tid='$row2[tid]'";
		$topic_r = $db->result();
		$db->close();
		//see if post is new.
		$db->run = "select * from ebb_read_topic WHERE Topic='$row2[tid]' and User='$logged_user'";
		$read_ct2 = $db->num_results();
		$db->close();
		if ($read_ct2 == 0){
		
			//check for the posting rule.
			$db->run = "select B_Read from ebb_board_access WHERE B_id='$row2[bid]'";
			$board_rule2 = $db->result();
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
			$read_chk2 = permission_check($board_rule2['B_Read']);
			if ($read_chk2 == 1){
				#get board details.
				$db->run = "SELECT Board FROM ebb_boards where id='$row2[bid]'";
				$board_r2 = $db->result();
				$db->close();
				#search results data.
				$page = new template($template_path ."/searchresults_post.htm");
				$page->replace_tags(array(
				"BID" => "$row2[bid]",
				"TID" => "$row2[tid]",
				"PID" => "$row2[pid]",
				"TOPICNAME" => "$topic_r[Topic]",
				"AUTHOR" => "$row2[author]",
				"LANG-POSTEDIN" => "$search[postedin]",
				"BOARDNAME" => "$board_r2[Board]"));
				$page->output();
			}//end permission check. 
		}//end newpost check.
	}//end loop.
	
	#search results footer.
	$page = new template($template_path ."/searchresults_foot.htm");
	$page->output();
}
#board select function
function board_select(){

	global $search, $db;

	$db->run = "SELECT id, Board FROM ebb_boards where type='2' or type='3'";
	$board_search = $db->query();
	$db->close();
	$boardlist = "<select name=\"board\" class=\"text\" id=\"board\">
	<option value=\"\">$search[selboard]</option>";
	while ($row = mysql_fetch_assoc ($board_search)){
		$boardlist .= "<option value=\"$row[id]\">$row[Board]</option>";
	}
	$boardlist .= "</select>";
	return ($boardlist);
}
#timezone select function
function timezone_select($tzone){

	$timezone = '<select name="time_zone" class="text">';
	#see if any settings are set, if not set value at 0.
	if($tzone == ""){
		$tzone = 0;
	}
	#-12 GMT
	if ($tzone == "-12"){
		$timezone .= '<option value="-12" selected=selected>(GMT -12:00) Eniwetok, Kwajalein</option>';
	}else{
		$timezone .= '<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>';
	}
	#-11 GMT
	if ($tzone == "-11"){
		$timezone .= '<option value="-11" selected=selected>(GMT -11:00) Midway Island, Samoa</option>';
	}else{
		$timezone .= '<option value="-11">(GMT -11:00) Midway Island, Samoa</option>';
	}
	#-10 GMT
	if ($tzone == "-10"){
		$timezone .= '<option value="-10" selected=selected>(GMT -10:00) Hawaii</option>';
	}else{
		$timezone .= '<option value="-10">(GMT -10:00) Hawaii</option>';
	}
	#-9 GMT
	if ($tzone == "-9"){
		$timezone .= '<option value="-9" selected=selected>(GMT -9:00) Alaska</option>';
	}else{
		$timezone .= '<option value="-9">(GMT -9:00) Alaska</option>';
	}
	#-8 GMT
	if ($tzone == "-8"){
		$timezone .= '<option value="-8" selected=selected>(GMT -8:00) Pacific Time (US &amp; Canada), Tijuana</option>';
	}else{
		$timezone .= '<option value="-8">(GMT -8:00) Pacific Time (US &amp; Canada), Tijuana</option>';
	}
	#-7 GMT
	if ($tzone == "-7"){
		$timezone .= '<option value="-7" selected=selected>(GMT -7:00) Mountain Time (US &amp; Canada), Arizona</option>';
	}else{
		$timezone .= '<option value="-7">(GMT -7:00) Mountain Time (US &amp; Canada), Arizona</option>';
	}
	#-6 GMT
	if ($tzone == "-6"){
		$timezone .= '<option value="-6" selected=selected>(GMT -6:00) Central Time (US &amp; Canada), Mexico City, Central America</option>';
	}else{
		$timezone .= '<option value="-6">(GMT -6:00) Central Time (US &amp; Canada), Mexico City, Central America</option>';
	}
	#-5 GMT
	if ($tzone == "-5"){
		$timezone .= '<option value="-5" selected=selected>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>';
	}else{
		$timezone .= '<option value="-5">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>';
	}
	#-4 GMT
	if ($tzone == "-4"){
		$timezone .= '<option value="-4" selected=selected>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz, Santiago</option>';
	}else{
		$timezone .= '<option value="-4">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz, Santiago</option>';
	}
	#-3.5 GMT
	if ($tzone == "-3.5"){
		$timezone .= '<option value="-3.5" selected=selected>(GMT -3:30) Newfoundland</option>';
	}else{
		$timezone .= '<option value="-3.5">(GMT -3:30) Newfoundland</option>';
	}
	#-3 GMT
	if ($tzone == "-3"){
		$timezone .= '<option value="-3" selected=selected>(GMT -3:00) Brasilia, Buenos Aires, Georgetown, Greenland</option>';
	}else{
		$timezone .= '<option value="-3">(GMT -3:00) Brasilia, Buenos Aires, Georgetown, Greenland</option>';
	}
	#-2 GMT
	if ($tzone == "-2"){
		$timezone .= '<option value="-2" selected=selected>(GMT -2:00) Mid-Atlantic, Ascension Islands, St. Helena</option>';
	}else{
		$timezone .= '<option value="-2">(GMT -2:00) Mid-Atlantic, Ascension Islands, St. Helena</option>';
	}
	#-1 GMT
	if ($tzone == "-1"){
		$timezone .= '<option value="-1" selected=selected>(GMT -1:00) Azores, Cape Verde Islands</option>';
	}else{
		$timezone .= '<option value="-1">(GMT -1:00) Azores, Cape Verde Islands</option>';
	}
	#0 GMT
	if ($tzone == "0"){
		$timezone .= '<option value="0" selected=selected>(GMT) Casablanca, Dublin, Edinburgh, Lisbon, London, Monrovia</option>';
	}else{
		$timezone .= '<option value="0">(GMT) Casablanca, Dublin, Edinburgh, Lisbon, London, Monrovia</option>';
	}
	#+1 GMT
	if ($tzone == "1"){
		$timezone .= '<option value="1" selected=selected>(GMT +1:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>';
	}else{
		$timezone .= '<option value="1">(GMT +1:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>';
	}
	#+2 GMT
	if ($tzone == "2"){
		$timezone .= '<option value="2" selected=selected>(GMT +2:00) Cairo, Helsinki, Kaliningrad, South Africa</option>';
	}else{
		$timezone .= '<option value="2">(GMT +2:00) Cairo, Helsinki, Kaliningrad, South Africa</option>';
	}
	#+3 GMT
	if ($tzone == "3"){
		$timezone .= '<option value="3" selected=selected>(GMT +3:00) Baghdad, Riyadh, Moscow, Nairobi</option>';
	}else{
		$timezone .= '<option value="3">(GMT +3:00) Baghdad, Riyadh, Moscow, Nairobi</option>';
	}
	#+3.5 GMT
	if ($tzone == "3.5"){
		$timezone .= '<option value="3.5" selected=selected>(GMT +3:30) Tehran</option>';
	}else{
		$timezone .= '<option value="3.5">(GMT +3:30) Tehran</option>';
	}
	#+4 GMT
	if ($tzone == "4"){
		$timezone .= '<option value="4" selected=selected>(GMT +4:00) Abu Dhabi, Baku, Muscat, Tbilii</option>';
	}else{
		$timezone .= '<option value="4">(GMT +4:00) Abu Dhabi, Baku, Muscat, Tbilii</option>';
	}
	#+4.5 GMT
	if ($tzone == "4.5"){
		$timezone .= '<option value="4.5" selected=selected>(GMT +4:30) Kabul</option>';
	}else{
		$timezone .= '<option value="4.5">(GMT +4:30) Kabul</option>';
	}
	#+5 GMT
	if ($tzone == "5"){
		$timezone .= '<option value="5" selected=selected>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>';
	}else{
		$timezone .= '<option value="5">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>';
	}
	#+5.5 GMT
	if ($tzone == "5.5"){
		$timezone .= '<option value="5.5" selected=selected>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>';
	}else{
		$timezone .= '<option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>';
	}
	#+5.75 GMT
	if ($tzone == "5.75"){
		$timezone .= '<option value="5.75" selected=selected>(GMT +5:45) Kathmandu</option>';
	}else{
		$timezone .= '<option value="5.75">(GMT +5:45) Kathmandu</option>';
	}
	#+6 GMT
	if ($tzone == "6"){
		$timezone .= '<option value="6" selected=selected>(GMT +6:00) Almaty, Colombo, Dhaka, Novosibirsk, Sri Jayawardenepura</option>';
	}else{
		$timezone .= '<option value="6">(GMT +6:00) Almaty, Colombo, Dhaka, Novosibirsk, Sri Jayawardenepura</option>';
	}
	#+6.5 GMT
	if ($tzone == "6.5"){
		$timezone .= '<option value="6.5" selected=selected>(GMT +6:30) Rangoon</option>';
	}else{
		$timezone .= '<option value="6.5">(GMT +6:30) Rangoon</option>';
	}
	#+7 GMT
	if ($tzone == "7"){
		$timezone .= '<option value="7" selected=selected>(GMT +7:00) Bangkok, Hanoi, Jakarta, Krasnoyarsk</option>';
	}else{
		$timezone .= '<option value="7">(GMT +7:00) Bangkok, Hanoi, Jakarta, Krasnoyarsk</option>';
	}
	#+8 GMT
	if ($tzone == "8"){
		$timezone .= '<option value="8" selected=selcted>(GMT +8:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>';
	}else{
		$timezone .= '<option value="8">(GMT +8:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>';
	}
	#+9 GMT
	if ($tzone == "9"){
		$timezone .= '<option value="9" selected=selected>(GMT +9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>';
	}else{
		$timezone .= '<option value="9">(GMT +9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>';
	}
	#+9.5 GMT
	if ($tzone == "9.5"){
		$timezone .= '<option value="9.5" selected=selected>(GMT +9:30) Adelaide, Darwin</option>';
	}else{
		$timezone .= '<option value="9.5">(GMT +9:30) Adelaide, Darwin</option>';
	}
	#+10 GMT
	if ($tzone == "10"){
		$timezone .= '<option value="10" selected=selected>(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>';
	}else{
		$timezone .= '<option value="10">(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>';
	}
	#+11 GMT
	if ($tzone == "11"){
		$timezone .= '<option value="11" selected=selected>(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>';
	}else{
		$timezone .= '<option value="11">(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>';
	}
	#+12 GMT
	if ($tzone == "12"){
		$timezone .= '<option value="12" selected=selected>(GMT +12:00) Auckland, Fiji, Kamchatka, Marshall Island, Wellington</option>';
	}else{
		$timezone .= '<option value="12">(GMT +12:00) Auckland, Fiji, Kamchatka, Marshall Island, Wellington</option>';
	}
	#+13 GMT
	if ($tzone == "13"){
		$timezone .= '<option value="13" selected=selected>(GMT +13:00) Nuku\' alofa</option>';
	}else{
		$timezone .= '<option value="13">(GMT +13:00) Nuku\' alofa</option>';
	}
	$timezone .= '</select>';

	return ($timezone);
}
#style select function
function style_select($stylesel){

	global $db;

	$db->run = "SELECT id, Name FROM ebb_style";
	$style_query = $db->query();
	$db->close();
	
	$style_select = "<select name=\"style\" class=\"text\">";
	while ($row = mysql_fetch_assoc ($style_query)){
		#see what is currently selected already.
		if ($stylesel == ""){
			$style_select .= "<option value=\"$row[id]\">$row[Name]</option>"; 
		}else{
			if ($stylesel == $row['id']){
				$style_select .= "<option value=\"$row[id]\" selected=selected>$row[Name]</option>";
			}else{
				$style_select .= "<option value=\"$row[id]\">$row[Name]</option>";
			}
		}
	}
	$style_select .= "</select>";

	return ($style_select);
}
#language select function
function lang_select($langsel){

	global $userpref;

	$lang = "<select name=\"default_lang\" class=\"text\">";
	$handle = opendir("lang");
	while (($file = readdir($handle))) {
		if (is_file("lang/$file") && false !== strpos($file, '.lang.php')) {

			$file = str_replace(".lang.php", "", $file);
			if($langsel == ""){
				$lang .= "<option value=\"$file\">$file</option>"; 
			}else{
				if ($langsel == $file){
					$lang .= "<option value=\"$file\" selected=selected>$file</option>";
				}else{
					$lang .= "<option value=\"$file\">$file</option>";
				}
			}
		}
	}
	$lang .= "</select>";
	return ($lang);
}
#group joined list
function groups_joined(){

	global $db, $logged_user, $userinfo, $title, $groups, $template_path;

	$db->run = "SELECT Name, id, Enrollment FROM ebb_groups";
	$joined_q = $db->query();
	$db->close();
	#grouplist CP header.
    $page = new template($template_path ."/editgrouplist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$userinfo[title]",
	"LANG-GROUPMANAGE" => "$userinfo[managegroups]",
	"LANG-TEXT" => "$userinfo[grouptxt]",
	"LANG-GROUPNAME" => "$groups[name]"));
	$joined_group = $page->output();
	while ($row = mysql_fetch_assoc ($joined_q)) {
		//see if user already joined this.
		$db->run = "SELECT Status, gid FROM ebb_group_users where gid='$row[id]' and Username='$logged_user'";
		$result = $db->result();
		$join_chk = $db->num_results();
		$db->close();
		#see if a user joined a group and see if their pending still.
		if($join_chk == 1){
			if ($result['Status'] == "Pending"){
				$group_status = $userinfo['pending'];
			}else{
				$group_status = "<a href=\"Profile?mode=unjoin_group&amp;id=$result[gid]\">[$userinfo[unjoingroup]]</a>";
			}
		}else{
		 	#See if a group is opened or locked or hidden.
			if ($row['Enrollment'] == 1){
				$group_status = "<a href=\"Profile?mode=join_group&amp;id=$row[id]\">[$userinfo[joingroup]]</a>";
			}elseif($row['Enrollment'] == 0){
				$group_status = "";
			}else{
				$group_status = "";
			}
		}
		#grouplist CP data.
    	$page = new template($template_path ."/editgrouplist.htm");
		$page->replace_tags(array(
		"GROUPSTATUS" => "$group_status",
		"GROUPNAME" => "$row[Name]"));
		$joined_group = $page->output();	
	}
	#grouplist CP footer.
	$page = new template($template_path ."/editgrouplist_foot.htm");
	$joined_group = $page->output();
	return ($joined_group);
}
#list subscriptions function
function digest_list(){

	global $template_path, $title, $search, $pagenation, $db, $logged_user, $userinfo, $sub_q, $num, $pm, $mod;

	if ($num == 0){
	 	#subscription no result output.
		$page = new template($template_path ."/editsubscription_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITSUBSCRIPTION" => "$userinfo[subscriptionsetting]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$userinfo[digesttxt]",
		"LANG-SUBSCRIBED" => "$userinfo[scription]",
		"LANG-POSTEDIN" => "$search[postedin]",
		"LANG-DELETE" => "$userinfo[delsubscription]",
		"LANG-NORESULT" => "$userinfo[nosubscription]"));
		$sub = $page->output();
	}else{
		#subscription header.
		$page = new template($template_path ."/editsubscription_head.htm");
		$page->replace_tags(array(
		"LANG-DELPROMPT" => "$mod[condel]",
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-EDITSUBSCRIPTION" => "$userinfo[subscriptionsetting]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$userinfo[digesttxt]",
		"LANG-SUBSCRIBED" => "$userinfo[scription]",
		"LANG-POSTEDIN" => "$search[postedin]",
		"LANG-DELETE" => "$userinfo[delsubscription]"));
		$sub = $page->output();
		while ($row = mysql_fetch_assoc ($sub_q)) {
			#get topic details
			$db->run = "SELECT bid, tid, Topic FROM ebb_topics where tid='$row[tid]'";
			$result = $db->result();
			$db->close();
			#get board details.
			$db->run = "SELECT id, Board FROM ebb_boards where id='$result[bid]'";
			$board_r = $db->result();
			$db->close();
			#output results
			$page = new template($template_path ."/editsubscription.htm");
			$page->replace_tags(array(
			"TOPICID" => "$row[tid]",
			"LANG-DELETE" => "$pm[del]",
			"LANG-POSTEDIN" => "$search[postedin]",
			"TOPICNAME" => "$result[Topic]",
			"BOARDID" => "$board_r[id]",
			"BOARDNAME" => "$board_r[Board]"));
			$sub = $page->output();
		}
		$page = new template($template_path ."/editsubscription_foot.htm");
		$sub = $page->output();
	}
	return ($sub);
}
#avatar gallery function
function avatar_gallery(){

	$x = 0; // we will use this to count to three later
	$gallery = '';
	$handle = opendir("images/avatar");
	while(($file = readdir($handle))){
		if (is_file("images/avatar/$file") and false !== strpos($file, '.gif') or false !== strpos($file, '.jpg') or false !== strpos($file, '.jpeg') or false !== strpos($file, '.png')){
			if (($x % 4) == 0) {
				$gallery .= "</tr><tr>";  // $x == 4 so we start the line again
				$x = 0; // $x is now 4 so we reset it here to start the next line
			}
		$gallery .= "<td class=\"td1\" align=\"center\"><img src=\"images/avatar/$file\" alt=\"$file\" title=\"$file\" /><br /><input type=\"radio\" name=\"avatarsel\" value=\"images/avatar/$file\" class=\"text\" /></td>";
		$x++; // increment $x by 1 so we get our 4
		}
	}
	return ($gallery);
}
#list board
function board_listing(){

	global $title, $rules, $index, $pagenation, $posting, $board_policy, $db, $board_rule, $access_level, $stat, $viewboard, $num, $query, $logged_user, $bid, $template_path, $time_format, $level_result, $gmt;
	
	if(($stat == "guest") or ($stat == "Member")){
		#guest & non-group members dont need group check.
		$checkgroup = 0;
		$checkmod = 0;
	}else{
		#get group access information.
		$checkgroup = group_validate($bid, $level_result['id'], 2);
		$checkmod = group_validate($bid, $level_result['id'], 1);
	}
	//see if user can read this board.
	$read_chk = permission_check($board_rule['B_Read']);
	if ($read_chk == 0){
		$boardmsg = $viewboard['noread'];
		$boarderr = 1;
	}elseif($num == 0){
		$boardmsg = $viewboard['nopost'];
		$boarderr = 1;	
	}else{
		$boardmsg = '';
		$boarderr = 0;	 
	}
	#see if we need to display a special message.
	if($boarderr == 1){
		$page = new template($template_path ."/viewboard_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$rules[Board]",
		"LANG-BOARD" => "$index[boards]",
		"LANG-TOPIC" => "$index[topics]",
		"LANG-POST" => "$index[posts]",
		"LANG-LASTPOSTDATE" => "$index[lastposteddate]",
		"PAGENATION" => "$pagenation",
		"POST-RULE" => "$posting",
		"BOARD-POLICY" => "$board_policy",
		"LANG-TOPIC" => "$viewboard[topic]",
		"LANG-POSTEDBY" => "$index[Postedby]",
		"LANG-REPLIES" => "$viewboard[replies]",
		"LANG-POSTVIEWS" => "$viewboard[views]",
		"LANG-LASTPOSTEDBY" => "$viewboard[lastpost]",
		"BOARDMSG" => "$boardmsg",
		"LANG-ICONGUIDE" => "$index[iconguide]",
		"LANG-NEW" =>"$viewboard[newtopic]",
		"LANG-OLD" =>"$viewboard[oldtopic]",
		"LANG-POLL" =>"$viewboard[polltopic]",
		"LANG-LOCKED" =>"$viewboard[lockedtopic]",
		"LANG-IMPORTANT" => "$viewboard[importanttopic]",
		"LANG-HOTTOPIC" => "$viewboard[hottopic]"));
		$board = $page->output();	 
	}else{
		#viewboard header.
		$page = new template($template_path ."/viewboard_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$rules[Board]",
		"LANG-BOARD" => "$index[boards]",
		"LANG-TOPIC" => "$index[topics]",
		"LANG-POST" => "$index[posts]",
		"LANG-LASTPOSTDATE" => "$index[lastposteddate]",
		"PAGENATION" => "$pagenation",
		"POST-RULE" => "$posting",
		"LANG-TOPIC" => "$viewboard[topic]",
		"LANG-POSTEDBY" => "$index[Postedby]",
		"LANG-REPLIES" => "$viewboard[replies]",
		"LANG-POSTVIEWS" => "$viewboard[views]",
		"LANG-LASTPOSTEDBY" => "$viewboard[lastpost]"));
		$board = $page->output();
		while ($row = mysql_fetch_assoc ($query)){
			//grab posted date info
			$gmttime = gmdate ($time_format, $row['last_update']);
			$topic_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			//get reply number
			$db->run = "select tid, pid from ebb_posts WHERE tid='$row[tid]'";
			$reply_num = $db->num_results();
			$post_pid = $db->result();
			$db->close();
			#see if any attachments are added to the topic, if so display an icon.
			$db->run = "select id from ebb_attachments where tid='$row[tid]' and pid='0'";
			$attach_ct = $db->num_results();
			$db->close();
			$db->run = "select id from ebb_attachments where pid='$post_pid[pid]' and tid='0'";
			$attach_ct2 = $db->num_results();
			$db->close();
			if(($attach_ct == 1) or ($attach_ct2 == 1)){
				$attach_icon = "<img src=\"$template_path/images/attach_icon.gif\" alt=\"$viewboard[attachment]\" title=\"$viewboard[attachment]\" />"; 
			}else{
				$attach_icon = '';
			}
			//see if the post is new to the user or not.
			$read_count = read_topic_stat($row['tid'], $logged_user);
			if (($read_count == 1) OR ($stat == "guest")){
				$read_status = "old";
			}else{
				$read_status = "new";
			}
			//decide on icon.
			if ($row['important'] == 1){
				$icon = "$template_path/images/important.gif";
			}else{
				if ($row['Locked'] == 1){
					$icon = "$template_path/images/locked_topic.gif";
				}
				if ($row['Type'] == "Poll"){
					$icon = "$template_path/images/poll.gif";
				}elseif ($reply_num >= 15){
					$icon = "$template_path/images/hottopic.gif";
				}elseif ($read_status == "new"){
					$icon = "$template_path/images/new.gif";
				}elseif ($read_status == "old"){
					$icon = "$template_path/images/old.gif";
				}
			}
			$page = new template($template_path ."/viewboard.htm");
			$page->replace_tags(array(
			"POSTINGICON" => "$icon",
			"ATTACHMENTICON" => "$attach_icon",
			"BOARDID" =>"$row[bid]",
			"TOPICID" =>"$row[tid]",
			"TOPICNAME" =>"$row[Topic]",
			"AUTHOR" =>"$row[author]",
			"LANG-REPLIES" => "$viewboard[repliedmsg]",
			"LANG-POSTVIEWS" => "$viewboard[views]",
			"REPLYCOUNT" => "$reply_num",
			"POSTVIEWS" => "$row[Views]",
			"TOPICDATE" => "$topic_date",
			"POSTLINK" => "$row[Post_Link]",
			"LANG-POSTEDUSER" => "$index[Postedby]",
			"POSTEDUSER" => "$row[Posted_User]"));
			$board = $page->output();
		}
		#viewboard footer.
		$page = new template($template_path ."/viewboard_foot.htm");
		$page->replace_tags(array(
		"BOARD-POLICY" => "$board_policy",
		"LANG-ICONGUIDE" => "$index[iconguide]",
		"LANG-NEW" =>"$viewboard[newtopic]",
		"LANG-OLD" =>"$viewboard[oldtopic]",
		"LANG-POLL" =>"$viewboard[polltopic]",
		"LANG-LOCKED" =>"$viewboard[lockedtopic]",
		"LANG-IMPORTANT" => "$viewboard[importanttopic]",
		"LANG-HOTTOPIC" => "$viewboard[hottopic]"));
		$board = $page->output();
	}
	return ($board);
}
#reply listing
function reply_listing(){

	global $db, $query, $gmt, $index, $template_path, $allowsmile, $allowbbcode, $allowimg, $stat, $access_level, $logged_user, $viewtopic, $edit, $time_format, $board_rule, $checkmod, $checkgroup, $permission_type, $t_name;

	while ($row = mysql_fetch_assoc ($query)) {
		#get author's profile information.
		$db->run = "select Post_Count, Custom_Title, Status, Avatar, Sig from ebb_users WHERE Username='$row[author]'";
		$re_user = $db->result();
		$db->close();
		#get user custom title if one is made for them.
		if(empty($re_user['Custom_Title'])){
			$customtitle = ''; 
		}else{
			$customtitle = $re_user['Custom_Title']."<br />"; 
		}
		//get date
		$gmttime = gmdate ($time_format, $row['Original_Date']);
		$post_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		//get rank stars.
		if ($re_user['Status'] == "groupmember"){
			//find what usergroup this user belongs to.
			$db->run = "SELECT gid FROM ebb_group_users where Username='$row[author]'";
			$groupchk = $db->result();
			$db->close();
			//get the access level of this group.
			$db->run = "SELECT Name, Level FROM ebb_groups where id='$groupchk[gid]'";
			$level_r = $db->result();
			$db->close();
			if($level_r['Level'] == 1){
				$rankicon = "<img src=\"$template_path/images/adminstar.gif\" alt=\"$level_r[Name]\" />";
				$rank = $level_r['Name'];
			}elseif ($level_r['Level'] == 2){
				$rankicon = "<img src=\"$template_path/images/modstar.gif\" alt=\"$level_r[Name]\" />";
				$rank = $level_r['Name'];
			}elseif($level_r['Level'] == 3){
				$db->run = "SELECT Name, Star_Image FROM ebb_ranks WHERE Post_req <= $re_user[Post_Count] ORDER BY Post_req DESC";
				$rank2 = $db->result();
				$db->close();
				//get outoput.
				$rankicon = "<img src=\"$template_path/images/$rank2[Star_Image]\" alt=\"$rank\" />";
				$rank = $level_r['Name'];
			}
		}elseif($re_user['Status'] == "Banned"){
			$rankicon = "";
			$rank = "Banned";
		}else{
			$db->run = "SELECT Name, Star_Image FROM ebb_ranks WHERE Post_req <= $re_user[Post_Count] ORDER BY Post_req DESC";
			$rank2 = $db->result();
			$db->close();
			$rank = $rank2['Name'];
			$rankicon = "<img src=\"$template_path/images/$rank2[Star_Image]\" alt=\"$rank\" />";
		}
		//set bbcode for replies.
		$re_msg = $row['Body'];
		//see if user wish to disable smiles in post.
		if($row['disable_smiles'] == 0){
			if ($allowsmile == 1){
				$re_msg = smiles($re_msg);
			}
		}
		//see if user wish to disable bbcode in post.
		if($row['disable_bbcode'] == 0){
			if ($allowimg == 1){
				$re_msg = BBCode($re_msg, true);
			}
			if ($allowbbcode == 1){
				$re_msg = BBCode($re_msg);
			}
		}
		if(empty($re_user['Avatar'])){
			$avatar = "images/noavatar.gif";
		}else{
			$avatar = $re_user['Avatar'];
		}
		if(empty($re_user['Sig'])){
			$sig = '';
		}else{
			$psig = nl2br(smiles(BBCode(language_filter($re_user['Sig'], 1), true))); 
			$sig = "_________________<br />$psig";
		}
		$re_msg = language_filter($re_msg, 1);
		$re_msg = nl2br($re_msg);
		#see if guests can download content.
		$permission_chk_dwnld = access_vaildator($permission_type, 29);
		$colume = 'download_attachments';
		$settings = board_settings($colume);
		if(($permission_chk_dwnld == 0) and ($checkgroup == 1)){
			$attachment = '';
		}elseif(($settings['download_attachments'] == 0) and ($stat == "guest")){
			$attachment = '';
		}else{
			#list any attachments.
			$attachment = attachment_stat("post", $row['author'], $row['pid']);
		}
		#see if user has a moderator status.
		$permission_chk_edit = access_vaildator($permission_type, 20);
		$permission_chk_del = access_vaildator($permission_type, 21);
		$permission_chk_vip = access_vaildator($permission_type, 24);
		$permission_chk_attach = access_vaildator($permission_type, 26);
		$permission_chk_warn = access_vaildator($permission_type, 25);
		$warn_bar = user_warn($row['author']);
		$edit_chk = permission_check($board_rule['B_Edit']);
		$delete_chk = permission_check($board_rule['B_Delete']);
		#see if user is a moderator or admin
		if ($checkmod == 1){
			#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
			if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
				$postopt = '';
				$quickEditStatus = "";		
			}else{
				#see what a moderator can do.
				if($permission_chk_vip == 1){
					$view_ip = "$viewtopic[ipmod]&nbsp;<a href=\"manage.php?mode=viewip&amp;ip=$row[IP]&amp;u=$row[author]&amp;tid=$row[tid]&amp;bid=$row[bid]\">$row[IP]</a>";
				}else{
					$view_ip = '';
				}
				if(($permission_chk_edit == 1) and ($permission_chk_del == 1)){
					$postopt = $view_ip."&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
				}elseif($permission_chk_edit == 1){
					$postopt = $view_ip."&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
				}elseif($permission_chk_del == 1){
					$postopt = $view_ip."&nbsp;<a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "";
				}else{
					$postopt = '';
					$quickEditStatus = "";
				}
			}
		}else{
			#see if user is part of a group.
			if($checkgroup == 1){
				if (($logged_user == $t_name['author']) AND ($permission_chk_edit == 1) AND ($permission_chk_del == 1)){
      				#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
					if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
						$postopt = '';
						$quickEditStatus = "";
					}else{ 
						$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
						$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
					}
				}elseif (($logged_user == $t_name['author']) AND ($permission_chk_edit == 1)){
					#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
					if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
						$postopt = '';
						$quickEditStatus = ""; 
					}else{
			  			$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			  			$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
					}
				}elseif (($logged_user == $t_name['author']) AND ($permission_chk_del == 1)){
					#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
					if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
						$postopt = '';
						$quickEditStatus = ""; 
					}else{
						$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
						$quickEditStatus = "";
					}
				}else{
			  		$postopt = '';
			  		$quickEditStatus = "";
				}
			}else{
		  		#default user permissions.
		   		if (($logged_user == $row['author']) AND ($edit_chk == 1) AND ($delete_chk == 1)){
	      	   		$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_post&bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";				
				}elseif (($logged_user == $row['author']) AND ($edit_chk == 1)){
	  				$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
	  				$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
				}elseif (($logged_user == $row['author']) AND ($delete_chk == 1)){
					$postopt = "$viewtopic[iplogged]&nbsp;<a href=\"delete.php?action=del_post&bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "";
				}else{
					$postopt = '';
					$quickEditStatus = "";
				}
			}
		}
		#output replies.
		$page = new template($template_path ."/replylisting.htm");
		$page->replace_tags(array(
		"POSTOPTIONS" => "$postopt",
		"LANG-POSTEDON" => "$viewtopic[postedon]",
		"POSTEDON" => "$post_date",
		"POSTID" => "$row[pid]",
		"AUTHOR" => "$row[author]",
		"CUSTOMTITLE" => "$customtitle",
		"RANKNAME" => "$rank",
		"RANKICON" => "$rankicon",
		"AVATAR" => "$avatar",
		"LANG-POSTCOUNT" => "$index[posts]",
		"POSTCOUNT" => "$re_user[Post_Count]",
		"WARNINGBAR" => "$warn_bar",
		"QUICKEDIT" => "$quickEditStatus",
		"LANG-QUICKEDIT" => "$edit[editpost]",
  		"LANG-CANCELEDIT" => "$viewtopic[canceledit]",
		"LANG-PROCESSINGEDIT" => "$viewtopic[processingedit]",
		"POSTBODY" => "$re_msg",
		"ATTACHMENTS"=> "$attachment",
		"SIGNATURE" => "$sig"));
		$page->output();
	}
}
#printable reply listing
function reply_listing_print(){

	global $query, $gmt, $time_format;
	$print_reply = '';
	while ($row = mysql_fetch_assoc ($query)) {
		//get date
		$gmttime = gmdate ($time_format, $row['Original_Date']);
		$post_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		//set bbcode for replies.
		$re_msg = $row['Body'];
		$re_msg = smiles($re_msg);
		$re_msg = BBCode_print($re_msg);
		$re_msg = language_filter($re_msg, 1);
		$re_msg = nl2br($re_msg);
		//output the replies.
		$print_reply .= "<hr><p class=\"td\">". $row['author'] . "&nbsp;-&nbsp;" . $post_date . "</p><p class=\"td\">" . $re_msg ."</p>";
	}
	return $print_reply;
}
#attachment manager.
function attach_manager($mode){
	global $template_path, $title, $mod, $txt, $userinfo, $pagenation, $attach, $search, $post, $uploads, $topic_q, $post_q, $attach_q, $bid, $db;
	#see if mode was left empty.
	if(!isset($mode)){
		die("Incorrect use of Function.");
	}
	#see how to manage the attachment.
	switch ($mode){
	case 'topic':
		#manager header.
		$page = new template($template_path ."/upload_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[manageattach]",
		"LANG-DELPROMPT" => "$mod[condel]",
		"LANG-FILENAME" => "$attach[filename]",
		"LANG-FILESIZE" => "$attach[filesize]"));
		$attachments = $page->output();
		#loop data.
		while ($manage_t = mysql_fetch_assoc ($topic_q)) {
	 		#get filesize in Kb.
	 		$file_size = ceil($manage_t['File_Size'] / 1024) . " Kb";
			#output results
			$page = new template($template_path ."/upload-manager.htm");
			$page->replace_tags(array(
			"ATTACHID" => "$manage_t[id]",
			"BID" => "$bid",
			"LANG-DELETE" => "$post[delattach]",
			"FILENAME" => "$manage_t[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$txt[closewindow]"));
		$attachments = $page->output(); 
	break;
	case 'post':
		#manager header.
		$page = new template($template_path ."/upload_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[manageattach]",
		"LANG-DELPROMPT" => "$mod[condel]",
		"LANG-FILENAME" => "$attach[filename]",
		"LANG-FILESIZE" => "$attach[filesize]"));
		$attachments = $page->output();
		#loop data.
		while ($manage_p = mysql_fetch_assoc ($post_q)) {
	 		#get filesize in Kb.
	 		$file_size = ceil($manage_p['File_Size'] / 1024) . " Kb";
			#output results
			$page = new template($template_path ."/upload-manager.htm");
			$page->replace_tags(array(
			"ATTACHID" => "$manage_p[id]",
			"BID" => "$bid",
			"LANG-DELETE" => "$post[delattach]",
			"FILENAME" => "$manage_p[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$txt[closewindow]"));
		$attachments = $page->output();
	break;
	case 'newentry':
		#manager header.
		$page = new template($template_path ."/upload_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$post[manageattach]",
		"LANG-DELPROMPT" => "$mod[condel]",
		"LANG-FILENAME" => "$attach[filename]",
		"LANG-FILESIZE" => "$attach[filesize]"));
		$attachments = $page->output();
		#loop data.
		while ($r = mysql_fetch_assoc ($uploads)) {
	 		#get filesize in Kb.
	 		$file_size = ceil($r['File_Size'] / 1024) . " Kb";
			#output results
			$page = new template($template_path ."/upload-manager.htm");
			$page->replace_tags(array(
			"ATTACHID" => "$r[id]",
			"BID" => "$bid",
			"LANG-DELETE" => "$post[delattach]",
			"FILENAME" => "$r[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$txt[closewindow]"));
		$attachments = $page->output();
	break;
	case 'profile':
		#attachment manager header.
		$page = new template($template_path ."/attachmanager_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$userinfo[title]",
		"LANG-MANAGEATTACHMENTS" => "$post[manageattach]",
		"LANG-DELPROMPT" => "$mod[condel]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$userinfo[attachmenttext]",
		"LANG-FILENAME" => "$attach[filename]",
		"LANG-FILESIZE" => "$attach[filesize]",
		"LANG-POSTEDIN" => "$search[postedin]"));
		$attachments = $page->output();
		while ($r = mysql_fetch_assoc ($attach_q)) {
	 		#get filesize in Kb.
	 		$file_size = ceil($r['File_Size'] / 1024) . " Kb";
	 		#get topic details.
			if($r['pid'] == 0){
	 			$db->run = "select Topic, bid from ebb_topics where tid='$r[tid]'";
				$attachdetails = $db->result();
				$db->close();
				#make query link.
				$link = "bid=$attachdetails[bid]&amp;tid=$r[tid]";
			}
			#get post details.
			if($r['tid'] == 0){
				$db->run = "select tid from ebb_posts where pid='$r[pid]'";
				$posttid = $db->result();
				$db->close(); 
				#get topic name.
				$db->run = "select Topic, bid from ebb_topics where tid='$posttid[tid]'";
				$attachdetails = $db->result();
				$db->close();
				#make query link.
				$link = "bid=$attachdetails[bid]&amp;tid=$posttid[tid]&pid=$r[pid]#post$r[pid]";
			}			
			#output results
			$page = new template($template_path ."/editattachments.htm");
			$page->replace_tags(array(
			"ATTACHID" => "$r[id]",
			"LANG-DELETE" => "$post[delattach]",
			"FILENAME" => "$r[Filename]",
			"FILESIZE" => "$file_size",
			"LANG-POSTEDIN" => "$search[postedin]",
			"POSTLINK" => "$link",
			"TOPICNAME" => "$attachdetails[Topic]"));
			$attachments = $page->output();
		}	
		#attachment manager footer.
		$page = new template($template_path ."/attachmanager_foot.htm");
		$attachments = $page->output();
	break;
	default:
		die($txt['invalidaction']); 
	}
	return ($attachments);
}
#view attachment function.
function attachment_stat($type, $user, $id){

	global $db, $attach;

	if($type == "topic"){
		#see if user attached a file.
		$db->run = "select id, Filename, File_Size, Download_Count from ebb_attachments where Username='$user' and tid='$id'";
		$attach_ct = $db->num_results();
		$attach_q = $db->query();
		$db->close();
		if($attach_ct > 0){
	 		$attachment = "<br /><div class=\"attachheader\">$attach[attachments]</div><div class=\"attachment\">";
			while ($r = mysql_fetch_assoc ($attach_q)) {
	 			#get filesize in Kb.
	 			$file_size = ceil($r['File_Size'] / 1024) . " Kb";
				#output results
				$attachment .= "<a href=\"download.php?id=$r[id]\">$r[Filename]</a> ($file_size) $attach[downloadct]: $r[Download_Count]<br />";
			}
			$attachment .= "</div>";	 
		}else{
			$attachment = ''; 
		}	
	}elseif($type == "post"){
		#see if user attached a file.
		$db->run = "select id, Filename, File_Size, Download_Count from ebb_attachments where Username='$user' and pid='$id'";
		$attach_ct = $db->num_results();
		$attach_q = $db->query();
		$db->close();
		if($attach_ct > 0){
	 		$attachment = "<br /><div class=\"attachheader\">$attach[attachments]</div><div class=\"attachment\">";
			while ($r = mysql_fetch_assoc ($attach_q)) {
	 			#get filesize in Kb.
	 			$file_size = ceil($r['File_Size'] / 1024) . " Kb";
				#output results
				$attachment .= "<a href=\"download.php?id=$r[id]\">$r[Filename]</a> ($file_size) $attach[downloadct]: $r[Download_Count]<br />";
			}
			$attachment .= "</div>";	 
		}else{
			$attachment = ''; 
		}	 
	}else{
	 	#function not called correctly.
		die($txt['invalidaction']); 
	}
	return ($attachment); 
}
#pm inbox function
function pm_inbox($folder){

	global $title, $menu, $pagenation, $settings, $pm, $num, $query, $time_format, $gmt, $template_path, $index;
	
	#see if folder name are valid.
	if (!isset($folder)){
		$error = $pm['invalidfolder'];
		echo error($error, "error");
	}
		
	#calculate percentage used from quota.
	if ($folder == "Inbox"){
		$percentageUsed = Round(($num / $settings['PM_Quota']) * 100);
		$pm_quota = $settings['PM_Quota'];
		$pm_lang_quota = $pm['pmquota'];
	}elseif ($folder == "Outbox"){
		$percentageUsed = '&#8734;';
		$pm_quota = '&#8734;';
		$pm_lang_quota = $pm['pmquota'];
	}elseif ($folder == "Archive"){
		$percentageUsed = Round(($num / $settings['Archive_Quota']) * 100);
		$pm_quota = $settings['Archive_Quota'];
		$pm_lang_quota = $pm['archivequota'];
	}else{
		$error = $pm['invalidfolder'];
		echo error($error, "error");	
	}
		
	#see if theres anything in the user's inbox.
	if ($num == 0){
		$page = new template($template_path ."/pm-inbox_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"PAGENATION" => "$pagenation",
		"LANG-VIEWBANLIST" => "$pm[banlist]",
		"LANG-POSTPM" => "$pm[postpmalt]",
		"LANG-PMRULE" => "$pm_lang_quota",
		"PMRULE" => "$pm_quota",
		"LANG-CURRENTAMOUNT" => "$pm[curquota]",
		"CURRENTAMOUNT" => "$percentageUsed",
		"LANG-INBOX" => "$pm[inbox]",
		"LANG-OUTBOX" => "$pm[outbox]",
		"LANG-ARCHIVE" => "$pm[archive]",
		"LANG-SUBJECT" => "$pm[subject]",
		"LANG-SENDER" => "$pm[sender]",
		"LANG-PMDATE" => "$pm[date]",
		"LANG-NOPM" => "$pm[nopm]"));
		$inbox = $page->output();
	}else{
	 	#pm inbox header.
		$page = new template($template_path ."/pm-inbox_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"PAGENATION" => "$pagenation",
		"LANG-VIEWBANLIST" => "$pm[banlist]",
		"LANG-POSTPM" => "$pm[postpmalt]",
		"LANG-PMRULE" => "$pm_lang_quota",
		"PMRULE" => "$pm_quota",
		"LANG-CURRENTAMOUNT" => "$pm[curquota]",
		"CURRENTAMOUNT" => "$percentageUsed",
		"LANG-INBOX" => "$pm[inbox]",
		"LANG-OUTBOX" => "$pm[outbox]",
		"LANG-ARCHIVE" => "$pm[archive]",
		"LANG-SUBJECT" => "$pm[subject]",
		"LANG-SENDER" => "$pm[sender]",
		"LANG-PMDATE" => "$pm[date]"));
		$inbox = $page->output();
		while ($row = mysql_fetch_assoc($query)) {
			$gmttime = gmdate ($time_format, $row['Date']);
			$pm_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
			if ($row['Read_Status'] == "old"){
				$icon = "$template_path/images/old.gif";
			}else{
				$icon = "$template_path/images/new.gif";
			}
			#pm inbox data.
			$page = new template($template_path ."/pm-inbox.htm");
			$page->replace_tags(array(
			"READICON" => "$icon",
			"PMID" => "$row[id]",
			"SUBJECT" => "$row[Subject]",
			"SENDER" => "$row[Sender]",
			"LANG-POSTEDBY" => "$index[Postedby]",
			"POSTDATE" => "$pm_date"));
			$inbox = $page->output();
		}
	#pm inbox footer.
	$page = new template($template_path ."/pm-inbox_foot.htm");
	$inbox = $page->output();	
	}

	return ($inbox);
}
#banlist
function view_banlist(){

	global $template_path, $title, $menu, $pm, $logged_user;

	$sql = "SELECT Banned_User, id FROM ebb_pm_banlist WHERE Ban_Creator='$logged_user'";
	$errorq = $sql;
	$banlistquery = mysql_query ($sql) or die(error($error, "mysql", $errorq));
	$ban_num = mysql_num_rows ($banlistquery);
	#no results found.
	if ($ban_num == "0"){
		$page = new template($template_path ."/pm-viewbanlist_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"LANG-BANLIST" => "$pm[banlisttitle]",
		"TEXT" => "$pm[text2]",
		"LANG-BANUSER" => "$pm[banusertitle]",
		"LANG-BANNEDUSER" => "$pm[banneduser]",
		"LANG-DELETE" => "$pm[del]",
		"LANG-NOBAN" => "$pm[noban]"));
		$banlist = $page->output();
	}else{
		#pm banlist header.
		$page = new template($template_path ."/pm-viewbanlist_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$menu[pm]",
		"LANG-BANLIST" => "$pm[banlisttitle]",
		"LANG-DELPROMPT" => "$pm[banlistconfirm]",
		"TEXT" => "$pm[text2]",
		"LANG-BANUSER" => "$pm[banusertitle]",
		"LANG-BANNEDUSER" => "$pm[banneduser]",
		"LANG-DELETE" => "$pm[del]"));
		$banlist = $page->output();
		#loop data.
		while ($row = mysql_fetch_assoc($banlistquery)) {
			$page = new template($template_path ."/pm-viewbanlist.htm");
			$page->replace_tags(array(
			"BANNEDUSER" => "$row[Banned_User]",
			"ID" => "$row[id]",
			"LANG-DELETEUSER" => "$pm[del]"));
			$banlist = $page->output();
		}
		#pm banlist footer.
		$page = new template($template_path ."/pm-viewbanlist_foot.htm");
		$banlist = $page->output();
	}
	return ($banlist);
}
?>
