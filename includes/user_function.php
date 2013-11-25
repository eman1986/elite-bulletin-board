<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: user_function.php
Last Modified: 11/25/2013

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
        $query = $db->prepare('SELECT count(tid) FROM ebb_topic_watch WHERE username=:username');
        $query->execute(array(":username" => $user));
        $count = $query->fetchColumn();

        //see if they want to subscribe or unsubscribe to a topic.
        if ($mode == "subscribe" && $count == 0) {
            $data = array(
                "username" => $user,
                "tid" => $tid,
                "read_status" => 0
            );
            $subscribe = $db->prepare('INSERT INTO ebb_topic_watch (username, tid, read_status) VALUES(:username, :tid, :read_status)');
            $subscribe->execute($data);
        } elseif ($mode == "unsubscribe" && $count > 0) {
            $unsubscribe = $db->prepare('DELETE FROM ebb_topic_watch WHERE tid=:tid AND username=:username');
            $unsubscribe->execute(array(":tid" => $tid, ":username" => $user));
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

/**
 * Update the online status of logged in users.
 * @param string $user The user we wish to update their online status.
 * @TODO refactor this to not be a separate process.
*/
function update_whosonline_reg($user) {

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
 * @TODO refactor this to not be a separate process.
*/
function update_whosonline_guest() {

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
function update_user($user) {

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
function detectProxy() {

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
        $query = $db->prepare("SELECT count(Read_Status) FROM ebb_pm  WHERE Reciever=:reciever AND Folder='Inbox' AND Read_Status=''");
        $query->execute(array(":reciever" => $user));

        if($query->fetchColumn() == 0){
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
