<?php
/**
 * header.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 12/29/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/
session_start();

if (PHP_VERSION_ID  < 50307) {
    exit('Elite Bulletin Board Requires at least PHP 5.3.7');
}

//delete old session.
session_regenerate_id(TRUE);

if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}

/*if (phpversion() >= "5.4") {
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT); //to remove all DEPRECATED & STRICT STANDARDS errors in production for PHP 5.4 users.
} elseif (phpversion() >= "5.3") {
    error_reporting(E_ALL ^ E_DEPRECATED); //to remove all DEPRECATED errors in production for PHP 5.3 users.
}*/

//see if config file is already written.
if (filesize('config.php') == 0) {
    header("Location: ". (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" . $_SERVER["SERVER_NAME"] . "/install" : "http://" . $_SERVER["SERVER_NAME"] . "/install");
}

require_once "config.php";

//composer autoloader
require_once FULLPATH.'/includes/autoload.php';

// setup our database object.
$options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);

try {
    $db = new \PDO(DB_DSN, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    $page = new \ebb\template();
    exit($page->output("error", array(
        "EXCEPTION_MESSAGE" => $e->getMessage(),
        "EXCEPTION_FILE" => $e->getFile(),
        "EXCEPTION_LINE" => $e->getLine()
    )));
}

//update online data.
$timeout = time() - 120;

//delete any old entries
$updateOnlineStatusQ = $db->prepare('DELETE FROM ebb_online WHERE time < :time');
$updateOnlineStatusQ->execute(array(":time" => $timeout));

//load our preference object.
$boardPref = new \ebb\preference($db);

//see if the IP of the user is banned.
$query = $this->db->prepare('SELECT COUNT(ban_ip) FROM ebb_banlist_ip WHERE ban_ip LIKE %:ban_ip%');
$query->execute(array(":ban_ip" => detectProxy()));
$banIpCount = $query->fetchColumn();

//output an error msg.
if ($banIpCount == 1) {
    error(outputLanguageTag('common:banned'), 'error', TRUE);
}

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
            $time_format = getDateTimeFormat($userInfo->getDateFormat(),$userInfo->getTimeFormat());
            $lng = $userInfo->getLanguage();
            $gmt = $userInfo->getTimeZone();
            $last_visit = $userInfo->getLastVisit();
            $suspend_length = $userInfo->getSuspendLength();
            $suspend_date = $userInfo->getSuspendTime();
        } else {
            throw new Exception('Invalid User');
        }

        //update user's online presence.
        update_whosonline_reg($logged_user);
    } else {
        //invalid login session, log user out.
        redirect("login.php?action=logout");
    }
} else {
    $logged_user = 'guest';

    //get group data.
    $groupData = new \ebb\groupPolicy($db);
    $groupData->isGuest = TRUE;

    //get default values.
    $access_level = 0;
    $template = $boardPref->getPreferenceValue("default_style");
    $time_format = getDateTimeFormat($boardPref->getPreferenceValue("dateformat"), $boardPref->getPreferenceValue("timeformat"));
    $lng = $boardPref->getPreferenceValue("default_language");
    $gmt = $boardPref->getPreferenceValue("timezone");
    $last_visit = NULL;
    $suspend_length = NULL;
    $suspend_date = NULL;

    //update user's online presence.
    update_whosonline_guest();
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