 <?php
define('IN_EBB', true);
/**
 * index.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 12/05/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/
require "header.php";

// load board index data.
$bInx = loadBoardIndex();

//
$boardStats = getBoardStats();

$page = new \ebb\template($template_path);
echo $page->output("index", array(
    "TITLE" => $title,
    "PAGETITLE" => outputLanguageTag("index:title"),
    "TimeZone" => $gmt,
    'NOTIFY_TYPE' => get_flashdata('NotifyType'),
    'NOTIFY_MSG' => get_flashdata('NotifyMsg'),
    'LANG_WELCOME'=> outputLanguageTag('common:loggedinas'),
    'LANG_WELCOMEGUEST' => outputLanguageTag('common:welcomeguest'),
    'LOGGEDUSER' => $logged_user,
    'LANG_JSDISABLED' => outputLanguageTag('common:jsdisabled'),
    'LANG_INFO' => outputLanguageTag('common:info'),
    'LANG_LOGIN' => outputLanguageTag('common:login'),
    'LANG_LOGOUT' => outputLanguageTag('common:logout'),
    'LANG_USERNAME' => outputLanguageTag('common:username'),
    'LANG_REGISTER' => outputLanguageTag('common:register'),
    'LANG_PASSWORD' => outputLanguageTag('login:pass'),
    'LANG_FORGOT' => outputLanguageTag('login:forgot'),
    'LANG_EMAIL' => outputLanguageTag('user:email'),
    'LANG_REMEMBERTXT' => outputLanguageTag('login:remembertxt'),

    'LANG_QUICKSEARCH' => outputLanguageTag('search:quicksearch'),
    'LANG_SEARCH' => outputLanguageTag('menu:search'),
    'LANG_ADVSEARCH' => outputLanguageTag('search:advsearch'),
    'LANG_PMS' => outputLanguageTag('menu:pm'),
    'LANG_PMINBOX' => outputLanguageTag('pm:inbox'),
    'LANG_PMARCHIVE' => outputLanguageTag('pm:archive'),
    'UNREADPMCOUNT' => 0, //@TODO IMPLEMENT
    //'LANG_UNREADPM' => outputLanguageTag('newpm'),
    'LANG_POSTPM' => outputLanguageTag('pm:PostPM'),
    'LANG_CP' => outputLanguageTag('acp:admincp'),
    'LANG_NEWPOSTS' => outputLanguageTag('index:newposts'),
    'LANG_HOME' => outputLanguageTag('menu:home'),
    'LANG_MEMBERLIST' => outputLanguageTag('menu:members'),
    'LANG_UOPTIONS' => outputLanguageTag('menu:uoptions'),
    'LANG_CHANGETHEME' => outputLanguageTag('menu:changetheme'),
    'SETTINGS_ANNOUNCEMENTS' => $boardPref->getPreferenceValue("infobox_status"),
    'LANG_ANNOUNCEMENTS' => outputLanguageTag("index:announcements"),
    'ANNOUNCEMENTS' => GetAnnouncements(),
    'LANG_BOARD' => outputLanguageTag('index:boards'),
    'LANG_TOPIC' => outputLanguageTag('index:topics'),
    'LANG_POST' => outputLanguageTag('index:posts'),
    'LANG_LASTPOSTDATE' => outputLanguageTag('index:lastposteddate'),
    "LANG_RSS" => outputLanguageTag('index:viewfeed'),
    'LANG_NOPOSTS' => outputLanguageTag('index:noposts'),
    'LANG_POSTEDBY' => outputLanguageTag('index:Postedby'),
    "CATEGORY" => $bInx['Parent_Boards'],
    "BOARDS" => $bInx['Child_Boards'],
    "LANG_BOARDSTAT" => outputLanguageTag('index:boardstatus'),
    "LANG_ICONGUIDE" => outputLanguageTag('index:iconguide'),
    "LANG_NEWESTMEMBER" => outputLanguageTag('index:newestmember'),
    "LANG_TOTALTOPIC" => outputLanguageTag('index:topics'),
    "LANG_TOTALPOST" => outputLanguageTag('index:posts'),
    "LANG_TOTALUSER" => outputLanguageTag('index:membernum'),
    "USERCOUNT" => $boardStats['userCount'],
    "TOPICCOUNT" => $boardStats['topicCount'],
    "POSTCOUNT" => $boardStats['postCount'],
    "NEWESTUSER" => $boardStats['newUser'],
    "LANG_NEWPOST" => outputLanguageTag('index:newpost'),
    "LANG_OLDPOST" => outputLanguageTag('index:oldpost'),
    "LANG_WHOSONLINE" => outputLanguageTag("index:onlinekey"),
    "LANG_ONLINEKEY" => outputLanguageTag("index:onlinekey"),
    "LANG_WHOSONLINE" =>  sprintf(outputLanguageTag("index:currentlyonline"), $boardStats['userOnline'], $boardStats['guestOnline']),
    "WHOSONLINE"=> whosonline(),
    "LANG_POWERED" => outputLanguageTag("common:poweredby")
));
ob_end_flush();