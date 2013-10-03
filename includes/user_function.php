<?php
/*
Filename: user_function.php
Last Modified: 10/02/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/


/**
 * Get Avatar from Gravatar Service.
 * @param string $eml The email to look up on gravatar.
 * @param string $size Setup the size of the avatar.
 * @param string $dImage Setup a default image.
 * @param string $rating Restrict the type of avatar to allow.
 * @return string URL to return an image.
 */
function getGravatar($eml, $size = "medium", $dImage = "gravatar", $rating = "ignore") {
    //base URL.
    $gravatarUrl = (!isSecure()) ? 'http://www.gravatar.com/avatar/' : 'https://www.gravatar.com/avatar/';

    //available sizes (gravatar allows 1-2048px, a preset of sizes is set to prevent abuse).
    $availableSizes = array(
        "tiny" => "16",
        "small" => "32",
        "medium" => "48",
        "large" => "64",
        "huge" => "128"
    );

    //available options.
    $defaultImage = array(
        "gravatar" => "",
        "404" => "404",
        "mystery-man" => "mm",
        "patterns" => "identicon",
        "monsters" => "monsterid",
        "faces" => "wavatar",
        "8bit" => "retro"
    );

    //the ratings filter.
    $ratings = array(
        "ignore" => "", //ignores the ratings rule.
        "all" => "g",
        "general" => "pg",
        "adult" => "r",
        "sexual" => "x"
    );

    //setup default value if incorrect value entered.
    if (!array_key_exists($size, $availableSizes)) {
        $size = "medium";
    }
    if (!array_key_exists($dImage, $defaultImage)) {
        $dImage = "gravatar";
    }
    if (!array_key_exists($rating, $ratings)) {
        $rating = "ignore";
    }

    //setup query string.
    if ($rating != "ignore") {
        $qsRating = 'r='.$ratings[$rating];
    } else {
        $qsRating = '';
    }
    if ($dImage == "gravatar") {
        $qsDefault = "d=";
    } else {
        $qsDefault = "d=".$defaultImage[$dImage];
    }
    $qsSize = 's='.$availableSizes[$size];

    return $gravatarUrl.md5(strtolower(trim($eml))).'?'.$qsRating.'&'.$qsSize.'&'.$qsDefault;
}

/**
 * Subscribe or unsubscribe from a topic.
 * @param string $user the user we want to this to affect.
 * @param integer $tid the Topic ID we want this to affect.
 * @param string $mode are we subscribing or unsubscribing?
 */
function subscriptionManager($user, $tid, $mode) {

    global $db;

    try {
        //check against the database to see if the username  match.
        $query = $db->prepare('SELECT tid from ebb_topic_watch WHERE username=:username');
        $query->execute(array(":username" => $user));

        //see if they want to subscribe or unsubscribe to a topic.
        if ($mode == "subscribe" && $query->rowCount() == 0) {
            $data = array(
                "username" => $user,
                "tid" => $tid,
                "read_status" => 0
            );
            $query = $db->prepare('INSERT INTO ebb_topic_watch (username, tid, read_status) VALUES(:username, :tid, :read_status)');
            $query->execute($data);
        } elseif ($mode == "unsubscribe" && $query->rowCount() > 0) {
            $query = $db->prepare('DELETE FROM ebb_topic_watch WHERE tid=:tid AND username=:username');
            $query->execute(array(":tid" => $tid, ":username" => $user));
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

#output last new user.
function newuser(){

	global $db;

	$db->run = "select Username from ebb_users where active='1' ORDER BY Date_Joined DESC LIMIT 1";
	$new_user = $db->result();
	$db->close();

	return ($new_user);
}
#group checker.
function group_validate($bid, $gid, $type){

global $db, $txt, $cp;

	#see if Board ID was declared, if not terminate any further outputting.
	if((!isset($bid)) or (empty($bid))){
		die($txt['nobid']);
	}
	#see if Group ID was declared, if not terminate any further outputting.
	if((!isset($gid)) or (empty($gid))){
		die($txt['nogid']);
	}
	#see if Group Type was declared, if not terminate any further outputting.
	if((!isset($type)) or (empty($type))){
		die($cp['nogroupsel']);
	}
	//check to see if this user is able to access this area.
	$db->run = "select type from ebb_grouplist WHERE board_id='$bid' and group_id='$gid' and type='$type'";
	$checkgroup = $db->num_results();
	$db->close();

	return ($checkgroup);
}
#permission checker.
function permission_check($action){

	global $access_level, $checkgroup, $checkmod, $txt;

	#see if action variable was used.(!isset($action)) or(DEBUG PURPOSES) 
	//if($action == ""){
	//	die($txt['invalidaction']);	
	//}
	#check permission.
	if($checkmod == 1){
		$permission_chk = 1;	
	}else{	
		if(($action == 1) AND ($access_level == 1)){
			$permission_chk = 1;
		}elseif(($action == 2) AND ($access_level == 1) or ($access_level == 2)){
			$permission_chk = 1;
		}elseif(($action == 3) AND ($access_level == 3) or ($access_level == 2) or ($access_level == 1)){
			$permission_chk = 1;	
		}elseif($action == 4){
			$permission_chk = 0;
		}elseif(($action == 5) and ($checkgroup == 1) or ($access_level == 1) or ($checkmod == 1)){
			$permission_chk = 1;
		}elseif($action == 0){
			$permission_chk = 1;	
		}else{
			$permission_chk = 0;
		}
	}
	return($permission_chk);
}
#group access controller.
function access_vaildator($permission_profile, $permission_action){
	
	global $db, $txt, $stat;
	
	#first lets make sure the permission profile used is valid.
	$db->run = "SELECT id FROM ebb_permission_profile WHERE id='$permission_profile'";
	$permission_profile_chk = $db->num_results();
	$db->close();
	
	#see if user ID is incorrect or Null.
	if(($permission_profile_chk == 0) and ($stat !== "guest")){
		die($txt['invalidprofile']);
	}
	
	#lets also check to make sure the action requested is valid.
	$db->run = "SELECT id FROM ebb_permission_actions WHERE id='$permission_action'";
	$permission_action_chk = $db->num_results();
	$db->close();
	if($permission_action_chk == 0){
		die($txt['invalidaction']);
	}
	
	#see if user has correct permission to access requested permission.
	$db->run = "SELECT set_value FROM ebb_permission_data WHERE profile='$permission_profile' and permission='$permission_action'";
	$validate_permission = $db->result();
	$db->close();

	#output value in script.
	return ($validate_permission['set_value']);
}
#user's warning level bar.
function user_warn($user){

	global $db, $checkmod, $access_level, $level_r, $template_path, $cp, $viewtopic, $tid, $bid, $logged_user, $permission_chk_warn;
	
	#see if user variable is blank.
	if(!isset($user) || empty($user)){
		echo $cp['nousernameentered'];
		exit();
	}else{
		#get user's warning level.
		$db->run = "SELECT warning_level FROM ebb_users WHERE Username='$user'";
		$warn_r = $db->result();
		$user_chk = $db->num_results();
		$db->close();
		#see if username placed in the function is invalid.
		if($user_chk == 0){
			echo $cp['nousernameentered'];
			exit();
		}
		#see if user has moderator status, if so let them alter warning level.
		if($checkmod == 1){
			#see if user they wish to warn is higher in rank than them, if so don't let them set anything.
			if(($access_level == 2) and ($level_r['Level'] == 1)){
				$warn_bar = "<div class=\"warningheader\">$viewtopic[warnlevel]</div><div class=\"warnlevel\"><img src=\"$template_path/images/bar.gif\" alt=\"$viewtopic[warnlevel]\" height=\"10\" width=\"$warn_r[warning_level]\" />&nbsp;($warn_r[warning_level]%)</div>";
			}else{
				#see if user has permission to alter warning value.
				if($permission_chk_warn == 1){
					$warn_bar = "<div class=\"warningheader\">$viewtopic[warnlevel]</div><div class=\"warnlevel\"><img src=\"$template_path/images/bar.gif\" alt=\"$viewtopic[warnlevel]\" height=\"12\" width=\"$warn_r[warning_level]\" />&nbsp;(<a href=\"manage.php?mode=warn&amp;user=$user&amp;bid=$bid&amp;tid=$tid\">$warn_r[warning_level]%</a>)</div>";
				}else{
					$warn_bar = "<div class=\"warningheader\">$viewtopic[warnlevel]</div><div class=\"warnlevel\"><img src=\"$template_path/images/bar.gif\" alt=\"$viewtopic[warnlevel]\" height=\"10\" width=\"$warn_r[warning_level]\" />&nbsp;($warn_r[warning_level]%)</div>";
				}
			}
		}else{
			#see if user is the actual user.
			if($user == $logged_user){
				$warn_bar = "<div class=\"warningheader\">$viewtopic[warnlevel]</div><div class=\"warnlevel\"><img src=\"$template_path/images/bar.gif\" alt=\"$viewtopic[warnlevel]\" height=\"10\" width=\"$warn_r[warning_level]\" />&nbsp;($warn_r[warning_level]%)</div>";
			}else{
				$warn_bar = '';
			}
		}
	}
	return($warn_bar);
}

#get latest 5 replies made by selected user.
function latestTopics($user){

	global $db, $stat, $level_result;

	$db->run = "select tid, bid, author, Topic, Body, Original_Date FROM ebb_topics where author='$user' Order By Original_Date DESC limit 5";
	$topic_query = $db->query();
	$db->close();
	
	#prime var.
	$LatestTopics = '';
	while($topic = mysql_fetch_array($topic_query)) {

		//check for the posting rule.
		$db->run = "select B_Read from ebb_board_access WHERE B_id='$topic[bid]'";
		$board_rule = $db->result();
		$db->close();
		if(($stat == "guest") or ($stat == "Member")){
			#guest has no group rights.
			$checkgroup = 0;
			$checkmod = 0;
		}else{
			#get group access information.
			$checkgroup = group_validate($topic['bid'], $level_result['id'], 2);
			$checkmod = group_validate($topic['bid'], $level_result['id'], 1);
		}
		//see if the user can access this spot.
		$read_chk = permission_check($board_rule['B_Read']);
		if ($read_chk == 1){
		
			#if body is over 100 characters, cut it off.
			if(strlen($topic['Body']) > 200){
				$topicBody = substr_replace(var_cleanup($topic['Body']),'[...]',100);
			}else{
				$topicBody = $topic['Body'];
			}
			
			#output topics
			$LatestTopics .= "<b>$topic[Topic]</b><br />$topicBody<hr />";
		}
	}
	return ($LatestTopics);	
}

#get latest 5 replies made by selected user.
function latestPosts($user){
	global $db, $stat, $level_result;

	$db->run = "select author, pid, tid, bid, Body, Original_Date from ebb_posts where author='$user' Order By Original_Date DESC LIMIT 10";
	$post_query = $db->query();
	$db->close();
	
	#prime var.
	$LatestPosts = '';
	while($post = mysql_fetch_array($post_query)) {

		//check for the posting rule.
		$db->run = "select B_Read from ebb_board_access WHERE B_id='$post[bid]'";
		$board_rule = $db->result();
		$db->close();
		if(($stat == "guest") or ($stat == "Member")){
		  #guest has no group rights.
			$checkgroup = 0;
			$checkmod = 0;
		}else{
			#get group access information.
			$checkgroup = group_validate($post['bid'], $level_result['id'], 2);
			$checkmod = group_validate($post['bid'], $level_result['id'], 1);
		}
		//see if the user can access this spot.
		$read_chk = permission_check($board_rule['B_Read']);
		if ($read_chk == 1){
		
			#if body is over 100 characters, cut it off.
			if(strlen($post['Body']) > 200){
				$postBody = substr_replace(var_cleanup($post['Body']),'[...]',100);
			}else{
				$postBody = var_cleanup($post['Body']);
			}
		
			#get topic details.
			$db->run = "SELECT Topic FROM ebb_topics where tid='$post[tid]'";
			$topic_r = $db->result();
			$db->close();
		
			#output topics
			$LatestPosts .= "<b>$topic_r[Topic]</b><br />$postBody<hr />";
	}
	}
	return ($LatestPosts);	
}

#update user's online status
function update_whosonline_reg($string){

	global $db;

	//update the user's last active status.
	$time = time();
	$db->run = "update ebb_users SET last_visit='$time' where Username='$string'";
	$db->query();
	$db->close();
	//check to see if user is marked as online, if not mark them as online.
	$db->run = "select Username from ebb_online where Username='$string'";
	$count_member = $db->num_results();
	$db->close();
	if ($count_member == 0){
		//user seems to be just getting on.
		$db->run = "insert into ebb_online (Username, time, location) values('$string', '$time', '".var_cleanup($_SERVER['PHP_SELF'])."')";
		$db->query();
		$db->close();
	}else{
		//user is still here so lets up their time to let the script know the user is still around.
		$db->run = "update ebb_online Set time='".var_cleanup($time)."', location='".var_cleanup($_SERVER['PHP_SELF'])."' where Username='$string'";
		$db->query();
		$db->close();
	}
}
#update guest's last activre status.
function update_whosonline_guest(){

	global $db;

	$time = time();
	$ip = var_cleanup($_SERVER["REMOTE_ADDR"]);
	$db->run = "select Username from ebb_online where ip='$ip'";
	$count_guest = $db->num_results();
	$db->close();
	if ($count_guest == 0){
		$db->run = "insert into ebb_online (ip, time, location) values('$ip', '$time', '".var_cleanup($_SERVER['PHP_SELF'])."')";
		$db->query();
		$db->close();
	}else{
		//user is still here so lets up their time to let the script know the user is still around.
		$db->run = "update ebb_online Set time='$time', location='".var_cleanup($_SERVER['PHP_SELF'])."' where ip='$ip'";
		$db->query();
		$db->close();
	}
}
#update user
function update_user($user){

	global $db, $time;

	//update user's last post.
	$db->run = "Update ebb_users SET last_post='$time' WHERE Username='$user'";
	$db->query();
	$db->close(); 
}

/**
 * sniffs out real IP Address.
 * @return mixed
*/
function detectProxy(){

    $ip_sources = array(
        "HTTP_CLIENT_IP",
        "HTTP_X_FORWARDED_FOR",
        "HTTP_X_FORWARDED",
        "HTTP_FORWARDED_FOR",
        "HTTP_FORWARDED",
        "HTTP_X_COMING_FROM",
        "HTTP_COMING_FROM",
        "REMOTE_ADDR");

    foreach ($ip_sources as $ip_source){
        // If the ip source exists, capture it
        if (isset($_SERVER[$ip_source])) {
            $proxy_ip = $_SERVER[$ip_source];
        break;
        }
    }

    //if all else fails, just set a false value.
    $proxy_ip = (isset($proxy_ip)) ? $proxy_ip : $_SERVER["REMOTE_ADDR"];

    // Return the IP
    return $proxy_ip;
}

/**
 * Detects new PMs in user's Inbox.
 * @version 2/8/12
 * @global db $db
 * @global string $menu
 * @param string $user
 * @return string 
 */
function DetectNewPM($user) {
    global $db, $menu;
    
    #total of new PM messages.
    $db->run = "select Read_Status from ebb_pm WHERE Reciever='$user' and Folder='Inbox' and Read_Status=''";
    $new_pm = $db->num_results();
    $db->close();
    if($new_pm == 0){
        return $menu['nonewpm'];
    }else{
        return $new_pm.$menu['newpm'];
	}
}
