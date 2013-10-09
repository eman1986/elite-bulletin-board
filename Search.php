<?php
define('IN_EBB', true);
/*
Filename: Search.php
Last Modified: 2/9/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";

$page = new template($template_path ."/header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$menu[search]",
  "LANG-HELP-TITLE" => "$help[searchtitle]",
  "LANG-HELP-BODY" => "$help[searchbody]"));

$page->output();
#see if user can access this portion of the site.
$permission_chk_search = access_vaildator($permission_type, 28);
if($permission_chk_search == 0){
	$error = $txt['accessdenied'];
	echo error($error, "error"); 		
} 
//check to see if the install file is stil on the user's server.
$setupexist = checkinstall();
if ($setupexist == 1){
	if ($access_level == 1){
		$error = $txt['installadmin'];
		echo error($error, "error");
	}else{
		$error = $txt['install'];
		echo error($error, "general");
	}
}
//check to see if this user is able to access this board.
echo check_ban();
//check to see if the board is on or off.
if ($board_status == 0){
	$offline_msg = nl2br($off_msg);
	$error = $offline_msg;
	if ($access_level == 1){
		$error .= "<p class=\"td\">[<a href=\"acp/index.php\">$menu[cp]</a>]</p>";
	}else{
		$error .= "<p class=\"td\">[<a href=\"login.php\">$txt[login]</a>]</p>"; 
	}
	echo error($error, "general");
	#terminate program after message appears.
	exit();
}

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
	$page = new template($template_path ."/top-admin.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-CP" => "$menu[cp]",
	"LANG-NEWPOSTS" => "$index[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-CLOSE" => "$txt[closewindow]",
	"LANG-QUICKSEARCH" => "$search[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$search[advsearch]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",
	"LANG-PROFILE" => "$menu[profile]"));
	$page->output();
	//update user's activity.
	echo update_whosonline_reg($logged_user);
}
if (($stat == "Member") OR ($access_level == 2) OR ($access_level == 3)){
	$page = new template($template_path ."/top-logged.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-NEWPOSTS" => "$index[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-CLOSE" => "$txt[closewindow]",
	"LANG-QUICKSEARCH" => "$search[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$search[advsearch]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",
	"LANG-PROFILE" => "$menu[profile]"));
	$page->output();
	//update user's activity.
	echo update_whosonline_reg($logged_user);
}
if ($stat == "guest"){
	$page = new template($template_path ."/top-guest.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcomeguest]",
	"LANG-LOGIN" => "$txt[login]",
	"LANG-REGISTER" => "$txt[register]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",));
	$page->output();
	//update guest's activity.
	echo update_whosonline_guest();
}
//display search
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
#call board setting function.
$colume = 'per_page';
$settings = board_settings($colume);

switch ($action){
case 'user_result';
	//get query text to perform search.
	$search_type = var_cleanup($_GET['search_type']);
	$poster = var_cleanup($_GET['poster']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//flood check.
	$flood = flood_check($logged_user, "search");
	if ($flood == 1){
		$errormsg = $search['flood']."\n\n";
		$error = 1;
	}
	#update last_search colume.
	$time = time();
	$db->run = "update ebb_users SET last_search='$time' where Username='$logged_user'";
	$db->query();
	$db->close();
	//see if user added too many characters in query.
	if (strlen($poster) > 50){
		$errormsg .= $search['toolong']."\n\n";
		$error = 1;
	}
	if (($poster == "") and ($search_type == "")){
		$errormsg .= $search['nokeyword']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//get results
		if ($search_type == "topic"){
			//start pagenation.
			$count = 0;
			$count2 = 0;
			if(!isset($_GET['pg'])){
				$pg = 1;
			}else{
				$pg = var_cleanup($_GET['pg']);
			}
			// Figure out the limit for the query based on the current page number.
			$from = (($pg * $settings['per_page']) - $settings['per_page']);

			$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE author LIKE '$poster' LIMIT $from, $settings[per_page]";
			$search_result = $db->query();
			$db->close();

			$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE author LIKE '$poster'";
			$num = $db->num_results();
			$db->close();
			#output pagination.
			$pagenation = pagination("action=user_result&amp;search_type=$search_type&amp;poster=$poster&amp;");
			#see if theres no results.
			if ($num == 0){
				$error = $search['noresults'];
				echo error($error, "general");
			}else{
				$searchresults = search_results_topic();
			}
		}
		if ($search_type == "post"){
			//start pagenation.
			$count = 0;
			$count2 = 0;

			if(!isset($_GET['pg'])){
			    $pg = 1;
			}else{
			    $pg = var_cleanup($_GET['pg']);
			}
			// Figure out the limit for the query based on the current page number.
			$from = (($pg * $settings['per_page']) - $settings['per_page']);
			// Figure out the total number of results in DB:
			$db->run = "SELECT author, bid, tid, pid FROM ebb_posts WHERE author LIKE '$poster' LIMIT $from, $settings[per_page]";
			$search_result = $db->query();
			$db->close();

			$db->run = "SELECT author, bid, tid, pid FROM ebb_posts WHERE author LIKE '$poster'";
			$num = $db->num_results();
			$db->close();
			#output pagination.
			$pagenation = pagination("action=user_result&amp;search_type=$search_type&amp;poster=$poster&amp;");
			#see if keyword catches anything.
			if ($num == 0){
				$error = $search['noresults'];
				echo error($error, "general");
			}else{
				$searchresults = search_results_post();
			}
		}
	}
break;
case 'result';
	//get query text to perform search.
	$search_type = var_cleanup($_POST['search_type']);
	$keyword = var_cleanup($_POST['keyword']);
	$poster = var_cleanup($_POST['poster']);
	$board = var_cleanup($_POST['board']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//flood check.
	$flood = flood_check($logged_user, "search");
	if ($flood == 1){
		$errormsg = $search['flood']."\n\n";
		$error = 1;
	}
	#update last_search colume.
	$time = time();
	$db->run = "update ebb_users SET last_search='$time' where Username='$logged_user'";
	$db->query();
	$db->close();
	//see if user added too many characters in query.
	if ((strlen($keyword) > 50) or (strlen($poster) > 25)){
		$errormsg .= $search['toolong']."\n\n";
		$error = 1;
	}
	if ((empty($keyword)) or (empty($search_type))){
		$errormsg .= $search['nokeyword']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//get results
		if ($search_type == "topic"){
			//start pagenation.
			$count = 0;
			$count2 = 0;
			if(!isset($_GET['pg'])){
			    $pg = 1;
			}else{
			    $pg = var_cleanup($_GET['pg']);
			}
			// Figure out the limit for the query based on the current page number.
			$from = (($pg * $settings['per_page']) - $settings['per_page']);
			// Figure out the total number of results in DB:
			$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE author LIKE '$poster' OR Topic LIKE '$keyword' OR Body LIKE '%$keyword%' OR bid LIKE '$board' LIMIT $from, $settings[per_page]";
			$search_result = $db->query();
			$db->close();

			$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE author LIKE '$poster' OR Topic LIKE '$keyword' OR Body LIKE '%$keyword%' OR bid LIKE '$board'";
			$num = $db->num_results();
			$db->close();
			#output pagination.
			$pagenation = pagination("action=result&amp;");
			#see if keyword catches anything.
			if ($num == 0){
				$error = $search['noresults'];
				echo error($error, "general");
			}else{
				$searchresults = search_results_topic();
			}
		}elseif ($search_type == "post"){
			//start pagenation.
			$count = 0;
			$count2 = 0;

			if(!isset($_GET['pg'])){
			    $pg = 1;
			}else{
			    $pg = var_cleanup($_GET['pg']);
			}
			// Figure out the limit for the query based on the current page number.
			$from = (($pg * $settings['per_page']) - $settings['per_page']);

			$db->run = "SELECT author, bid, tid, pid FROM ebb_posts WHERE author LIKE '$poster' OR Body LIKE '%$keyword%' OR bid LIKE '$board' LIMIT $from, $settings[per_page]";
			$search_result = $db->query();
			$db->close();

			$db->run = "SELECT author, bid, tid, pid FROM ebb_posts WHERE author LIKE '$poster' OR Body LIKE '%$keyword%' OR bid LIKE '$board'";
			$num = $db->num_results();
			$db->close();
			#output pagination.
			$pagenation = pagination("action=result&amp;");
			#see if keyword catches anything.
			if ($num == 0){
				$error = $search['noresults'];
				echo error($error, "general");
			}else{
				$searchresults = search_results_post();
			}
		}else{
			$errormsg .= $search['nokeyword']."\n\n";
			$error = 1; 
		}
	}
break;
case 'newposts':
	//flood check.
	$flood = flood_check($logged_user, "search");
	if ($flood == 1){
		$error = $search['flood'];
		echo error($error, "error");
	}
	#update last_search colume.
	$time = time();
	$db->run = "update ebb_users SET last_search='$time' where Username='$logged_user'";
	$db->query();
	$db->close();
	//find topics
	$db->run = "SELECT Topic, author, bid, tid FROM ebb_topics WHERE Original_Date<='$last_visit' or last_update='$last_visit'";
	$search_result = $db->query(); //search query for counter.
	$search_results = $db->query(); //search query for loop.
	$db->close();
	//find posts
	$db->run = "SELECT author, bid, tid, pid FROM ebb_posts WHERE Original_Date<='$last_visit'";
	$search_result2 = $db->query(); //search query for counter.
	$search_results2 = $db->query(); //search query for loop.
	$db->close();
	#get total count.
	$count = newpost_counter();
	#get results.
	if($count == 0){
		$error = $search['noresults'];
		echo error($error, "general"); 
	}else{
		$searchresults = search_results_newposts();
	}
break;
default:
	$boardlist = board_select();
	$page = new template($template_path ."/search.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$menu[search]",
	"LANG-NOKEYWORD" => "$search[nokeyword]",
	"LANG-LONGKEYWORD" => "$search[toolong]",
	"LANG-LONGUSER" => "$search[toolong]",
	"LANG-NOTYPE" => "$search[notype]",
	"LANG-TEXT" => "$search[text]",
	"LANG-KEYWORD" => "$search[keyword]",
	"LANG-USERNAME" => "$search[author]",
	"LANG-SELBOARD" => "$search[selboard]",
	"BOARDLIST" => "$boardlist",
	"LANG-TEXT2" => "$search[text2]",
	"LANG-TOPIC" => "$index[topics]",
	"LANG-POST" => "$index[posts]",
	"LANG-SEARCH" => "$menu[search]"));
	$page->output();
}
//display footer
$page = new template($template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
