<?php
/**
 * header.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 11/14/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/
session_start();

if (phpversion() < "5.3") {
    exit('Elite Bulletin Board Requires at least PHP 5.3.0');
}

//delete old session.
session_regenerate_id(TRUE);

if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}

if (phpversion() >= "5.4") {
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT); //to remove all DEPRECATED & STRICT STANDARDS errors in production for PHP 5.4 users.
} elseif (phpversion() >= "5.3") {
    error_reporting(E_ALL ^ E_DEPRECATED); //to remove all DEPRECATED errors in production for PHP 5.3 users.
}

//see if config file is already written.
if (filesize('config.php') == 0) {
    header("Location: ". (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" . $_SERVER["SERVER_NAME"] . "/install" : "http://" . $_SERVER["SERVER_NAME"] . "/install");
}

require_once "config.php";

//composer autoloader
require_once FULLPATH.'/includes/autoload.php';

//see if xdebug is installed
if (!function_exists("xdebug_get_code_coverage")) {
    $run = new \Whoops\Run;
    $errorPage = new \Whoops\Handler\PrettyPageHandler;
    $errorPage->setPageTitle("System Failure!");
    $run->pushHandler($errorPage);
    $run->register();
}

// setup our database object.
$options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);

try {
    $db = new \PDO(DB_DSN, DB_USER, DB_PASS, $options);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit( $e->getMessage());
}

//update online data.
$timeout = time() - 120;

//delete any old entries
$updateOnlineStatusQ = $db->prepare('DELETE FROM ebb_online WHERE time =:time');
$updateOnlineStatusQ->execute(array(":time" => $timeout));

//load our preference object.
$boardPref = new \ebb\preference($db);

//user check
if (isset($_COOKIE['ebbuser']) || isset($_SESSION['ebb_user'])) {
    //get username value.
    if (isset($_SESSION['ebb_user'])) {
        $ebbUserId = var_cleanup($_SESSION['ebb_user']);
        $loginKey = var_cleanup($_SESSION['ebbLoginKey']);
        $loginLastActive = var_cleanup($_SESSION['ebbLastActive']);
    } elseif(isset($_COOKIE['ebbuser'])) {
        $$ebbUserId = var_cleanup($_COOKIE['ebbuser']);
        $loginKey = var_cleanup($_SESSION['ebbLoginKey']);
        $loginLastActive = var_cleanup($_SESSION['ebbLastActive']);
    } else {
        exit('Invalid login!');
    }

    //start-up login checker.
    $userAuth = new \ebb\login($db);

    if ($userAuth->validateLoginSession($loginLastActive, $loginKey, $ebbUserId)) {
        //user is logged in.

        //get user info.
        $userInfo = new \ebb\user($db);

        //get group data.
        $groupData = new \ebb\groupPolicy($db);

        $userEntity = $userInfo->getUser($ebbUserId);


        //validate user is correct.
        if ($userEntity) {
            $groupEntity = $groupData->getGroupData($userInfo->getGid());

            if ($groupEntity) {
                $access_level = $groupData->getLevel();
            } else {
                throw new Exception('Invalid Group ID');
            }

            //set-up vars
            $logged_user = $userInfo->getUserName();
            //$stat = $userpref['Status']; //TODO: should replace with $access_level
            $template = $userInfo->getStyle();
            $time_format = $userInfo->getTimeFormat();
            $lng = $userInfo->getLanguage();
            $gmt = $userInfo->getTimeZone();
            $last_visit = $userInfo->getLastVisit();
            $suspend_length = $userInfo->getSuspendLength();
            $suspend_date = $userInfo->getSuspendTime();
        } else {
            throw new Exception('Invalid User');
        }

    } else {
        //@todo invalid login session, log user out.
    }
} else {
    $logged_user = 'guest';

    //get group data.
    $groupData = new \ebb\groupPolicy($db);
    $groupData->isGuest = TRUE;

    //get default values.
    $access_level = 0;
    $template = $boardPref->getPreferenceValue("default_style");
    $time_format = 0;//$boardPref->getPreferenceValue("time_format");
    $lng = $boardPref->getPreferenceValue("default_language");
    $gmt = $boardPref->getPreferenceValue("timezone");
    $last_visit = NULL;
    $suspend_length = NULL;
    $suspend_date = NULL;
}

//settings
$title = $boardPref->getPreferenceValue("board_name");
$address = $boardPref->getPreferenceValue("board_address");
$board_status = $boardPref->getPreferenceValue("board_status");
$board_email = $boardPref->getPreferenceValue("board_email");
$boardDir = $boardPref->getPreferenceValue("board_directory");

//get template path.
$template_path = theme($template);

//language loading
require_once FULLPATH."/lang/".$lng.".lang.php";