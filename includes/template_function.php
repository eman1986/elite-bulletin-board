<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
 * template_function.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 11/21/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/

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
 * Get a list of categories & boards to show on the index page.
 * @return array an array of both the parent boards & the child boards.
 */
function loadBoardIndex() {

    global $db,  $groupData, $time_format, $gmt;

    $parent = $child = array();

    try {
        //check against the database to see if the username  match.
        $categoryQ = $db->query("SELECT id, Board FROM ebb_boards WHERE type='1' ORDER BY B_Order");

        while($cat = $categoryQ->fetch(PDO::FETCH_OBJ)) {
            //board rules
            $parentAclQ = $db->prepare('SELECT B_Read FROM ebb_board_access WHERE B_id=:boardId');
            $parentAclQ->execute(array(":boardId" => $cat->id));
            $parentAclR = $parentAclQ->fetch(PDO::FETCH_OBJ);

            if ($groupData->validateAccess(0, $parentAclR->B_Read)) {
                $parent[] = $cat;

                //get child boards of the category.
                $childQ = $db->prepare("SELECT b.id, b.Board, b.Description, b.last_update, b.Post_Link, u.Username, b.last_update, b.Category
                                      FROM ebb_boards b
                                      LEFT JOIN ebb_users u ON b.Posted_User=u.Username
                                      WHERE b.type='2' AND b.Category=:category
                                      ORDER BY b.B_Order");
                $childQ->execute(array(":category" => $cat->id));
                while($board = $childQ->fetch(PDO::FETCH_OBJ)) {
                    //board rules
                    $boardAclQ = $db->prepare('SELECT B_Read FROM ebb_board_access WHERE B_id=:boardId');
                    $boardAclQ->execute(array(":boardId" => $board->id));
                    $boardAclR = $boardAclQ->fetch(PDO::FETCH_OBJ);
                    if ($groupData->validateAccess(0, $boardAclR->B_Read)) {
                        #get topic & post count from board.
                        $topicCountQ = $db->prepare('SELECT tid FROM ebb_topics WHERE bid=:boardId');
                        $topicCountQ->execute(array(":boardId" => $board->id));
                        $topicCountR = $topicCountQ->fetchAll(PDO::FETCH_OBJ);
                        $topicCount = number_format(count($topicCountR));

                        $postCountQ = $db->prepare('SELECT pid FROM ebb_posts WHERE bid=:boardId');
                        $postCountQ->execute(array(":boardId" => $board->id));
                        $postCountR = $postCountQ->fetchAll(PDO::FETCH_OBJ);
                        $postCount = number_format(count($postCountR));

                        $board->topicCount = $topicCount;
                        $board->postCount = $postCount;
                        $board->lastUpdate = empty($board->last_update) ? "": dateTimeFormatter($time_format, $board->last_update, $gmt);
                        $board->subBoards = getSubBoard($board->id);
                        //$board->moderators = moderator_boardlist($board->id);
                        $board->postIcon = isNewTopics($board->last_update, $board->Username, $board->Post_Link) ? "fa-comment" : "fa-comment-o";
                        $child[] = $board;
                    }
                }
            }
        }
        return array("Parent_Boards" => $parent, "Child_Boards" => $child);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/**
 * See if a board has new topics
 * @param $lastUpdate
 * @param $username
 * @param $tid
 * @return bool
*/
function isNewTopics($lastUpdate, $username, $tid) {

    global $logged_user;

    if ($lastUpdate < time()-3600*24*30) {
        return FALSE;
    } elseif ($logged_user == $username) {
        return FALSE;
    } elseif (readTopicStat($tid, $username) == 1) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
 * Will list all sub-boards linked to a parent board.
 * @param int $boardID - Board ID to search for any sub-boards.
 * @return string
*/
function getSubBoard($boardID) {

    global $db, $groupData;

    try {
        $childQ = $db->prepare("SELECT id, Board FROM ebb_boards b WHERE b.type='3' AND b.Category=:category ORDER BY b.B_Order");
        $childQ->execute(array(":category" => $boardID));
        $childR = $childQ->fetchAll();

        if (count($childR) == 0) {
            $subBoard = '';
        } else {
            $subBoard = outputLanguageTag('index:subboards').":&nbsp;";
            $counter = 0;
            foreach ($childR as $subBoardRow) {
                //see if we've reached the end of our query results.
                if (count($childR) == 1) {
                    $marker = '';
                } elseif($counter < count($childR) - 1) {
                    $marker = ',&nbsp;';
                } else {
                    $marker = '';
                }

                //board rules
                $subBoardAclQ = $db->prepare('SELECT B_Read FROM ebb_board_access WHERE B_id=:boardId');
                $subBoardAclQ->execute(array(":boardId" => $subBoardRow['id']));
                $subBoardAclR = $subBoardAclQ->fetch(PDO::FETCH_OBJ);

                //see if user can view the board.
                if ($groupData->validateAccess(0, $subBoardAclR->B_Read)) {
                    $subBoard .= sprintf('<em><a href="viewboard.php?bid=%d">%s</a></em>%s', $subBoardRow['id'], $subBoardRow['Board'], $marker);
                }

                //increment counter.
                $counter++;
            }
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
    return $subBoard;
}

/**
 * Displays a list of users & guest currently online.
 * @return string
*/
function whosonline() {

    global $db, $ebbUserId;

    $online = '';

    try {
        $getUsersOnlineQ = $db->query("SELECT u.Username FROM ebb_online o
                                       LEFT JOIN ebb_users u ON o.Username=u.id
                                       WHERE o.ip IS NULL");

        //get a new instance of the group & user object.
        $groupsonline = new \ebb\groupPolicy($db);
        $usersonline = new \ebb\user($db);

        while($onlineRow = $getUsersOnlineQ->fetch(PDO::FETCH_OBJ)) {
            #gain status of users
            $usersonline->getUser($ebbUserId);
            $groupsonline->getGroupData($usersonline->getGid());

            if ($groupsonline->getLevel() == 1) {
                $online .= sprintf('<strong><a href="Profile.php?action=viewprofile&amp;u=%s">%s</a></strong>&nbsp;', $onlineRow->Username);
            } elseif ($groupsonline->getLevel() == 2) {
                $online .= sprintf('<em><a href="Profile.php?action=viewprofile&amp;u=%s">%s</a></em>&nbsp;', $onlineRow->Username);
            } elseif($groupsonline->getLevel() == 3) {
                $online .= sprintf('<a href="Profile.php?action=viewprofile&amp;u=%s">%s</a>&nbsp;', $onlineRow->Username);
            } else {
                $online .= '&nbsp;';
            }
        }
        return $online;
    }  catch (PDOException $e) {
        return $e->getMessage();
    }
}

#group detail display
function display_group(){

	global $db, $template_path, $title;

	$db->run = "select id, Name from ebb_groups where Enrollment!='2'";
	$query = $db->query();
	$db->close();
	
	$grouplist = '';
	#grouplist header.
	$page = new template($template_path ."/grouplist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[title]"));
	$grouplist = $page->output();
	while ($row = mysql_fetch_assoc ($query)){
		$page = new template($template_path ."/grouplist.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[title]",
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

	global $id, $db, $gmt, $time_format, $template_path, $lang;

	$db->run = "select Username from ebb_group_users where gid='$id' and Status='Active'";
	$query = $db->query();
	$gnum = $db->num_results();
	$db->close();
	#list any members that are a part of this group.
	if ($gnum == 0){
		$page = new template($template_path ."/grouplist-viewusers_noresult.htm");
		$page->replace_tags(array(
		"LANG-GROUPMEMBERS" => "$lang[groupmembers]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-POSTCOUNT" => "$lang[posts]",
		"LANG-REGISTRATIONDATE" => "$lang[joindate]",
		"LANG-NOMEMBERS" => "$lang[nomembers]"));
		$groupmembers = $page->output();
	}else{
		#group userlist header.
		$page = new template($template_path ."/grouplist-viewusers_head.htm");
		$page->replace_tags(array(
		"LANG-GROUPMEMBERS" => "$lang[groupmembers]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-POSTCOUNT" => "$lang[posts]",
		"LANG-REGISTRATIONDATE" => "$lang[joindate]"));
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
			"LANG-PMALT" => "$lang[postpmalt]",
			"LANG-POSTCOUNT" => "$lang[posts]",
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

	global $title, $pagenation, $gmt, $query, $template_path, $time_format, $lang;

	#memberlist header.
	$page = new template($template_path ."/memberlist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[members]",
	"PAGENATION" => "$pagenation",
	"LANG-USERNAME" => "$lang[username]",
	"LANG-POSTCOUNT" => "$lang[posts]",
	"LANG-REGISTRATIONDATE" => "$lang[joindate]"));
	$memberlist = $page->output();
	while ($row = mysql_fetch_assoc ($query)){

		$gmttime = gmdate ($time_format, $row['Date_Joined']);
		$join_date = date($time_format,strtotime("$gmt hours",strtotime($gmttime)));
		#memberlist data.
		$page = new template($template_path ."/memberlist.htm");
		$page->replace_tags(array(
		"USERNAME" => "$row[Username]",
		"LANG-POSTCOUNT" => "$lang[posts]",
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

	global $title, $template_path, $pagenation, $search_result, $db, $num, $lang, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[search]",
	"LANG-SEARCHRESULTS" => "$lang[searchresults]",
	"PAGINATION" => "$pagenation",
	"NUM-RESULTS" => "$num",
	"LANG-RESULTS" => "$lang[result]",
	"LANG-USERNAME" => "$lang[author]",
	"LANG-TOPIC" => "$lang[topics]",
	"LANG-POSTEDIN" => "$lang[postedin]"));
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
			"LANG-POSTEDIN" => "$lang[postedin]",
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

	global $title, $template_path, $pagenation, $search_result, $db, $num, $lang, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[search]",
	"LANG-SEARCHRESULTS" => "$lang[searchresults]",
	"PAGINATION" => "$pagenation",
	"NUM-RESULTS" => "$num",
	"LANG-RESULTS" => "$lang[result]",
	"LANG-USERNAME" => "$lang[author]",
	"LANG-TOPIC" => "$lang[topics]",
	"LANG-POSTEDIN" => "$lang[postedin]"));
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
			"LANG-POSTEDIN" => "$lang[postedin]",
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

	global $title, $template_path, $search_results, $search_results2, $count, $logged_user, $db, $lang, $stat, $level_result;

	#search results header.
	$page = new template($template_path ."/searchresults_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[search]",
	"LANG-SEARCHRESULTS" => "$lang[searchresults]",
	"PAGINATION" => "",
	"NUM-RESULTS" => "$count",
	"LANG-RESULTS" => "$lang[result]",
	"LANG-USERNAME" => "$lang[author]",
	"LANG-TOPIC" => "$lang[topics]",
	"LANG-POSTEDIN" => "$lang[postedin]"));
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
				"LANG-POSTEDIN" => "$lang[postedin]",
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
				"LANG-POSTEDIN" => "$lang[postedin]",
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

	global $lang, $db;

	$db->run = "SELECT id, Board FROM ebb_boards where type='2' or type='3'";
	$board_search = $db->query();
	$db->close();
	$boardlist = "<select name=\"board\" class=\"text\" id=\"board\">
	<option value=\"\">$lang[selboard]</option>";
	while ($row = mysql_fetch_assoc ($board_search)){
		$boardlist .= "<option value=\"$row[id]\">$row[Board]</option>";
	}
	$boardlist .= "</select>";
	return ($boardlist);
}

/**
 * Builds our timezone select field.
 * @param string $tzone Our selected timezone
 * @return string
*/
function timezone_select($tzone="") {
    //array list of timezones supported by PHP.
    $tzoneList = array(
        "Pacific/Kwajalein" => "(GMT -12:00) Eniwetok, Kwajalein",
        "Pacific/Midway" => "(GMT -11:00) Midway Island, Samoa",
        "Pacific/Honolulu" => "(GMT -10:00) Honolulu, Hawaii",
        "America/Anchorage" => "(GMT -9:00) Anchorage, Alaska",
        "America/Tijuana" => "(GMT -8:00) Pacific Time (US &amp; Canada), Tijuana",
        "America/Phoenix" => "(GMT -7:00) Mountain Time (US &amp; Canada), Phoenix, Arizona",
        "America/Mexico_City" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City, Central America",
        "America/New_York" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima, New York",
        "America/Santiago" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz, Santiago",
        "Buenos_Aires" => "(GMT -3:00) Brasilia, Buenos Aires, Georgetown, Greenland",
        "Atlantic/St_Helena" => "(GMT -2:00) Mid-Atlantic, Ascension Islands, St. Helena",
        "Atlantic/Azores" => "(GMT -1:00) Azores, Cape Verde Islands",
        "Europe/London" => "(GMT) Casablanca, Dublin, Edinburgh, Lisbon, London, Monrovia",
        "Europe/Paris" => "(GMT +1:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome",
        "Africa/Cairo" => "(GMT +2:00) Cairo, Helsinki, Kaliningrad, South Africa",
        "Europe/Moscow" => "(GMT +3:00) Baghdad, Riyadh, Moscow, Nairobi",
        "Asia/Tehran" => "(GMT +3:30) Tehran",
        "Asia/Muscat" => "(GMT +4:00) Abu Dhabi, Baku, Muscat, Tbilii",
        "Asia/Kabul" => "(GMT +4:30) Kabul",
        "Asia/Yekaterinburg" => "(GMT +5:00) Yekaterinburg, Islamabad, Karachi, Tashkent",
        "Asia/Calcutta" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
        "Asia/Kathmandu" => "(GMT +5:45) Kathmandu",
        "Asia/Dhaka" => "(GMT +6:00) Almaty, Colombo, Dhaka, Novosibirsk, Sri Jayawardenepura",
        "Asia/Rangoon" => "(GMT +6:30) Rangoon",
        "Asia/Bangkok" => "(GMT +7:00) Bangkok, Hanoi, Jakarta, Krasnoyarsk",
        "China/Beijing" => "(GMT +8:00) Beijing, Hong Kong, Perth, Singapore, Taipei",
        "Asia/Tokyo" => "(GMT +9:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk",
        "Australia/Adelaide" => "(GMT +9:30) Adelaide, Darwin",
        "Australia/Sydney" => "(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok",
        "Asia/Magadan" => "(GMT +11:00) Magadan, New Caledonia, Solomon Islands",
        "Pacific/Fiji" => "(GMT +12:00) Auckland, Fiji, Kamchatka, Marshall Island, Wellington",
        "Pacific/Tongatapu" => "(GMT +13:00) Nuku' alofa"
    );

    $timezone = '<select name="time_zone" class="text">'."\n";

    foreach($tzoneList as $timezone => $text) {
        //see if one of these values are selected.
        if ($timezone == $tzone) {
            $timezone .= '<option value="'.$timezone.'" selected=selected>'.$text.'</option>'."\n";
        } else {
            $timezone .= '<option value="'.$timezone.'">'.$text.'</option>'."\n";
        }
    }
    $timezone .= '</select>';

    return $timezone;
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

	global $db, $logged_user, $lang, $title, $template_path;

	$db->run = "SELECT Name, id, Enrollment FROM ebb_groups";
	$joined_q = $db->query();
	$db->close();
	#grouplist CP header.
    $page = new template($template_path ."/editgrouplist_head.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[title]",
	"LANG-GROUPMANAGE" => "$lang[managegroups]",
	"LANG-TEXT" => "$lang[grouptxt]",
	"LANG-GROUPNAME" => "$lang[name]"));
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
				$group_status = $lang['pending'];
			}else{
				$group_status = "<a href=\"Profile?mode=unjoin_group&amp;id=$result[gid]\">[$lang[unjoingroup]]</a>";
			}
		}else{
		 	#See if a group is opened or locked or hidden.
			if ($row['Enrollment'] == 1){
				$group_status = "<a href=\"Profile?mode=join_group&amp;id=$row[id]\">[$lang[joingroup]]</a>";
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

	global $template_path, $title, $lang, $pagenation, $db, $logged_user, $sub_q, $num;

	if ($num == 0){
	 	#subscription no result output.
		$page = new template($template_path ."/editsubscription_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[title]",
		"LANG-EDITSUBSCRIPTION" => "$lang[subscriptionsetting]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$lang[digesttxt]",
		"LANG-SUBSCRIBED" => "$lang[scription]",
		"LANG-POSTEDIN" => "$lang[postedin]",
		"LANG-DELETE" => "$lang[delsubscription]",
		"LANG-NORESULT" => "$lang[nosubscription]"));
		$sub = $page->output();
	}else{
		#subscription header.
		$page = new template($template_path ."/editsubscription_head.htm");
		$page->replace_tags(array(
		"LANG-DELPROMPT" => "$lang[condel]",
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[title]",
		"LANG-EDITSUBSCRIPTION" => "$lang[subscriptionsetting]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$lang[digesttxt]",
		"LANG-SUBSCRIBED" => "$lang[scription]",
		"LANG-POSTEDIN" => "$lang[postedin]",
		"LANG-DELETE" => "$lang[delsubscription]"));
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
			"LANG-DELETE" => "$lang[del]",
			"LANG-POSTEDIN" => "$lang[postedin]",
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

	global $title, $rules, $lang, $pagenation, $posting, $board_policy, $db, $board_rule, $access_level, $stat, $num, $query, $logged_user, $bid, $template_path, $time_format, $level_result, $gmt;
	
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
		$boardmsg = $lang['noread'];
		$boarderr = 1;
	}elseif($num == 0){
		$boardmsg = $lang['nopost'];
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
		"LANG-BOARD" => "$lang[boards]",
		"LANG-TOPIC" => "$lang[topics]",
		"LANG-POST" => "$lang[posts]",
		"LANG-LASTPOSTDATE" => "$lang[lastposteddate]",
		"PAGENATION" => "$pagenation",
		"POST-RULE" => "$posting",
		"BOARD-POLICY" => "$board_policy",
		"LANG-TOPIC" => "$lang[topic]",
		"LANG-POSTEDBY" => "$lang[Postedby]",
		"LANG-REPLIES" => "$lang[replies]",
		"LANG-POSTVIEWS" => "$lang[views]",
		"LANG-LASTPOSTEDBY" => "$lang[lastpost]",
		"BOARDMSG" => "$boardmsg",
		"LANG-ICONGUIDE" => "$lang[iconguide]",
		"LANG-NEW" =>"$lang[newtopic]",
		"LANG-OLD" =>"$lang[oldtopic]",
		"LANG-POLL" =>"$lang[polltopic]",
		"LANG-LOCKED" =>"$lang[lockedtopic]",
		"LANG-IMPORTANT" => "$lang[importanttopic]",
		"LANG-HOTTOPIC" => "$lang[hottopic]"));
		$board = $page->output();	 
	}else{
		#viewboard header.
		$page = new template($template_path ."/viewboard_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$rules[Board]",
		"LANG-BOARD" => "$lang[boards]",
		"LANG-TOPIC" => "$lang[topics]",
		"LANG-POST" => "$lang[posts]",
		"LANG-LASTPOSTDATE" => "$lang[lastposteddate]",
		"PAGENATION" => "$pagenation",
		"POST-RULE" => "$posting",
		"LANG-TOPIC" => "$lang[topic]",
		"LANG-POSTEDBY" => "$lang[Postedby]",
		"LANG-REPLIES" => "$lang[replies]",
		"LANG-POSTVIEWS" => "$lang[views]",
		"LANG-LASTPOSTEDBY" => "$lang[lastpost]"));
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
				$attach_icon = "<img src=\"$template_path/images/attach_icon.gif\" alt=\"$lang[attachment]\" title=\"$lang[attachment]\" />";
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
			"LANG-REPLIES" => "$lang[repliedmsg]",
			"LANG-POSTVIEWS" => "$lang[views]",
			"REPLYCOUNT" => "$reply_num",
			"POSTVIEWS" => "$row[Views]",
			"TOPICDATE" => "$topic_date",
			"POSTLINK" => "$row[Post_Link]",
			"LANG-POSTEDUSER" => "$lang[Postedby]",
			"POSTEDUSER" => "$row[Posted_User]"));
			$board = $page->output();
		}
		#viewboard footer.
		$page = new template($template_path ."/viewboard_foot.htm");
		$page->replace_tags(array(
		"BOARD-POLICY" => "$board_policy",
		"LANG-ICONGUIDE" => "$lang[iconguide]",
		"LANG-NEW" =>"$lang[newtopic]",
		"LANG-OLD" =>"$lang[oldtopic]",
		"LANG-POLL" =>"$lang[polltopic]",
		"LANG-LOCKED" =>"$lang[lockedtopic]",
		"LANG-IMPORTANT" => "$lang[importanttopic]",
		"LANG-HOTTOPIC" => "$lang[hottopic]"));
		$board = $page->output();
	}
	return ($board);
}
#reply listing
function reply_listing(){

	global $db, $query, $gmt, $lang, $template_path, $allowsmile, $allowbbcode, $allowimg, $stat, $access_level, $logged_user, $time_format, $board_rule, $checkmod, $checkgroup, $permission_type, $t_name;

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
		$lang_chk = permission_check($board_rule['B_Edit']);
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
					$view_ip = "$lang[ipmod]&nbsp;<a href=\"manage.php?mode=viewip&amp;ip=$row[IP]&amp;u=$row[author]&amp;tid=$row[tid]&amp;bid=$row[bid]\">$row[IP]</a>";
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
						$postopt = "$lang[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
						$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
					}
				}elseif (($logged_user == $t_name['author']) AND ($permission_chk_edit == 1)){
					#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
					if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
						$postopt = '';
						$quickEditStatus = ""; 
					}else{
			  			$postopt = "$lang[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
			  			$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
					}
				}elseif (($logged_user == $t_name['author']) AND ($permission_chk_del == 1)){
					#see if user is a mod, if so dont allow them to see admin's IP or mofiy their post.
					if(($access_level == 2) and ($level_r['Level'] == 1) or ($permission_chk_vip == 0)){
						$postopt = '';
						$quickEditStatus = ""; 
					}else{
						$postopt = "$lang[iplogged]&nbsp;<a href=\"delete.php?action=del_post&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$bid&amp;tid=$tid&amp;type=1&amp;quser=$t_name[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
						$quickEditStatus = "";
					}
				}else{
			  		$postopt = '';
			  		$quickEditStatus = "";
				}
			}else{
		  		#default user permissions.
		   		if (($logged_user == $row['author']) AND ($edit_chk == 1) AND ($delete_chk == 1)){
	      	   		$postopt = "$lang[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"delete.php?action=del_post&bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
					$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";				
				}elseif (($logged_user == $row['author']) AND ($edit_chk == 1)){
	  				$postopt = "$lang[iplogged]&nbsp;<a href=\"edit.php?mode=editpost&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/edit.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
	  				$quickEditStatus = "<div align=\"right\"><a href=\"#post$row[pid]\" onclick=\"editor$row[pid].enterEditMode('click')\"><img src=\"$template_path/images/quick_edit.gif\" border=\"0\" alt=\"\" /></a></div>";
				}elseif (($logged_user == $row['author']) AND ($delete_chk == 1)){
					$postopt = "$lang[iplogged]&nbsp;<a href=\"delete.php?action=del_post&bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]\"><img src=\"$template_path/images/delete.gif\" border=\"0\" alt=\"\" /></a><a href=\"Post.php?mode=Reply&amp;bid=$row[bid]&amp;tid=$row[tid]&amp;pid=$row[pid]&amp;type=2&amp;quser=$row[author]\"><img src=\"$template_path/images/quote.gif\" border=\"0\" alt=\"\" /></a>";
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
		"LANG-POSTEDON" => "$lang[postedon]",
		"POSTEDON" => "$post_date",
		"POSTID" => "$row[pid]",
		"AUTHOR" => "$row[author]",
		"CUSTOMTITLE" => "$customtitle",
		"RANKNAME" => "$rank",
		"RANKICON" => "$rankicon",
		"AVATAR" => "$avatar",
		"LANG-POSTCOUNT" => "$lang[posts]",
		"POSTCOUNT" => "$re_user[Post_Count]",
		"WARNINGBAR" => "$warn_bar",
		"QUICKEDIT" => "$quickEditStatus",
		"LANG-QUICKEDIT" => "$lang[editpost]",
  		"LANG-CANCELEDIT" => "$lang[canceledit]",
		"LANG-PROCESSINGEDIT" => "$lang[processingedit]",
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
	global $template_path, $title, $pagenation, $lang, $uploads, $topic_q, $post_q, $attach_q, $bid, $db;
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
		"LANG-TITLE" => "$lang[manageattach]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-FILENAME" => "$lang[filename]",
		"LANG-FILESIZE" => "$lang[filesize]"));
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
			"LANG-DELETE" => "$lang[delattach]",
			"FILENAME" => "$manage_t[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$lang[closewindow]"));
		$attachments = $page->output(); 
	break;
	case 'post':
		#manager header.
		$page = new template($template_path ."/upload_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[manageattach]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-FILENAME" => "$lang[filename]",
		"LANG-FILESIZE" => "$lang[filesize]"));
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
			"LANG-DELETE" => "$lang[delattach]",
			"FILENAME" => "$manage_p[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$lang[closewindow]"));
		$attachments = $page->output();
	break;
	case 'newentry':
		#manager header.
		$page = new template($template_path ."/upload_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[manageattach]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-FILENAME" => "$lang[filename]",
		"LANG-FILESIZE" => "$lang[filesize]"));
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
			"LANG-DELETE" => "$lang[delattach]",
			"FILENAME" => "$r[Filename]",
			"FILESIZE" => "$file_size"));
			$attachments = $page->output();
		}
		#manager footer.
		$page = new template($template_path ."/upload_foot.htm");
		$page->replace_tags(array(
		"LANG-CLOSEWINDOW" => "$lang[closewindow]"));
		$attachments = $page->output();
	break;
	case 'profile':
		#attachment manager header.
		$page = new template($template_path ."/attachmanager_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[title]",
		"LANG-MANAGEATTACHMENTS" => "$lang[manageattach]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"PAGINATION" => "$pagenation",
		"LANG-TEXT" => "$lang[attachmenttext]",
		"LANG-FILENAME" => "$lang[filename]",
		"LANG-FILESIZE" => "$lang[filesize]",
		"LANG-POSTEDIN" => "$lang[postedin]"));
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
			"LANG-DELETE" => "$lang[delattach]",
			"FILENAME" => "$r[Filename]",
			"FILESIZE" => "$file_size",
			"LANG-POSTEDIN" => "$lang[postedin]",
			"POSTLINK" => "$link",
			"TOPICNAME" => "$attachdetails[Topic]"));
			$attachments = $page->output();
		}	
		#attachment manager footer.
		$page = new template($template_path ."/attachmanager_foot.htm");
		$attachments = $page->output();
	break;
	default:
		die($lang['invalidaction']);
	}
	return ($attachments);
}
#view attachment function.
function attachment_stat($type, $user, $id){

	global $db, $lang;

	if($type == "topic"){
		#see if user attached a file.
		$db->run = "select id, Filename, File_Size, Download_Count from ebb_attachments where Username='$user' and tid='$id'";
		$attach_ct = $db->num_results();
		$attach_q = $db->query();
		$db->close();
		if($attach_ct > 0){
	 		$attachment = "<br /><div class=\"attachheader\">$lang[attachments]</div><div class=\"attachment\">";
			while ($r = mysql_fetch_assoc ($attach_q)) {
	 			#get filesize in Kb.
	 			$file_size = ceil($r['File_Size'] / 1024) . " Kb";
				#output results
				$attachment .= "<a href=\"download.php?id=$r[id]\">$r[Filename]</a> ($file_size) $lang[downloadct]: $r[Download_Count]<br />";
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
	 		$attachment = "<br /><div class=\"attachheader\">$lang[attachments]</div><div class=\"attachment\">";
			while ($r = mysql_fetch_assoc ($attach_q)) {
	 			#get filesize in Kb.
	 			$file_size = ceil($r['File_Size'] / 1024) . " Kb";
				#output results
				$attachment .= "<a href=\"download.php?id=$r[id]\">$r[Filename]</a> ($file_size) $lang[downloadct]: $r[Download_Count]<br />";
			}
			$attachment .= "</div>";	 
		}else{
			$attachment = ''; 
		}	 
	}else{
	 	#function not called correctly.
		die($lang['invalidaction']);
	}
	return ($attachment); 
}
#pm inbox function
function pm_inbox($folder){

	global $title, $pagenation, $settings, $num, $query, $time_format, $gmt, $template_path, $lang;
	
	#see if folder name are valid.
	if (!isset($folder)){
		$error = $lang['invalidfolder'];
		echo error($error, "error");
	}
		
	#calculate percentage used from quota.
	if ($folder == "Inbox"){
		$percentageUsed = Round(($num / $settings['PM_Quota']) * 100);
		$pm_quota = $settings['PM_Quota'];
		$pm_lang_quota = $lang['pmquota'];
	}elseif ($folder == "Outbox"){
		$percentageUsed = '&#8734;';
		$pm_quota = '&#8734;';
		$pm_lang_quota = $lang['pmquota'];
	}elseif ($folder == "Archive"){
		$percentageUsed = Round(($num / $settings['Archive_Quota']) * 100);
		$pm_quota = $settings['Archive_Quota'];
		$pm_lang_quota = $lang['archivequota'];
	}else{
		$error = $lang['invalidfolder'];
		echo error($error, "error");	
	}
		
	#see if theres anything in the user's inbox.
	if ($num == 0){
		$page = new template($template_path ."/pm-inbox_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[pm]",
		"PAGENATION" => "$pagenation",
		"LANG-VIEWBANLIST" => "$lang[banlist]",
		"LANG-POSTPM" => "$lang[postpmalt]",
		"LANG-PMRULE" => "$pm_lang_quota",
		"PMRULE" => "$pm_quota",
		"LANG-CURRENTAMOUNT" => "$lang[curquota]",
		"CURRENTAMOUNT" => "$percentageUsed",
		"LANG-INBOX" => "$lang[inbox]",
		"LANG-OUTBOX" => "$lang[outbox]",
		"LANG-ARCHIVE" => "$lang[archive]",
		"LANG-SUBJECT" => "$lang[subject]",
		"LANG-SENDER" => "$lang[sender]",
		"LANG-PMDATE" => "$lang[date]",
		"LANG-NOPM" => "$lang[nopm]"));
		$inbox = $page->output();
	}else{
	 	#pm inbox header.
		$page = new template($template_path ."/pm-inbox_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[pm]",
		"PAGENATION" => "$pagenation",
		"LANG-VIEWBANLIST" => "$lang[banlist]",
		"LANG-POSTPM" => "$lang[postpmalt]",
		"LANG-PMRULE" => "$pm_lang_quota",
		"PMRULE" => "$pm_quota",
		"LANG-CURRENTAMOUNT" => "$lang[curquota]",
		"CURRENTAMOUNT" => "$percentageUsed",
		"LANG-INBOX" => "$lang[inbox]",
		"LANG-OUTBOX" => "$lang[outbox]",
		"LANG-ARCHIVE" => "$lang[archive]",
		"LANG-SUBJECT" => "$lang[subject]",
		"LANG-SENDER" => "$lang[sender]",
		"LANG-PMDATE" => "$lang[date]"));
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
			"LANG-POSTEDBY" => "$lang[Postedby]",
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

	global $template_path, $title, $lang, $logged_user;

	$sql = "SELECT Banned_User, id FROM ebb_pm_banlist WHERE Ban_Creator='$logged_user'";
	$errorq = $sql;
	$banlistquery = mysql_query ($sql) or die(error($error, "mysql", $errorq));
	$ban_num = mysql_num_rows ($banlistquery);
	#no results found.
	if ($ban_num == "0"){
		$page = new template($template_path ."/pm-viewbanlist_noresult.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[pm]",
		"LANG-BANLIST" => "$lang[banlisttitle]",
		"TEXT" => "$lang[text2]",
		"LANG-BANUSER" => "$lang[banusertitle]",
		"LANG-BANNEDUSER" => "$lang[banneduser]",
		"LANG-DELETE" => "$lang[del]",
		"LANG-NOBAN" => "$lang[noban]"));
		$banlist = $page->output();
	}else{
		#pm banlist header.
		$page = new template($template_path ."/pm-viewbanlist_head.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[pm]",
		"LANG-BANLIST" => "$lang[banlisttitle]",
		"LANG-DELPROMPT" => "$lang[banlistconfirm]",
		"TEXT" => "$lang[text2]",
		"LANG-BANUSER" => "$lang[banusertitle]",
		"LANG-BANNEDUSER" => "$lang[banneduser]",
		"LANG-DELETE" => "$lang[del]"));
		$banlist = $page->output();
		#loop data.
		while ($row = mysql_fetch_assoc($banlistquery)) {
			$page = new template($template_path ."/pm-viewbanlist.htm");
			$page->replace_tags(array(
			"BANNEDUSER" => "$row[Banned_User]",
			"ID" => "$row[id]",
			"LANG-DELETEUSER" => "$lang[del]"));
			$banlist = $page->output();
		}
		#pm banlist footer.
		$page = new template($template_path ."/pm-viewbanlist_foot.htm");
		$banlist = $page->output();
	}
	return ($banlist);
}