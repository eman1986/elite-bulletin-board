<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
 * template_function.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 11/25/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/

/**
 * Get template path.
 * @param int $id Theme ID
 * @return mixed
*/
function theme($id) {

    global $db;

    try {
        $query = $db->prepare('SELECT Temp_Path from ebb_style WHERE id=:id LIMIT 1');
        $query->execute(array(":id" => $id));
        $results = $query->fetch(PDO::FETCH_OBJ);

        return $results->Temp_Path;
    }
    catch (PDOException $e) {
        RETURN $e->getMessage();
    }
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
function lang_select($langsel) {

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