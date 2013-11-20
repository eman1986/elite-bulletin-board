<?php
define('IN_EBB', true);
/**
 * index.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 11/04/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/
require "header.php";

// load board index data.
$bInx = loadBoardIndex();

$page = new \ebb\template($template_path);
echo $page->output("index", array(
    "TITLE" => $title,
    "PAGETITLE" => outputLanguageTag("index:title"),
    "TimeZone" => $gmt,
    'LANG_WELCOME'=> outputLanguageTag('common:loggedinas'),
    'LANG_WELCOMEGUEST' => outputLanguageTag('common:welcomeguest'),
    'LOGGEDUSER' => $logged_user,
    'LANG_JSDISABLED' => outputLanguageTag('common:jsdisabled'),
    'LANG_INFO' => outputLanguageTag('common:info'),
    'LANG_LOGIN' => outputLanguageTag('common:login'),
    'LANG_LOGOUT' => outputLanguageTag('common:logout'),
    //'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
    'LANG_USERNAME' => outputLanguageTag('common:username'),
    'LANG_REGISTER' => outputLanguageTag('common:register'),
    'LANG_PASSWORD' => outputLanguageTag('pass'),
    'LANG_FORGOT' => outputLanguageTag('forgot'),
    'LANG_REMEMBERTXT' => outputLanguageTag('remembertxt'),
    'LANG_QUICKSEARCH' => outputLanguageTag('quicksearch'),
    'LANG_SEARCH' => outputLanguageTag('search'),
    'LANG_ADVSEARCH' => outputLanguageTag('advsearch'),
    'LANG_PMS' => outputLanguageTag('pm'),
    'LANG_PMINBOX' => outputLanguageTag('inbox'),
    'LANG_PMARCHIVE' => outputLanguageTag('archive'),
    'UNREADPMCOUNT' => 0, //@TODO IMPLEMENT
    'LANG_UNREADPM' => outputLanguageTag('newpm'),
    'LANG_POSTPM' => outputLanguageTag('PostPM'),
    'LANG_CP' => outputLanguageTag('admincp'),
    'LANG_NEWPOSTS' => outputLanguageTag('newposts'),
    'LANG_HOME' => outputLanguageTag('home'),
    'LANG_HELP' => outputLanguageTag('help'),
    'LANG_MEMBERLIST' => outputLanguageTag('members'),
    'LANG_UOPTIONS' => outputLanguageTag('uoptions'),
    'LANG_CHANGETHEME' => outputLanguageTag('changetheme'),

    'LANG_BOARD' => outputLanguageTag('index:boards'),
    'LANG_TOPIC' => outputLanguageTag('index:topics'),
    'LANG_POST' => outputLanguageTag('index:posts'),
    'LANG_LASTPOSTDATE' => outputLanguageTag('index:lastposteddate'),
    "LANG_RSS" => outputLanguageTag('index:viewfeed'),
    'LANG_NOPOSTS' => outputLanguageTag('index:noposts'),
    'LANG_POSTEDBY' => outputLanguageTag('index:Postedby'),


    "CATEGORY" => $bInx['Parent_Boards'],
    "BOARDS" => $bInx['Child_Boards'],

    "LANG_WHOSONLINE" => outputLanguageTag("index:onlinekey"),
    "LANG_ONLINEKEY" => outputLanguageTag("index:onlinekey"),
    "LOGGED_ONLINE" => 0,
    "LANG_LOGGED_ONLINE" => outputLanguageTag("index:membernum"),
    "GUEST_ONLINE" => 0,
    "LANG_GUEST_ONLINE" => outputLanguageTag("index:guestonline"),
    "WHOSONLINE"=> "",
    "LANG_POWERED" => outputLanguageTag("common:poweredby")
));
//check to see if the install file is still on the user's server.
//$setupexist = checkinstall();
// if ($setupexist){
//    if ($access_level == 1){
//        $error = $lang['installadmin'];
//        echo error($error, "error");
//    }else{
//        $error = $lang['install'];
//        echo error($error, "general");
//        exit();
//    }
// }

//check to see if the board is on or off.
if ($board_status == 0){
	$offline_msg = nl2br($off_msg);
	$error = $offline_msg;
	if ($access_level == 1){
		$error .= "<p class=\"td\">[<a href=\"acp/index.php\">$lang[cp]</a>]</p>";
	}else{
		$error .= "<p class=\"td\">[<a href=\"login.php\">$lang[login]</a>]</p>";
	}
	echo error($error, "general");
	#terminate program after message appears.
	exit();
}

//detect new PMs.
//$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
//    $page = new \ebb\template("top-admin", $template_path);
//    $page->replace_tags(array(
//        "TITLE" => $title,
//        "LANG-WELCOME" => $lang['welcome'],
//        "LOGGEDUSER" => $logged_user,
//        "LANG-LOGOUT" => $lang['logout'],
//        "NEWPM" => $pm_msg,
//        "LANG-CP" => $lang['cp'],
//        "LANG-NEWPOSTS" => $lang['newposts'],
//        "ADDRESS" => $address,
//        "LANG-HOME" => $lang['home'],
//        "LANG-SEARCH" => $lang['search'],
//        "LANG-CLOSE" => $lang['closewindow'],
//        "LANG-QUICKSEARCH" => $lang['quicksearch'],
//        "LANG-ADVANCEDSEARCH" => $lang['advsearch'],
//        "LANG-FAQ" => $lang['faq'],
//        "LANG-MEMBERLIST" => $lang['members'],
//        "LANG-GROUPLIST"=> $lang['groups'],
//        "LANG-PROFILE" => $lang['profile']));
//    $page->output();
}
if ($access_level == 2 || $access_level == 3) {
//	$page = new \ebb\template("top-logged", $template_path);
//	$page->replace_tags(array(
//	"TITLE" => "$title",
//	"LANG-WELCOME" => "$lang[welcome]",
//	"LOGGEDUSER" => "$logged_user",
//	"LANG-LOGOUT" => "$lang[logout]",
//	"NEWPM" => "$pm_msg",
//	"LANG-NEWPOSTS" => "$lang[newposts]",
//	"ADDRESS" => "$address",
//	"LANG-HOME" => "$lang[home]",
//	"LANG-SEARCH" => "$lang[search]",
//	"LANG-CLOSE" => "$lang[closewindow]",
//	"LANG-QUICKSEARCH" => "$lang[quicksearch]",
//	"LANG-ADVANCEDSEARCH" => "$lang[advsearch]",
//	"LANG-FAQ" => "$lang[faq]",
//	"LANG-MEMBERLIST" => "$lang[members]",
//	"LANG-GROUPLIST"=> "$lang[groups]",
//	"LANG-PROFILE" => "$lang[profile]"));
//	$page->output();
}
if ($access_level == 0) {
//    $page = new \ebb\template("top-guest", $template_path);
//    $page->replace_tags(array(
//    "TITLE" => "$title",
//    "LANG-WELCOME" => "$lang[welcomeguest]",
//    "LANG-LOGIN" => "$lang[login]",
//    "LANG-REGISTER" => "$lang[register]",
//    "ADDRESS" => "$address",
//    "LANG-HOME" => "$lang[home]",
//    "LANG-SEARCH" => "$lang[search]",
//    "LANG-FAQ" => "$lang[faq]",
//    "LANG-MEMBERLIST" => "$lang[members]",
//    "LANG-GROUPLIST"=> "$lang[groups]",));
//    $page->output();
}

//@TODO rebuild this if we go jQuery.
//show announcement, if admins wants them on.
if ($boardPref->getPreferenceValue('infobox_status') == 1) {
//    $string = nl2p(smiles(BBCode("Hi TESTING!")));
//    //load template
//    $page = new \ebb\template("announcement", $template_path);
//    $page->replace_tags(array(
//    "TITLE" => "$title",
//    "LANG-ANNOUNCEMENT" => "$lang[announcements]",
//    "LANG-TICKER" => "$lang[ticker_txt]",
//    "ANNOUNCEMENT" => "$string"));
//    $page->output();
}
//#display board listings.
//$board_row = index_board();
//#get board stats.
////$b_stats = board_stats();
//$new_user = newuser();
////load board stat-icon
//$page = new \ebb\template("boardstat", $template_path);
//$page->replace_tags(array(
//  "LANG-BOARDSTAT" => "$lang[boardstatus]",
//  "LANG-ICONGUIDE" => "$lang[iconguide]",
//  "LANG-NEWESTMEMBER" => "$lang[newestmember]",
//  //"NEWESTMEMBER" => "$new_user[Username]",
//  //"TOTAL-TOPIC" => "$b_stats[1]",
//  "LANG-TOTALTOPIC" => "$lang[topics]",
//  //"TOTAL-POST" => "$b_stats[2]",
//  "LANG-TOTALPOST" => "$lang[posts]",
//  //"TOTAL-USER" => "$b_stats[0]",
//  "LANG-TOTALUSER" => "$lang[membernum]",
//  "LANG-NEWPOST" => "$lang[newpost]",
//  "LANG-OLDPOST" => "$lang[oldpost]"));
//$page->output();

//grab total online currently
//$db->run = "select DISTINCT Username from ebb_online where ip=''";
//$online_logged_count = $db->num_results();
//$db->close();
//$db->run = "select DISTINCT ip from ebb_online where Username=''";
//$online_guest_count = $db->num_results();
//$db->close();
//call the whos online function
//$online = whosonline();
//output who's online.

ob_end_flush();