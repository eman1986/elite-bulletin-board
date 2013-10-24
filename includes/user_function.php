<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: user_function.php
Last Modified: 10/21/2013

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
        $query = $db->prepare('SELECT tid FROM ebb_topic_watch WHERE username=:username');
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

/**
 * Get the latest user that joined.
 * @return string
*/
function newuser() {
	global $db;

    try {
        //Get data
        $query = $db->query('SELECT Username FROM ebb_users ORDER BY Date_Joined DESC LIMIT 1');
        $result = $query->fetch(PDO::FETCH_OBJ);

        return $result->Username;
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        return NULL;
    }
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

/**
 * Update the online status of logged in users.
 * @param string $user The user we wish to update their online status.
*/
function update_whosonline_reg($user){

    global $db;

    try {
        //$query = $db->prepare('UPDATE ebb_users SET last_visit=:last_visit WHERE Username=:username');
        //$query->execute(array(":last_visit" => time(), ":username" => $user));

        $query = $db->prepare('SELECT Username FROM ebb_online WHERE Username=:username');
        $query->execute(array(":username" => $user));

        if ($query->rowCount() == 0) {
            $insertQ = $db->prepare('INSERT INTO  ebb_online (Username, time) VALUES(:username, :time)');
            $insertQ->execute(array(":username" => $user, ":time" => time()));
        } else {
            $updateQ = $db->prepare('UPDATE  ebb_online SET time=:time WHERE Username=:username');
            $updateQ->execute(array(":time" => time(), ":username" => $user));
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/**
 * Update the online status of guest accounts.
*/
function update_whosonline_guest(){

    global $db;

    try {
        $query = $db->prepare('SELECT ip FROM ebb_online WHERE ip=:ip');
        $query->execute(array(":ip" => detectProxy()));

        if ($query->rowCount() == 0) {
            $insertQ = $db->prepare('INSERT INTO  ebb_online (ip, time) VALUES(:ip, :time)');
            $insertQ->execute(array(":ip" => detectProxy(), ":time" => time()));
        } else {
            $updateQ = $db->prepare('UPDATE  ebb_online SET time=:time WHERE ip=:ip');
            $updateQ->execute(array(":time" => time(), ":ip" => detectProxy()));
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/**
 * Update the last post when creating a post.
 * @param string $user
*/
function update_user($user){

    global $db;

    try {
        $queryQ = $db->prepare('UPDATE  ebb_users SET last_post=:last_post WHERE Username=:username');
        $queryQ->execute(array(":last_post" => time(), ":username" => $user));
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
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
 * @param string $user
 * @return string 
*/
function DetectNewPM($user) {
    global $db, $menu;

    try {
        $query = $db->prepare("SELECT Read_Status FROM ebb_pm  WHERE Reciever=:reciever AND Folder='Inbox' AND Read_Status=''");
        $query->execute(array(":reciever" => $user));

        if($query->rowCount() == 0){
            return $menu['nonewpm'];
        }else{
            return $query->rowCount().$menu['newpm'];
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        return NULL;
    }
}
