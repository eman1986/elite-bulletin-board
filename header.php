<?php
session_start();

if (phpversion() < "5.3") {
    exit('Elite Bulletin Board Requires at least PHP 5.3.0');
}

//delete old session.
session_regenerate_id(true);

if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: header.php
Last Modified: 10/02/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
if (phpversion() >= "5.4") {
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT); //to remove all DEPRECATED & STRICT STANDARDS errors in production for PHP 5.4 users.
}elseif (phpversion() >= "5.3") {
    error_reporting(E_ALL ^ E_DEPRECATED); //to remove all DEPRECATED errors in production for PHP 5.3 users.
}

#see if config file is already written.
$config_path = 'config.php';
$file_size = filesize($config_path);
if($file_size == 0){
    header("Location: ". isSecure() ? "https://" : "http://" . $_SERVER["SERVER_NAME"] . "/install");
}

require_once "config.php";

//load exception handler library.
require_once FULLPATH.'/includes/Whoops/Run.php';
require_once FULLPATH.'/includes/Whoops/Handler/HandlerInterface.php';
require_once FULLPATH.'/includes/Whoops/Handler/Handler.php';
require_once FULLPATH.'/includes/Whoops/Handler/PrettyPageHandler.php';
require_once FULLPATH.'/includes/Whoops/Handler/JsonResponseHandler.php';
require_once FULLPATH.'/includes/Whoops/Exception/ErrorException.php';
require_once FULLPATH.'/includes/Whoops/Exception/Inspector.php';
require_once FULLPATH.'/includes/Whoops/Exception/Frame.php';
require_once FULLPATH.'/includes/Whoops/Exception/FrameCollection.php';

$run = new \Whoops\Run;
$errorPage = new \Whoops\Handler\PrettyPageHandler;

$errorPage->setPageTitle("System Failure!");

//$JsonHandler = new \Whoops\Handler\JsonResponseHandler;

//$run->pushHandler($JsonHandler);
$run->pushHandler($errorPage);
$run->register();


#load functions & classes
require_once FULLPATH."/includes/template.php";
require_once FULLPATH."/includes/function.php";
require_once FULLPATH."/includes/template_function.php";
require_once FULLPATH."/includes/posting_function.php";
require_once FULLPATH."/includes/user_function.php";

require_once FULLPATH."/includes/login.class.php";
require_once FULLPATH."/includes/user.class.php";

// setup our database object.
$options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);

try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS, $options);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//update online data.
$timeout = time() - 120;

//delete any old entries
$db->exec('DELETE FROM ebb_online WHERE time = $timeout');

#user check
if (isset($_COOKIE['ebbuser']) || isset($_SESSION['ebb_user'])) {
    #get username value.
    if (isset($_SESSION['ebb_user'])) {
        $ebbUserId = var_cleanup($_SESSION['ebb_user']);
        $loginKey = var_cleanup($_SESSION['ebbLoginKey']);
        $loginLastActive = var_cleanup($_SESSION['ebbLastActive']);
    } elseif(isset($_COOKIE['ebbuser'])) {
        $$ebbUserId = var_cleanup($_COOKIE['ebbuser']);//$logged_user
        $loginKey = var_cleanup($_SESSION['ebbLoginKey']);
        $loginLastActive = var_cleanup($_SESSION['ebbLastActive']);
    } else {
        exit('Invalid login!');
    }

    #start-up login checker.
    $userAuth = new login();

    if ($userAuth->validateLoginSession($loginLastActive, $loginKey, $ebbUserId)) {
        //user is logged in.

        //get user info.
        $userInfo = new user($db);

        $userEntity = $userInfo->getUser($ebbUserId);

        //validate user is correct.
        if ($userEntity) {
            #set-up vars
            $logged_user = $userInfo->getUserName();
            $stat = $userpref['Status'];
            $template = $userInfo->getStyle();
            $time_format = $userInfo->getTimeFormat();
            $lang = $userInfo->getLanguage();
            $gmt = $userInfo->getTimeZone();
            $last_visit = $userInfo->getLastVisit();
            $suspend_length = $userInfo->getSuspendLength();
            $suspend_date = $userInfo->getSuspendTime();
        } else {
            throw new Exception('Invalid User');
        }

    } else {
        //not logged in.
    }

    if($chk_user == 1){
        #set the columns needed for now.
        $columes = 'Status, Style, Time_format, Language, Time_Zone, last_visit, suspend_length, suspend_time';
        #call user function.
        $userpref = user_settings($logged_user, $columes);

        //check to see if user is part of a group.
        if ($stat == "groupmember"){
            $db->run = "SELECT gid FROM ebb_group_users where Username='$logged_user' AND Status='Active'";
            $groupuser = $db->result();
            $group_auth_chk = $db->num_results();
            $db->close();
            if ($group_auth_chk == 1){
                $db->run = "SELECT id, Name, Level, permission_type FROM ebb_groups where id='$groupuser[gid]'";
                $level_result = $db->result();
                $db->close();
                #set-up vars
                $access_level = $level_result['Level'];
                $permission_type = $level_result['permission_type'];
            }else{
                die('INVALID GROUP ID');
            }
        }elseif($stat == "Member"){
            $level_result = 3;
            $access_level = 3;
            $permission_type = 4;
        }else{
            $logged_user = '';
            $level_result = 0;
            $access_level = 0;
            $permission_type = 0;
        }
    }else{
        $error = "INVALID COOKIE OR SESSION!";
        echo error($error, "error");
    }
}else{
    $stat = "guest";
    $access_level = 0;
    $logged_user = '';
    $level_result['Level'] = 0;
    $level_result['id'] = 0;
    $permission_type = 0;
    #call board setting function.
    $colume = 'Default_Style, Default_Language, Default_Time, Default_Zone, activation';
    $settings = board_settings($colume);
    #set-up vars
    $template = $settings['Default_Style'];
    $lang = $settings['Default_Language'];
    $time_format = $settings['Default_Time'];
    $gmt = $settings['Default_Zone'];
    $active_type = $settings['activation'];
}
#call board setting function.
$colume = 'Site_Title, Site_Address, Board_Address, Board_Status, Board_Email, Off_Message';
$settings = board_settings($colume);
#settings
$title = $settings['Site_Title'];
$address = $settings['Site_Address'];
$board_address = $settings['Board_Address'];
$board_status = $settings['Board_Status'];
$board_email = $settings["Board_Email"];
$off_msg = $settings["Off_Message"];

#template
$theme = theme($template);

#set-up vars
$template_path = $theme['Temp_Path'];

#language loading
require FULLPATH."/lang/".$lang.".lang.php";